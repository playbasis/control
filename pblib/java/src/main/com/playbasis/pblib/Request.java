package com.playbasis.pblib;

/**
 * Class storing request information
 * success : boolean stating success or failure of the request
 * error_code : error code. "0000" for success.
 * message : error message or "Success"
 * response : Map<String, Object> representing the object requested if any (ex: Player information)
 * timestamp : time of the request in Long format
 * time : time of the request in format like "Wed, 19 Mar 2014 11:11:39 +0700 Asia\/Bangkok"
 * 
 * @author Sylvain Dormieu
 *
 */
public class Request {
	private boolean success;
	private String error_code;
	private String message;
	private Object response;
	private Long timestamp;
	private String time;
	/**
	 * success : boolean stating success or failure of the request
	 * @return
	 */
	public boolean isSuccess() {
		return success;
	}
	public void setSuccess(boolean success) {
		this.success = success;
	}
	/**
	 * error_code : error code. "0000" for success
	 * @return
	 */
	public String getError_code() {
		return error_code;
	}
	public void setError_code(String error_code) {
		this.error_code = error_code;
	}
	/**
	 * message : error message or "Success"
	 * @return
	 */
	public String getMessage() {
		return message;
	}
	public void setMessage(String message) {
		this.message = message;
	}
	/**
	 * response : Map<String, Object> representing the object requested if any (ex: Player information)
	 * @return
	 */
	public Object getResponse() {
		return response;
	}
	public void setResponse(Object response) {
		this.response = response;
	}
	/**
	 * timestamp : time of the request in Long format
	 * @return
	 */
	public Long getTimestamp() {
		return timestamp;
	}
	public void setTimestamp(Long timestamp) {
		this.timestamp = timestamp;
	}
	/**
	 * time : time of the request in format like "Wed, 19 Mar 2014 11:11:39 +0700 Asia\/Bangkok"
	 * @return
	 */
	public String getTime() {
		return time;
	}
	public void setTime(String time) {
		this.time = time;
	}
}
