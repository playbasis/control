using Microsoft.SharePoint.Client.Utilities;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;

namespace PlaybasisWeb
{
	public class TraceHelper
	{
		public static void RemoteLog(string message)
		{
			const string ECHO_URL = "https://node.pbapp.net:3004/echo/msg/";
			WebClient client = new WebClient();
			client.DownloadStringAsync(new Uri(ECHO_URL + HttpUtility.UrlPathEncode(message, true, true, true)));
		}

		public static string RemoteLogSync(string message)
		{
			const string ECHO_URL = "https://node.pbapp.net:3004/echo/msg/";
			WebClient client = new WebClient();
			return client.DownloadString(new Uri(ECHO_URL + HttpUtility.UrlPathEncode(message, true, true, true)));
		}
	}
}