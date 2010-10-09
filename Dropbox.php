<?
class Dropbox
{
	const DB_CONSUMER_KEY = '...';
	const DB_CONSUMER_SECRET = '...';
	
	const DB_USER = '...';
	const DB_PASSWORD = '...';
	
	private function _getClient($uri)
	{
		$client = new Zend_Http_Client('http://api.dropbox.com/0/token');
		$client->setParameterPost(array(
			'oauth_consumer_key' => self::DB_CONSUMER_KEY,
			'oauth_consumer_secret' => self::DB_CONSUMER_SECRET,
			'email' => self::DB_USER,
			'password' => self::DB_PASSWORD,
		));

		$response = $client->request('POST');
		$data = json_decode($response->getBody());
		$token = $data->token;
		$secret = $data->secret;

		$tokenObject = new Zend_Oauth_Token_Access();
		$tokenObject->setToken($token);
		$tokenObject->setTokenSecret($secret);
		
		$options = array(
			'consumerKey'    => self::DB_CONSUMER_KEY,
			'consumerSecret' => self::DB_CONSUMER_SECRET,
		);
		return $tokenObject->getHttpClient($options, $uri);
	}
	
	public function getfilesAction($path)
	{
		$uri = 'http://api.dropbox.com/0/metadata/dropbox/' . ltrim($path,'/');
		
		$client = $this->_getClient($uri);
		$client->setParameterPost(array(
			'list' => 1,
		));
		$response = $client->request('POST');
		$data = json_decode($response->getBody());
		
		return $data->contents;
	}
	
}