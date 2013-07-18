using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Newtonsoft.Json;
using System.Net;
using System.Diagnostics;

namespace pblib.NET
{
	public class Playbasis
	{
		private static readonly string BASE_URL = "https://api.pbapp.net/";
		private string token;
		private string apiKeyParam;

		public bool auth(string apiKey, string apiSecret)
		{
			apiKeyParam = "?api_key=" + apiKey;
			var param = "api_key=" + apiKey + "&api_secret=" + apiSecret;
			dynamic result = JsonToDynamic(call("Auth", param));
			if ((bool)result.success)
			{
				token = result.response.token;
				Debug.Assert(!string.IsNullOrEmpty(token));
				return true;
			}
			return false;
		}

		public string player(string playerId)
		{
			return call("Player/" + playerId, "token=" + token);
		}

		/// <summary>
		/// Register a new user
		/// </summary>
		/// <param name="playerId"></param>
		/// <param name="username"></param>
		/// <param name="email"></param>
		/// <param name="imageUrl"></param>
		/// <param name="optionalData"> Varargs of String for additional parameters to be sent to the register method.
		/// Each element is a string in the format of key=value, for example: first_name=john
		/// The following keys are supported:
		/// - facebook_id
		/// - twitter_id
		/// - password		assumed hashed
		/// - first_name
		/// - last_name
		/// - nickname
		/// - gender		1=Male, 2=Female
		/// - birth_date	format YYYY-MM-DD</param>
		/// <returns></returns>
		public string register(string playerId, string username, string email, string imageUrl, params string[] optionalData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);
			param.Append("&username=");
			param.Append(username);
			param.Append("&email=");
			param.Append(email);
			param.Append("&image=");
			param.Append(imageUrl);

			for (int i = 0; i < optionalData.Length; ++i)
				param.Append("&" + optionalData[i]);

			return call("Player/" + playerId + "/register", param.ToString());
		}
		public void register_async(string playerId, string username, string email, string imageUrl, UploadStringCompletedEventHandler onComplete = null, params string[] optionalData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);
			param.Append("&username=");
			param.Append(username);
			param.Append("&email=");
			param.Append(email);
			param.Append("&image=");
			param.Append(imageUrl);

			for (int i = 0; i < optionalData.Length; ++i)
				param.Append("&" + optionalData[i]);

