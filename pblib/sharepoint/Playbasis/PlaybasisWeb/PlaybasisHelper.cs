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
		private static readonly string API_KEY = "abc";
		private static readonly string API_SECRET = "abcde";
		private static readonly string PLAYER_ID_PREFIX = "sp";
		private static readonly string DEFAULT_PROFILE_IMAGE = "https://www.pbapp.net/images/default_profile.jpg";

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

		public void Register(int userId)
		{
			var id = PLAYER_ID_PREFIX + userId.ToString();
			var result = pb.register(id, id, id + "@mail.com", DEFAULT_PROFILE_IMAGE);
			TraceHelper.RemoteLog(result);
		}

		public void TriggerAction(int userId, string action, params string[] optionalData)
		{
			Debug.Assert(!string.IsNullOrWhiteSpace(action));
			pb.rule_async(PLAYER_ID_PREFIX + userId.ToString(), action, printHandler, optionalData);
		}

		public void TriggerAction(SPRemoteEventProperties properties, string action, params string[] optionalData)
		{
			var userId = properties.ItemEventProperties.CurrentUserId;
			TraceHelper.RemoteLog("user " + userId.ToString() + " trigger action " + action);
			PlaybasisHelper.Instance.Auth();
			PlaybasisHelper.Instance.Register(userId);
			PlaybasisHelper.Instance.TriggerAction(userId, action, optionalData);
		}

		public static void printHandler(Object sender, UploadStringCompletedEventArgs e)
		{
			TraceHelper.RemoteLog(e.Result);
		}
	}
}