package com.playbasis.pblib;

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
import java.util.Map;

import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;

/**
 * The Playbasis Object
 * @author Sylvain Dormieu
 */
public class Playbasis
{
	public static Playbasis instance;
	
	public static final String BASE_URL = "https://api.pbapp.net/";
	
	public static final String CONTENT_TYPE = "application/x-www-form-urlencoded";
	public static final String CHARSET = "UTF-8";
	
	private String token;
	private String dateExpire;
	private String apiKeyParam;
	
	public Playbasis()
	{
		assert instance == null;
		instance = this;
	}
	/**
	 * check if Auth was made or not
	 */
	public String getToken(){
		return token;
	}
	public boolean isAuth(){
		return (token != null);
	}
	/**
	 * Authentication procedure on playbasis
	 * @param apiKey
	 * @param apiSecret
	 * @return true if request is successful
	 */
	public boolean auth(String apiKey, String apiSecret)
	{
		apiKeyParam = "?api_key=" + apiKey;
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
			Request req = getPlaybasisAnswer("Auth", param);
			if(req.isSuccess()){
				Map<String,Object> result = (Map<String,Object>) req.getResponse();
				token = (String) result.get("token"); 
				setDateExpire((String) result.get("date_expire"));
			}
			else{
				token = null;
			}
			if(token != null) return true;
		}
		catch (Exception e)
		{
			e.printStackTrace();
			return false;
		}
		
