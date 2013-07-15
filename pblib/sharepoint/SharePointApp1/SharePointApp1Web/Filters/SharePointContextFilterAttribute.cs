using System;
using System.Web;
using System.Web.Mvc;

namespace SharePointApp1Web
{
    /// <summary>
    /// SharePoint action filter attribute.
    /// </summary>
    public class SharePointContextFilterAttribute : ActionFilterAttribute
    {
        public const string SPHasRedirectedToSharePointKey = "SPHasRedirectedToSharePoint";

        public override void OnActionExecuting(ActionExecutingContext filterContext)
        {
            if (filterContext == null)
            {
                throw new ArgumentNullException("filterContext");
            }

            if (SharePointContextProvider.Current.GetSharePointContext(filterContext.HttpContext) != null)
            {
                // SharePointContext is found, return.
                return;
            }

            Uri spHostUrl = SharePointContext.GetSPHostUrl(filterContext.HttpContext.Request);

            if (spHostUrl != null && !HasRedirectedToSharePoint(filterContext.HttpContext.Request))
            {
                // The HTTP request contains the SharePoint host url. Redirect to SharePoint to obtain necessary information such as context token.
                RedirectToSharePoint(filterContext, spHostUrl);
            }
            else
            {
                // There is not enough information to determine the SharePoint site associated with the HTTP request,
                // or the request was previously redirected to SharePoint. Show the error view.
                ShowErrorView(filterContext);
            }
        }

        /// <summary>
        /// Determines if the specified HTTP request has been redirected to SharePoint before by checking the existence of SPHasRedirectedToSharePoint in the query string.
        /// </summary>
        /// <param name="httpRequest">The HTTP request.</param>
        /// <returns>True if the request has been redirected to SharePoint before.</returns>
        protected virtual bool HasRedirectedToSharePoint(HttpRequestBase httpRequest)
        {
            return !string.IsNullOrEmpty(httpRequest.QueryString[SPHasRedirectedToSharePointKey]);
        }

        /// <summary>
        /// Redirects to the specified SharePoint host url for user to login.
        /// </summary>
        /// <param name="filterContext">The filter context.</param>
        /// <param name="spHostUrl">The SharePoint host url.</param>
        protected virtual void RedirectToSharePoint(ActionExecutingContext filterContext, Uri spHostUrl)
        {
            const string StandardTokens = "{StandardTokens}";

            Uri requestUrl = filterContext.HttpContext.Request.Url;

            var queryNameValueCollection = HttpUtility.ParseQueryString(requestUrl.Query);

            // Removes SPHostUrl if it exists, as {StandardTokens} will be inserted at the beginning of the query string, which contains the SPHostUrl.
            queryNameValueCollection.Remove(SharePointContext.SPHostUrlKey);

            // Adds SPHasRedirectedToSharePoint=1.
            queryNameValueCollection.Add(SPHasRedirectedToSharePointKey, "1");

            UriBuilder returnUrlBuilder = new UriBuilder(requestUrl);
            returnUrlBuilder.Query = queryNameValueCollection.ToString();

            // Inserts StandardTokens.
            string returnUrlString = returnUrlBuilder.Uri.AbsoluteUri;
            returnUrlString = returnUrlString.Insert(returnUrlString.IndexOf("?") + 1, StandardTokens + "&");

            // Constructs redirect url.
            string redirectUrlString = TokenHelper.GetAppContextTokenRequestUrl(spHostUrl.AbsoluteUri, Uri.EscapeDataString(returnUrlString));

            filterContext.Result = new RedirectResult(redirectUrlString);
        }

        /// <summary>
        /// Shows error view.
        /// </summary>
        /// <param name="filterContext">The filter context.</param>
        protected virtual void ShowErrorView(ActionExecutingContext filterContext)
        {
            filterContext.Result = new ViewResult { ViewName = "Error" };
        }
    }
}
