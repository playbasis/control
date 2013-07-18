using pblib.NET;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Diagnostics;
using Microsoft.SharePoint.Client.EventReceivers;
using Microsoft.SharePoint.Client;
using System.Net;

namespace PlaybasisWeb
{
	public sealed class PlaybasisHelper
	{
		public static readonly int AUTH_INTERVAL_HOUR = 24;
		private static readonly string API_KEY = "YOUR_API_KEY";
		private static readonly string API_SECRET = "YOUR_API_SECRET";
		private static readonly string PLAYER_ID_PREFIX = "spusr_";
		private static readonly string DEFAULT_PROFILE_IMAGE = "https://www.pbapp.net/images/default_profile.jpg";
		private static readonly string DEFAULT_EMAIL_DOMAIN = "@email.com";

		private static PlaybasisHelper instance = new PlaybasisHelper();

		public static PlaybasisHelper Instance
		{
			get { return instance; }
		}

		public Playbasis pb;
		private DateTime authTime;

		private PlaybasisHelper()
		{
			pb = new Playbasis();
			authTime = new DateTime(1970,1,1, 0,0,0);
		}

		public void Auth(bool forced = false)
		{
			var diff = DateTime.Now - authTime;
			if (forced || (diff.TotalHours > AUTH_INTERVAL_HOUR))
			{
				bool result = pb.auth(API_KEY, API_SECRET);
				TraceHelper.RemoteLog(result ? "auth success" : "auth failed");
				authTime = DateTime.Now;
			}
		}

		public string Register(int userId, SPRemoteEventProperties properties, bool async)
		{
			using (ClientContext clientContext = TokenHelper.CreateRemoteEventReceiverClientContext(properties))
			{
				if (clientContext != null)
				{
					clientContext.Load(clientContext.Web);
					clientContext.Load(clientContext.Web.SiteUsers);
					clientContext.ExecuteQuery();

					var user = clientContext.Web.SiteUsers.GetById(userId);
					clientContext.Load(user);
					clientContext.ExecuteQuery();

					return Register(user, async);
				}
			}
			return null;
		}

		public string Register(User user, bool async)
		{
			var id = PLAYER_ID_PREFIX + user.Id.ToString();
			var username = user.Title;
			if (string.IsNullOrWhiteSpace(username))
				username = id;
			var email = user.Email;
			if (string.IsNullOrWhiteSpace(email))
				email = id + DEFAULT_EMAIL_DOMAIN;

			if (!async)
			{
				var result = pb.register(id, username, email, DEFAULT_PROFILE_IMAGE);
				TraceHelper.RemoteLog(result);
				return result;
			}
			pb.register_async(id, username, email, DEFAULT_PROFILE_IMAGE, printHandler);
			return null;
		}

		public void TriggerAction(int userId, string action, bool tryAuth = true, params string[] optionalData)
		{
			Debug.Assert(!string.IsNullOrWhiteSpace(action));
			TraceHelper.RemoteLog("user " + userId.ToString() + " trigger action " + action);
			if (tryAuth)
				Auth();
			pb.rule_async(PLAYER_ID_PREFIX + userId.ToString(), action, printHandler, optionalData);
		}

		public void TriggerAction(int userId, SPRemoteEventProperties properties, string action, bool tryAuth = true, params string[] optionalData)
		{
			if (tryAuth)
				Auth();
			Register(userId, properties, false); //make sure the user is registered
			TriggerAction(userId, action, false, optionalData);
		}

		public static void printHandler(Object sender, UploadStringCompletedEventArgs e)
		{
			TraceHelper.RemoteLog(e.Result);
		}
	}
}