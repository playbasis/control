using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Microsoft.SharePoint.Client;
using Microsoft.SharePoint.Client.EventReceivers;

namespace PlaybasisWeb.Services
{
	public class Issues_ItemDeleted : IRemoteEventService
	{
		public SPRemoteEventResult ProcessEvent(SPRemoteEventProperties properties)
		{
			SPRemoteEventResult result = new SPRemoteEventResult();

			using (ClientContext clientContext = TokenHelper.CreateRemoteEventReceiverClientContext(properties))
			{
				if (clientContext != null)
				{
					clientContext.Load(clientContext.Web);
					clientContext.ExecuteQuery();
				}
			}

			return result;
		}

		public void ProcessOneWayEvent(SPRemoteEventProperties properties)
		{
			// the line below will trigger action for the current user, 
			PlaybasisHelper.Instance.TriggerAction(properties.ItemEventProperties.CurrentUserId, properties, "ACTION_NAME");
		}
	}
}