			call_async("Player/" + playerId + "/register", param.ToString(), onComplete);
		}

		/// <summary>
		/// Update user data
		/// </summary>
		/// <param name="playerId"></param>
		/// <param name="updateData"> Varargs of String data to be updated.
		/// Each element is a string in the format of key=value, for example: first_name=john
		/// The following keys are supported:
		/// - username
		/// - email
		/// - image
		/// - exp
		/// - level
		/// - facebook_id
		/// - twitter_id
		/// - password		assumed hashed
		/// - first_name
		/// - last_name
		/// - nickname
		/// - gender		1=Male, 2=Female
		/// - birth_date	format YYYY-MM-DD</param>
		/// <returns></returns>
		public string update(string playerId, params string[] updateData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);

			for (int i = 0; i < updateData.Length; ++i)
				param.Append("&" + updateData[i]);

			return call("Player/" + playerId + "/update", param.ToString());
		}
		public void update_async(string playerId, UploadStringCompletedEventHandler onComplete = null, params string[] updateData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);

			for (int i = 0; i < updateData.Length; ++i)
				param.Append("&" + updateData[i]);

			call_async("Player/" + playerId + "/update", param.ToString(), onComplete);
		}

		public string delete(string playerId)
		{
			return call("Player/" + playerId + "/delete", "token=" + token);
		}
		public void delete_async(string playerId, UploadStringCompletedEventHandler onComplete = null)
		{
			call_async("Player/" + playerId + "/delete", "token=" + token, onComplete);
		}

		public string login(string playerId)
		{
			return call("Player/" + playerId + "/login", "token=" + token);
		}
		public void login_async(string playerId, UploadStringCompletedEventHandler onComplete = null)
		{
			call_async("Player/" + playerId + "/login", "token=" + token, onComplete);
		}

		public string logout(string playerId)
		{
			return call("Player/" + playerId + "/logout", "token=" + token);
		}
		public void logout_async(string playerId, UploadStringCompletedEventHandler onComplete = null)
		{
			call_async("Player/" + playerId + "/logout", "token=" + token, onComplete);
		}

		public string points(string playerId)
		{
			return call("Player/" + playerId + "/points" + apiKeyParam);
		}

		public string point(string playerId, string pointName)
		{
			return call("Player/" + playerId + "/point/" + pointName + apiKeyParam);
		}

		public string actionLastPerformed(string playerId)
		{
			return call("Player/" + playerId + "/action/time" + apiKeyParam);
		}

		public string actionLastPerformedTime(string playerId, string actionName)
		{
			return call("Player/" + playerId + "/action/" + actionName + "/time" + apiKeyParam);
		}

		public string actionPerformedCount(string playerId, string actionName)
		{
			return call("Player/" + playerId + "/action/" + actionName + "/count" + apiKeyParam);
		}

		public string badgeOwned(string playerId)
		{
			return call("Player/" + playerId + "/badge" + apiKeyParam);
		}

		public string rank(string rankedBy, int limit)
		{
			return call("Player/rank/" + rankedBy + "/" + limit.ToString() + apiKeyParam);
		}

		public string ranks(int limit)
		{
			return call("Player/ranks/" + limit.ToString() + apiKeyParam);
		}

		public string badges()
		{
			return call("Badge" + apiKeyParam);
		}

		public string badge(string badgeId)
		{
			return call("Badge/" + badgeId + apiKeyParam);
		}

		public string badgeCollections()
		{
			return call("Badge/collection" + apiKeyParam);
		}

		public string badgeCollection(string collectionId)
		{
			return call("Badge/collection/" + collectionId + apiKeyParam);
		}

		public string actionConfig()
		{
			return call("Engine/actionConfig" + apiKeyParam);
		}

		/// <summary>
		/// Trigger an action and process related rules for the specified user
		/// </summary>
		/// <param name="playerId"></param>
		/// <param name="action"></param>
		/// <param name="optionalData">Varargs of String for additional parameters to be sent to the rule method.
		/// Each element is a string in the format of key=value, for example: url=playbasis.com
		/// The following keys are supported:
		/// - url		url or filter string (for triggering non-global actions)
		/// - reward	name of the custom-point reward to give (for triggering rules with custom-point reward)
		/// - quantity	amount of points to give (for triggering rules with custom-point reward)</param>
		/// <returns></returns>
		public string rule(string playerId, string action, params string[] optionalData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);
			param.Append("&player_id=");
			param.Append(playerId);
			param.Append("&action=");
			param.Append(action);

			for (int i = 0; i < optionalData.Length; ++i)
				param.Append("&" + optionalData[i]);

			return call("Engine/rule", param.ToString());
		}
		public void rule_async(string playerId, string action, UploadStringCompletedEventHandler onComplete, params string[] optionalData)
		{
			var param = new StringBuilder();
			param.Append("token=");
			param.Append(token);
			param.Append("&player_id=");
			param.Append(playerId);
			param.Append("&action=");
			param.Append(action);

			for (int i = 0; i < optionalData.Length; ++i)
				param.Append("&" + optionalData[i]);

			call_async("Engine/rule", param.ToString(), onComplete);
		}

		public static string call(string address, string data = null)
		{
			Console.WriteLine("making request to: " + address);

			WebClient client = new WebClient();
			if (!string.IsNullOrEmpty(data))
			{
				client.Headers[HttpRequestHeader.ContentType] = "application/x-www-form-urlencoded";
				return client.UploadString(BASE_URL + address, data);
			}
			return client.DownloadString(BASE_URL + address);
		}

		public static void call_async(string address, string data, UploadStringCompletedEventHandler onComplete)
		{
			Console.WriteLine("making async request to: " + address);

			WebClient client = new WebClient();
			Debug.Assert(!string.IsNullOrEmpty(data));			
			client.Headers[HttpRequestHeader.ContentType] = "application/x-www-form-urlencoded";
			if(onComplete != null)
				client.UploadStringCompleted += onComplete;
			client.UploadStringAsync(new Uri(BASE_URL + address), data);
		}

		public static dynamic JsonToDynamic(string json)
		{
			return JsonConvert.DeserializeObject<dynamic>(json);
		}

		public static T JsonToObject<T>(string json)
		{
			return JsonConvert.DeserializeObject<T>(json);
		}
	}
}
