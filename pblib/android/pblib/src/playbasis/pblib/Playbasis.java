package playbasis.pblib;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;

import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

/**
 * The Playbasis Object
 * @author eddie.playbasis
 */
public class Playbasis
{
	public static Playbasis instance;
	
	private static final String BASE_URL = "https://api.pbapp.net/";
	private static final String CONTENT_TYPE = "application/x-www-form-urlencoded";
	private static final String CHARSET = "UTF-8";
	
	private String token;
	
	public Playbasis()
	{
		assert instance == null;
		instance = this;
	}
	
	public String auth(String apiKey, String apiSecret)
	{
		String param = "";
		try
		{
			param = "api_key=" + URLEncoder.encode(apiKey, "UTF-8") + 
					"&api_secret=" + URLEncoder.encode(apiSecret, "UTF-8");	
		}
		catch (UnsupportedEncodingException e)
		{
			return null;
		}
		String result = call("Auth", param);
		token = result;
		return token;
	}
	
	public String call(String method, String data)
	{
		try
		{
			return MakeRequest(new URL(BASE_URL + method), data);
		}
		catch (MalformedURLException e)
		{
			return null;
		}
	}
	
	private static String MakeRequest(URL url, String data)
	{
		try
		{
			//opening http or https connection
			HttpURLConnection http = null;
			if (url.getProtocol().contains("https") || url.getProtocol().contains("HTTPS"))
			{
				trustAllHosts();
				HttpsURLConnection https = (HttpsURLConnection) url.openConnection();
				https.setHostnameVerifier(DO_NOT_VERIFY);
				http = https;
			}
			else //regular http connection
			{
				http = (HttpURLConnection) url.openConnection();
			}

			http.setRequestMethod("POST");
			
			//set method to post if we have data to send
			if (data != null)
			{
				http.setDoOutput(true);
				http.setUseCaches(false);
				http.setRequestProperty("Content-Type", CONTENT_TYPE);
				http.setRequestProperty("charset", CHARSET);
				http.setFixedLengthStreamingMode(data.getBytes().length); //http.setChunkedStreamingMode(0);
				
				//write data
				BufferedWriter out = new BufferedWriter(new OutputStreamWriter(http.getOutputStream()));
				out.write(data);
				out.flush();
			}
			
			//get the response string
			InputStream in = http.getInputStream();
			assert in != null;
			BufferedReader rd = new BufferedReader(new InputStreamReader(in));
			StringBuffer sb = new StringBuffer();
			String line;
			while ((line = rd.readLine()) != null)
				sb.append(line);
			rd.close();
			
			http.disconnect();
			return sb.toString();
		}
		catch (IOException e)
		{
			return null;
		}
	}

	////////////////////////////////////////
	// Below:
	// code accept all host, don't check for any certificate.
	// from:
	// http://stackoverflow.com/questions/995514/https-connection-android/1000205#1000205
	////////////////////////////////////////

	/**
	 * always verify the host - don't check for certificate
	 */
	final static HostnameVerifier DO_NOT_VERIFY = new HostnameVerifier()
	{
		public boolean verify(String hostname, SSLSession session)
		{
			return true;
		}
	};

	/**
	 * Trust every server - don't check for any certificate
	 */
	private static void trustAllHosts()
	{
		// Create a trust manager that does not validate certificate chains
		TrustManager[] trustAllCerts = new TrustManager[]
		{ 
			new X509TrustManager()
			{
				public java.security.cert.X509Certificate[] getAcceptedIssuers()
				{
					return new java.security.cert.X509Certificate[] {};
				}
	
				public void checkClientTrusted(X509Certificate[] chain,	String authType) throws CertificateException
				{
				}
	
				public void checkServerTrusted(X509Certificate[] chain,	String authType) throws CertificateException
				{
				}
			}
		};

		try // Install the all-trusting trust manager
		{
			SSLContext sc = SSLContext.getInstance("TLS");
			sc.init(null, trustAllCerts, new java.security.SecureRandom());
			HttpsURLConnection.setDefaultSSLSocketFactory(sc.getSocketFactory());
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}