		return false;
	}
	/**
	 * Renew the authentification
	 * @param apiKey
	 * @param apiSecret
	 * @return true if request is successful
	 */
	public boolean renew(String apiKey, String apiSecret)
	{
		apiKeyParam = "?api_key=" + apiKey;
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
			Request req = getPlaybasisAnswer("Auth/renew", param);
			if(req.isSuccess()){
				Map<String,Object> result = (Map<String,Object>) req.getResponse();
				token = (String) result.get("token"); 
				setDateExpire((String) result.get("date_expire"));
			}
			else{
				token = null;
			}
			if(token != null) return true;
		}
		catch (Exception e)
		{
			e.printStackTrace();
			return false;
		}
		
		return false;
	}
	/**
	 * Get information for a player. Fields include
	 * image
	 * email
	 * username
	 * exp
	 * level
	 * first_name
	 * last_name
	 * gender
	 * birth_date
	 * registered
	 * last_login
	 * last_logout
	 * cl_player_id
	 * @param playerId
	 * @return
	 */
	public Request player(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId, "token="+token);
	}
        /**
	 * Get detailed information for a player.
	 * @param playerId
	 * @return
	 */
	public Request playerDetail(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/data/all", "token="+token);
	}
	/**
	 * Get basic information of players.
	 * @param playerIdList player id as used in client's website separated with ',' example '1,2,3'
	 * @return
	 */
	public Request playerList(String playerIdList)
	{
		StringBuilder param = new StringBuilder();
		param.append("token=");
		param.append(token);
		try
		{
			param.append("&list_player_id=" + URLEncoder.encode(playerIdList, "UTF-8"));	
		}
		catch (UnsupportedEncodingException e)
		{return null;}
		return getPlaybasisAnswer("Player/list", param.toString());
	}
	
	/**
	 * Update data on a player. Fields include
	 * image
	 * email
	 * username
	 * exp
	 * level
	 * first_name
	 * last_name
	 * gender
	 * birth_date
	 * registered
	 * last_login
	 * last_logout
	 * cl_player_id
	 * @param playerId
	 * @param updateData
	 * @return
	 */
	public Request update(String playerId, String... updateData)
	{
		StringBuilder param = new StringBuilder();
		param.append("token=");
		param.append(token);
		for(int i=0; i<updateData.length; ++i)
			param.append("&"+updateData[i]);
		return getPlaybasisAnswer("Player/"+playerId+"/update", param.toString());
	}
	/**
	 * Delete a player
	 * @param playerId
	 * @return
	 */
	public Request delete(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/delete", "token="+token);
	}
	
	/**
	 * Register a new player
	 * @param playerId
	 * @param username
	 * @param email
	 * @param imageUrl
	 * @param optionalData	Varargs of String for additional parameters to be sent to the register method.
	 * 							Each element is a string in the format of key=value, for example: first_name=john
	 * 							The following keys are supported:
	 * 							- facebook_id
	 * 							- twitter_id
	 * 							- password		assumed hashed
	 * 							- first_name
	 * 							- last_name
	 * 							- nickname
	 * 							- gender		1=Male, 2=Female
	 * 							- birth_date	format YYYY-MM-DD
	 * @return
	 */
	public Request register(String playerId, String username, String email, String imageUrl, String... optionalData)
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
		return getPlaybasisAnswer("Player/"+playerId+"/register", param.toString());
	}
	/**
	 * Call login action on server
	 * @param playerId
	 * @return
	 */
	public Request login(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/login", "token="+token);
	}
	/**
	 * Call logout action on server
	 * @param playerId
	 * @return
	 */
	public Request logout(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/logout", "token="+token);
	}
	/**
	 * Returns information about all point-based rewards that a player currently have.
	 * @param playerId
	 * @return
	 */
	public Request points(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/points"+apiKeyParam, null);
	}
	/**
	 * Returns how much of specified the point-based reward a player currently have.
	 * @param playerId
	 * @param pointName
	 * @return
	 */
	public Request point(String playerId, String pointName)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/point/"+pointName+apiKeyParam, null);
	}
	/**
	 * Returns the time and action that a player last performed.
	 * @param playerId
	 * @return
	 */
	public Request actionLastPerformed(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/action/time"+apiKeyParam, null);
	}
	/**
	 * Returns the last time that player performed the specified action.
	 * @param playerId
	 * @param actionName
	 * @return
	 */
	public Request actionLastPerformedTime(String playerId, String actionName)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/action/"+actionName+"/time"+apiKeyParam, null);
	}
	/**
	 * Returns the number of times that a player has performed the specified action.
	 * @param playerId
	 * @param actionName
	 * @return
	 */
	public Request actionPerformedCount(String playerId, String actionName)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/action/"+actionName+"/count"+apiKeyParam, null);
	}
	/**
	 * Returns information about all the goods list that a player has redeem.
	 * @param playerId player id as used in client's website
	 * @return
	 */
	public Request playerGoods(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/goods"+apiKeyParam, null);
	}
	/**
	 * Returns information about the goods with the specified id.
	 * @param goodId
	 * @return
	 */
	public Request goodInfo(String goodId)
	{
		return getPlaybasisAnswer("Goods/"+goodId+apiKeyParam, null);
	}
	/**
	 * Returns information about all available goods for the current site.
	 * @return
	 */
	public Request goodsList()
	{
		return getPlaybasisAnswer("Goods/"+apiKeyParam, null);
	}
	/**
	 * Returns information about all the badges that a player has earned.
	 * @param playerId
	 * @return
	 */
	public Request badgeOwned(String playerId)
	{
		return getPlaybasisAnswer("Player/"+playerId+"/badge"+apiKeyParam, null);
	}
	
	public Request claimBadge(String playerId, String badgeId){
		StringBuilder param = new StringBuilder();
		param.append("token=");
		param.append(token);
		
		return getPlaybasisAnswer("Player/"+playerId+"/badge/"+badgeId+"/claim"+apiKeyParam, param.toString());	
	}
	public Request redeemBadge(String playerId, String badgeId){
		StringBuilder param = new StringBuilder();
		param.append("token=");
		param.append(token);
		
		return getPlaybasisAnswer("Player/"+playerId+"/badge/"+badgeId+"/redeem"+apiKeyParam, param.toString());	
	}
	/**
	 * Returns list of top players according to specified point type.
	 * @param rankedBy
	 * @param limit
	 * @return
	 */
	public Request rank(String rankedBy, int limit)
	{
		return getPlaybasisAnswer("Player/rank/"+rankedBy+"/"+String.valueOf(limit)+apiKeyParam, null);
	}
        /**
	 * Returns list of top players.
	 * @param limit
	 * @return
	 */
	public Request ranks(int limit)
	{
		return getPlaybasisAnswer("Player/ranks/"+String.valueOf(limit)+apiKeyParam, null);
	}
        /**
	 * Returns information about specified level.
	 * @param lv
	 * @return
	 */
	public Request level(int lv)
	{
		return getPlaybasisAnswer("Player/level/"+String.valueOf(lv)+apiKeyParam, null);
	}
        /**
	 * Returns information of all levels.
	 * @return
	 */
	public Request levels()
	{
		return getPlaybasisAnswer("Player/levels/"+apiKeyParam, null);
	}
	/**
	 * Returns information about all available badges for the current site.
	 * @return
	 */
	public Request badges()
	{
		return getPlaybasisAnswer("Badge"+apiKeyParam, null);
	}
	/**
	 * Returns information about the badge with the specified id.
	 * @param badgeId
	 * @return
	 */
	public Request badge(String badgeId)
	{
		return getPlaybasisAnswer("Badge/"+badgeId+apiKeyParam, null);
	}
	/**
	 * Returns names of actions that can trigger game rules within a client’s website.
	 * @return
	 */
	public Request actionConfig()
	{
		return getPlaybasisAnswer("Engine/actionConfig"+apiKeyParam, null);
	}
	
	/*
	 * @param	optionalData	Varargs of String for additional parameters to be sent to the rule method.
	 * 							Each element is a string in the format of key=value, for example: url=playbasis.com
	 * 							The following keys are supported:
	 * 							- url		url or filter string (for triggering non-global actions)
	 * 							- reward	name of the custom-point reward to give (for triggering rules with custom-point reward)
	 * 							- quantity	amount of points to give (for triggering rules with custom-point reward)
	 */
	/**
	 * Process an action through all the game rules defined for a client’s website.
	 * @param playerId	player id as used in client's website
	 * @param action	name of action performed
	 * @param optionalData	Varargs of String for additional parameters to be sent to the rule method.
	 * 							Each element is a string in the format of key=value, for example: url=playbasis.com
	 * 							The following keys are supported:
	 * 							- url		URL of the page that trigger the action or any identifier string - Used for logging, URL specific rules, and rules that trigger only when a specific identifier string is supplied
	 * 							- reward	name of the point-based reward to give to player, if the action trigger custom-point reward that doesn't specify reward name
	 * 							- quantity	amount of the point-based reward to give to player, if the action trigger custom-point reward that doesn't specify reward quantity
	 * @return
	 */
	public Request rule(String playerId, String action, String... optionalData)
	{
		StringBuilder param = new StringBuilder();
		try
		{
			param.append("token=");
			param.append(token);
			param.append("&player_id=");
			param.append(URLEncoder.encode(playerId, "UTF-8"));
			param.append("&action=");
			param.append(URLEncoder.encode(action, "UTF-8"));
			
			for(int i=0; i<optionalData.length; ++i)
				param.append("&"+optionalData[i]);
		}
		catch (UnsupportedEncodingException e)
		{
			return null;
		}
		return getPlaybasisAnswer("Engine/rule", param.toString());	
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

	public static Request getPlaybasisAnswer(String method, String data)
	{
		String json = call(method, data);
		// modifying JSON because it is bugged
		//System.out.println("received JSON :"+json);
		String newJson = null;
		try {
			if(json != null)
				newJson = JSONModifier.convertJSON(json);
		} catch (JsonParseException e) {
			e.printStackTrace();
		} catch (JsonMappingException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		if(newJson == null) return null;
		//System.out.println("modified JSON :"+newJson);
		RequestMaker rm= new RequestMaker(newJson);
		return rm.getRequestResult();
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

			//set method to post if we have data to send
			if (data != null)
			{
				http.setRequestMethod("POST");
				http.setDoOutput(true);
				http.setUseCaches(false);
				http.setRequestProperty("Content-Type", CONTENT_TYPE);
				http.setRequestProperty("charset", CHARSET);
				
				http.setRequestProperty("Accept", "application/json");
				
				http.setFixedLengthStreamingMode(data.getBytes().length); //http.setChunkedStreamingMode(0);
				
				//write data
				BufferedWriter out = new BufferedWriter(new OutputStreamWriter(http.getOutputStream()));
				out.write(data);
				out.flush();
			}
			else
			{
				http.setRequestMethod("GET");
				http.setRequestProperty("Accept", "application/json");
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
                        System.err.println(e);
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

	public String getDateExpire() {
		return dateExpire;
	}

	public void setDateExpire(String dateExpire) {
		this.dateExpire = dateExpire;
	}
	
	//Unit test
	public static void main(String[] args) {
		Playbasis pb = new Playbasis();
		
		boolean result = pb.auth("abc", "abcde");
		System.out.println("testPlaybasis: Auth return is"+result);
		if(result){
			Request request = null;
//			Request request = pb.login("javaApiUser4");
//			request = pb.login("javaApiUser4");
			//Request request = pb.register("javaApiUser4","javaApiUser4","testUserjava4@test.com","http://lakorndara.files.wordpress.com/2011/04/167148_187576261263101_116354608385267_546322_3889345_n1.jpg");
			//Request request = pb.playerList("1610533872facebook,1003047582facebook,100002511981270facebook");
			//Request request = pb.rank("point",10);
			//Request request = pb.playerGoods("1610533872facebook");
			//Request request = pb.goodsList();
			
			//Request request = pb.badges();
			//request = pb.claimBadge("javaApiUser4", "52ea1ea78d8c89401c00004f");
			//request = pb.redeemBadge("javaApiUser4", "52ea1ea78d8c89401c00004f");
			request = pb.badgeOwned("javaApiUser4");
//			for(int i=0;i<10;i++){
//				pb.login("javaApiUser4");
//				pb.logout("javaApiUser4");
//				request = pb.rule("javaApiUser4","login");
//				//request = pb.rule("javaApiUser4","logout");
//			}
//			request = pb.badgeOwned("javaApiUser4");
//			request = pb.rule("javaApiUser4","point");
			//Request request = pb.badgeOwned("javaApiUser4");
			if(request == null) return;
			if(request != null && request.isSuccess()){
				Object item = request.getResponse();
				System.out.println("test Playbasis DEBUG : player is "+ item);
			}
			else{
				System.out.println("test Playbasis DEBUG : request failed with message "+ request.getMessage());
			}
		}
	}
}