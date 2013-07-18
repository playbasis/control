require 'net/https'
require 'uri'
require 'json'
require 'pp'

class Playbasis

	BASE_URL = 'https://api.pbapp.net/'

	def initialize()
		@token = ''
		@apiKeyParam = ''
	end

	def auth(apiKey, apiSecret)
		@apiKeyParam = "?api_key=#{apiKey}"
		result = call('Auth', {	:api_key => apiKey,
								:api_secret => apiSecret })
		@token = result['response']['token']
		return @token.is_a? String
	end

	def player(playerId)
		return call("Player/#{playerId}", { :token => @token })
	end

	# @param	optionalData	Key-value for additional parameters to be sent to the register method.
	# 							The following keys are supported:
	# 							- facebook_id
	# 							- twitter_id
	# 							- password		assumed hashed
	# 							- first_name
	# 							- last_name
	# 							- nickname
	# 							- gender		1=Male, 2=Female
	# 							- birth_date	format YYYY-MM-DD
	def register(playerId, username, email, imageUrl, optionalData={})
		return call("Player/#{playerId}/register", {
			:token => @token,
			:username => username,
			:email => email,
			:image => imageUrl
			}.merge(optionalData))
	end

	def login(playerId)
		return call("Player/#{playerId}/login", { :token => @token })
	end
		
	def logout(playerId)
		return call("Player/#{playerId}/logout", { :token => @token })
	end

	def points(playerId)
		return call("Player/#{playerId}/points" + @apiKeyParam)
	end

	def point(playerId, pointName)
		return call("Player/#{playerId}/point/#{pointName}" + @apiKeyParam)
	end

	def actionLastPerformed(playerId)
		return call("Player/#{playerId}/action/time" + @apiKeyParam)
	end
	
	def actionLastPerformedTime(playerId, actionName)
		return call("Player/#{playerId}/action/#{actionName}/time" + @apiKeyParam)
	end
	
	def actionPerformedCount(playerId, actionName)
		return call("Player/#{playerId}/action/#{actionName}/count" + @apiKeyParam)
	end
	
	def badgeOwned(playerId)
		return call("Player/#{playerId}/badge" + @apiKeyParam)
	end
	
	def rank(rankedBy, limit)
		return call("Player/rank/#{rankedBy}/#{limit}" + @apiKeyParam)
	end
	
	def badges()
		return call("Badge" + @apiKeyParam)
	end
	
	def badge(badgeId)
		return call("Badge/#{badgeId}" + @apiKeyParam)
	end
	
	def badgeCollections()
		return call("Badge/collection" + @apiKeyParam)
	end
	
	def badgeCollection(collectionId)
		return call("Badge/collection/#{collectionId}" + @apiKeyParam)
	end
	
	def actionConfig()
		return call("Engine/actionConfig" + @apiKeyParam)
	end

	# @param	optionalData	Key-value for additional parameters to be sent to the rule method.
	# 							The following keys are supported:
	# 							- url		url or filter string (for triggering non-global actions)
	# 							- reward	name of the custom-point reward to give (for triggering rules with custom-point reward)
	# 							- quantity	amount of points to give (for triggering rules with custom-point reward)
	def rule(playerId, action, optionalData={})
		return call("Engine/rule", {
			:token => @token,
			:player_id => playerId,
			:action => action
			}.merge(optionalData));
	end

	def call(method, data=nil)
		
		uri = URI.parse(BASE_URL + method)
		http = Net::HTTP.new(uri.host, uri.port)
		http.use_ssl = true
		http.verify_mode = OpenSSL::SSL::VERIFY_NONE
		if data
			request = Net::HTTP::Post.new(uri.request_uri)
			request.set_form_data(data)
		else
			request = Net::HTTP::Get.new(uri.request_uri)
		end
		result = http.request(request)
		return JSON.parse(result.body)
	end
end
