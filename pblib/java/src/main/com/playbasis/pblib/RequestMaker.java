package com.playbasis.pblib;

import java.io.IOException;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;

public class RequestMaker {
	private static ObjectMapper mapper = new ObjectMapper();
	private Request request = null;
        
	public RequestMaker(String jsonString){
		if(jsonString == null) return;
		try {
			
			request = mapper.readValue(jsonString, Request.class);
			
		} catch (JsonParseException e) {
			e.printStackTrace();
		} catch (JsonMappingException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	public Request getRequestResult() {
		return request;
	}
	public Object getCommandResult() {
		Object response = request.getResponse();
		return response;
	}
}
