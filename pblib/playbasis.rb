require 'net/https'
require 'uri'
require 'json'
require 'pp'

class Playbasis

	BASE_URL = 'https://api.pbapp.net/'
	#BASE_URL = 'https://dev.pbapp.net/api/'

	def initialize()
		@token = ''
	end

	def auth(apiKey, apiSecret)
		result = call('Auth', {	:api_key => apiKey,
								:api_secret => apiSecret })
		@token = result['response']['token']
		return @token.is_a? String;
	end

	def player(playerId)
		return call("Player/#{playerId}", { :token => @token })
	end

	def register(playerId, username, email, imageUrl, optionalData={})
		return call("Player/#{playerId}/register", {
			:token => @token,
			:username => username,
			:email => email,
			:image => imageUrl
			}.merge(optionalData));
	end

	def login(playerId)
		return call("Player/#{playerId}/login", { :token => @token });
	end
		
	def logout(playerId)
		return call("Player/#{playerId}/logout", { :token => @token });
	end

	def points(playerId)
		return call("Player/#{playerId}/points", { :token => @token });
	end

	def point(playerId, pointName)
		return call("Player/#{playerId}/point/#{pointName}", { :token => @token });
	end

	def actionLastPerformed(playerId)
		return call("Player/#{playerId}/action/time", { :token => @token });
	end
	
	def actionLastPerformedTime(playerId, actionName)
		return call("Player/#{playerId}/action/#{actionName}/time", { :token => @token });
	end
	
	def actionPerformedCount(playerId, actionName)
		return call("Player/#{playerId}/action/#{actionName}/count", { :token => @token });
	end
	
	def badgeOwned(playerId)
		return call("Player/#{playerId}/badge", { :token => @token });
	end
	
	def rank(rankedBy, limit)
		return call("Player/rank/#{rankedBy}/#{limit}", { :token => @token });
	end
	
	def badges()
		return call("Badge", { :token => @token });
	end
	
	def badge(badgeId)
		return call("Badge/#{badgeId}", { :token => @token });
	end
	
	def badgeCollections()
		return call("Badge/collection", { :token => @token });
	end
	
	def badgeCollection(collectionId)
		return call("Badge/collection/#{collectionId}", { :token => @token });
	end
	
	def actionConfig()
		return call("Engine/actionConfig", { :token => @token });
	end

	def rule(playerId, action, optionalData={})
		return call("Engine/rule", {
			:token => @token,
			:player_id => playerId,
			:action => action
			}.merge(optionalData));
	end

	def call(method, data)
		
		uri = URI.parse(BASE_URL + method)
		http = Net::HTTP.new(uri.host, uri.port)
		http.use_ssl = true
		http.verify_mode = OpenSSL::SSL::VERIFY_NONE
		request = Net::HTTP::Post.new(uri.request_uri)
		request.set_form_data(data)
		result = http.request(request)
		return JSON.parse(result.body)
	end
end
