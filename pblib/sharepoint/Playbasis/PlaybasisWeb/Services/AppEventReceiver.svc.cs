using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Microsoft.SharePoint.Client;
using Microsoft.SharePoint.Client.EventReceivers;
using System.ServiceModel.Channels;
using System.ServiceModel;
using System.Net;

namespace PlaybasisWeb.Services
{
	public class AppEventReceiver : IRemoteEventService
	{
		public SPRemoteEventResult ProcessEvent(SPRemoteEventProperties properties)
		{
			TraceHelper.RemoteLog("app event triggered: " + properties.EventType.ToString());

			SPRemoteEventResult result = new SPRemoteEventResult();

			using (ClientContext clientContext = TokenHelper.CreateAppEventClientContext(properties, false))
			{
				if (clientContext != null)
				{
					clientContext.Load(clientContext.Web);
					clientContext.Load(clientContext.Web.SiteUsers);
					clientContext.ExecuteQuery();

					RegisterEventReceivers(clientContext);

					PlaybasisHelper.Instance.Auth();
					foreach (var user in clientContext.Web.SiteUsers)
					{
						if (string.IsNullOrWhiteSpace(user.Email))
							continue;
						PlaybasisHelper.Instance.Register(user, true);
					}
				}
			}

			return result;
		}

		public void ProcessOneWayEvent(SPRemoteEventProperties properties)
		{
			// This method is not used by app events
		}

		private void RegisterEventReceivers(ClientContext clientContext)
		{
			AddEventReceiverToList(clientContext, "Announcements", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Announcements", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Announcements", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Discussion", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Discussion", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Discussion", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Issues", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Issues", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Issues", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Survey", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Survey", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Survey", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Tasks", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Tasks", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Tasks", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Contacts", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Contacts", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Contacts", EventReceiverType.ItemUpdated);

			AddEventReceiverToList(clientContext, "Calendar", EventReceiverType.ItemAdded);
			AddEventReceiverToList(clientContext, "Calendar", EventReceiverType.ItemDeleted);
			AddEventReceiverToList(clientContext, "Calendar", EventReceiverType.ItemUpdated);
		}

		private static void AddEventReceiverToList(ClientContext clientContext, string listTitle, EventReceiverType eventType)
		{
			var list = clientContext.Web.Lists.GetByTitle(listTitle);
			var requestProperty = (HttpRequestMessageProperty)OperationContext.Current.IncomingMessageProperties[HttpRequestMessageProperty.Name];
			var appWebUrl = "https://" + requestProperty.Headers[HttpRequestHeader.Host];
			var receiverName = listTitle + "_" + eventType.ToString();
			var eventReceiver = new EventReceiverDefinitionCreationInformation();
			eventReceiver.EventType = eventType;
			eventReceiver.ReceiverName = receiverName;
			eventReceiver.ReceiverClass = receiverName;
			eventReceiver.ReceiverUrl = appWebUrl + "/Services/" + receiverName + ".svc";
			eventReceiver.SequenceNumber = 10000;
			list.EventReceivers.Add(eventReceiver);
			clientContext.ExecuteQuery();
			TraceHelper.RemoteLog("RER attached: " + receiverName);
		}
	}
}
