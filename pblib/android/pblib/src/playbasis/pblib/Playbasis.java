package playbasis.pblib;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.StringReader;
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

import android.util.JsonReader;
import android.util.JsonToken;

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
	
	public boolean auth(String apiKey, String apiSecret)
	{
		String param = "";
		try
		{
			param = "api_key=" + URLEncoder.encode(apiKey, "UTF-8") + 
					"&api_secret=" + URLEncoder.encode(apiSecret, "UTF-8");	
		}
		catch (UnsupportedEncodingException e)
		{
			return false;
		}
		try
		{
			JsonReader reader = callJSON("Auth", param);
			while(true)
			{
				JsonToken nextToken = reader.peek();
				if(nextToken == JsonToken.BEGIN_OBJECT)
					reader.beginObject();
				else if(nextToken == JsonToken.BEGIN_ARRAY)
					reader.beginArray();
				else if(nextToken == JsonToken.END_DOCUMENT)
				{
					reader.close();
					return false;
				}
				else if(nextToken == JsonToken.NAME)
				{
					String name = reader.nextName();
					if(name.equals("token"))
					{
						token = reader.nextString();
						reader.close();
						return true;
					}
				}
				else
					reader.skipValue();
			}
		}
		catch (IOException e)
		{
			return false;
		}
	}
	
	public JsonReader player(String playerId)
	{
		return callJSON("Player/"+playerId, "token="+token);
	}
	
	/*
	 * @param	optionalData	Varargs of String for additional parameters to be sent to the register method.
	 * 							Each element is a string in the format of key=value, for example: first_name=john  
	 */
	public JsonReader register(String playerId, String username, String email, String imageUrl, String... optionalData)
	{
		StringBuilder param = new StringBuilder();
		try
		{
			param.append("token=");
			param.append(token);
			param.append("&username=");
			param.append(URLEncoder.encode(username, "UTF-8"));
			param.append("&email=");
			param.append(URLEncoder.encode(email, "UTF-8"));
			param.append("&image=");
			param.append(URLEncoder.encode(imageUrl, "UTF-8"));
			
			for(int i=0; i<optionalData.length; ++i)
				param.append("&"+optionalData[i]);
		}
		catch (UnsupportedEncodingException e)
		{
			return null;
		}
		return callJSON("Player/"+playerId+"/register", param.toString());
	}
	
	public static String call(String method, String data)
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
	
	public static JsonReader callJSON(String method, String data)
	{
		return new JsonReader(new StringReader(call(method, data)));
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