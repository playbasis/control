package com.playbasis.pblib;

import java.io.IOException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;

/**
 * This class to clean the JSON...
 * @author OS
 *
 */
public class JSONModifier {

	public JSONModifier(){
		
	}
	public static String convertJSON(String json) throws JsonParseException, JsonMappingException, IOException{
		ObjectMapper mapper = new ObjectMapper();
		Map<String,Object> myMap = mapper.readValue(json, HashMap.class);
		convert(myMap);
		String result = mapper.writeValueAsString(myMap);
		return result;
	}
	/**
	 * convert JSON to cover specific bugs
	 * @param map
	 * @return
	 */
	public static void convert(Map<String,Object> map){
		Object response = map.get("response");
		if(response !=null && response instanceof List && ((List)response).size()==0){
			map.put("response", null);
		}
//		if(response !=null){
//			Object points = map.get("points");
//			if(points != null && points instanceof List){
//				map.put("points", null);
//			}
//	
//		}
				
	}
	public static void  main(String[] args){
		//{"timestamp":1395129461,"response":{"points":[{"reward_id":{"$id":"52ea1ea78d8c89401c0000b4"},"reward_name":"point","value":12353},{"reward_id":{"$id":"52ea1ea78d8c89401c0000b5"},"reward_name":"exp","value":633}]},"message":"Success","time":"Tue, 18 Mar 2014 14:57:41 +0700 Asia/Bangkok","error_code":"0000","success":true}
		String json = "{\"success\":true,\"error_code\":\"0000\",\"message\":\"Success\",\"response\":[],\"timestamp\":1395126492,\"time\":\"Tue, 18 Mar 2014 14:08:12 +0700 Asia Bangkok\"}";
		try {
			String result = convertJSON(json);
			System.out.println("result : "+result);
		} catch (JsonParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JsonMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
}
