<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once ("Google/autoload.php");

define('APPLICATION_NAME', 'Google Calendar API');
define('CREDENTIALS_PATH', 'application/config/google-credentials.json');
define('CLIENT_SECRET_PATH', 'application/config/google-client_secret.json');
define('SCOPES', implode(' ', array(
    Google_Service_Calendar::CALENDAR_READONLY)
));

class GoogleApi
{
    protected $_ci;
    protected $_config;
    protected $_client;

    function __construct($config=array())
    {
        $this->_ci =& get_instance();
        $this->_config = $config;
        $this->_client = $this->getClient();
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    private function getClient() {
        $client = new Google_Client();
        $client->setApplicationName(APPLICATION_NAME);
        $client->setScopes(SCOPES);
        $client->setAuthConfigFile(CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->authenticate($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, $accessToken);
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, $client->getAccessToken());
        }
        return $client;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    private function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

    public function calendar() {
        return new Google_Service_Calendar($this->_client);
    }
}