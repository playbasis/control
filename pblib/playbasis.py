import requests, json

class Playbasis:
    """The Playbasis Object"""
    
    BASE_URL = 'https://api.pbapp.net/'

    def __init__(self):
        self.token = None
        self.apiKeyParam = None

    def auth(self, apiKey, apiSecret):
        self.apiKeyParam = '?api_key=' + apiKey
        result = self.call('Auth', { 'api_key' : apiKey, 
                                     'api_secret' : apiSecret })
        self.token = result['response']['token']
        return isinstance(self.token, basestring)

    def player(self, playerId):
        return self.call('Player/' + playerId, {'token' : self.token})

    # @param    optionalData    Key-value for additional parameters to be sent to the register method.
    #                           The following keys are supported:
    #                           - facebook_id
    #                           - twitter_id
    #                           - password      assumed hashed
    #                           - first_name
    #                           - last_name
    #                           - nickname
    #                           - gender        1=Male, 2=Female
    #                           - birth_date    format YYYY-MM-DD
    def register(self, playerId, username, email, imageUrl, optionalData={}):
        data = {
            'token' : self.token,
            'username' : username,
            'email' : email,
            'image' : imageUrl
        }
        data.update(optionalData)
        return self.call('Player/' + playerId + '/register', data)

    def login(self, playerId):
        return self.call('Player/' + playerId + '/login', {'token' : self.token})

    def logout(self, playerId):
        return self.call('Player/' + playerId + '/logout', {'token' : self.token})

    def points(self, playerId):
        return self.call('Player/%s/points%s' % (playerId, self.apiKeyParam))

    def point(self, playerId, pointName):
        return self.call('Player/%s/point/%s%s' % (playerId, pointName, self.apiKeyParam))

    def actionLastPerformed(self, playerId):
        return self.call('Player/%s/action/time%s' % (playerId, self.apiKeyParam))
    
    def actionLastPerformedTime(self, playerId, actionName):
        return self.call('Player/%s/action/%s/time%s' % (playerId, actionName, self.apiKeyParam))
    
    def actionPerformedCount(self, playerId, actionName):
        return self.call('Player/%s/action/%s/count%s' % (playerId, actionName, self.apiKeyParam))
    
    def badgeOwned(self, playerId):
        return self.call('Player/%s/badge%s' % (playerId, self.apiKeyParam))
    
    def rank(self, rankedBy, limit=20):
        return self.call('Player/rank/%s/%s%s' % (rankedBy, limit, self.apiKeyParam))

    def ranks(self, limit=20):
        return self.call('Player/ranks/%s%s' % (limit, self.apiKeyParam))
    
    def badges(self):
        return self.call('Badge' + self.apiKeyParam)
    
    def badge(self, badgeId):
        return self.call('Badge/' + badgeId + self.apiKeyParam)
    
    def badgeCollections(self):
        return self.call('Badge/collection' + self.apiKeyParam)
    
    def badgeCollection(self, collectionId):
        return self.call('Badge/collection/' + self.apiKeyParam)
    
    def actionConfig(self):
        return self.call('Engine/actionConfig' + self.apiKeyParam)

    # @param    optionalData    Key-value for additional parameters to be sent to the rule method.
    #                           The following keys are supported:
    #                           - url       url or filter string (for triggering non-global actions)
    #                           - reward    name of the custom-point reward to give (for triggering rules with custom-point reward)
    #                           - quantity  amount of points to give (for triggering rules with custom-point reward)
    def rule(self, playerId, action, optionalData={}):
        data = {
            'token' : self.token,
            'player_id' : playerId,
            'action' : action
        }
        data.update(optionalData)
        return self.call('Engine/rule', data)

    def call(self, method, data=None):
        url = self.BASE_URL + method
        print 'requesting url: ' + url
        if data:
            return json.loads(requests.post(url, data, verify=False).text)
        return json.loads(requests.get(url, verify=False).text)
