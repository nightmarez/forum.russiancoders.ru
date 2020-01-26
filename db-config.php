<?php
	include_once('validator.php'); 

	define('DB_NAME', 'nightmarez_forum');
	define('DB_USER', 'dbuser');
	define('DB_PASS', 'HJkbN789d__jH_');
	define('RE_SECRET', '6LdlUjcUAAAAAJXEqnJ9WpVHs4onHmzg93kBAPD2');
	define('SMTP_PASS', 'F2@k?-\Lfz\3<P');

	define('vk_client_id', '5519267');
	define('vk_client_secret', '68127JoRY5PtrHYuAn12');
	define('vk_redirect_uri', 'https://russiancoders.tech/vk-auth.php');

	function genVkAuthLink()
	{
		$url = 'http://oauth.vk.com/authorize';
		$params = array(
		    'client_id'     => vk_client_id,
		    'redirect_uri'  => vk_redirect_uri,
		    'response_type' => 'code'
		);
		return $url . '?' . urldecode(http_build_query($params));
	}

	function getVkToken($code)
	{
		$params = array(
	        'client_id' => vk_client_id,
	        'client_secret' => vk_client_secret,
	        'code' => $code,
	        'redirect_uri' => vk_redirect_uri
	    );
	    $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
	    return $token;
	}

	define('fb_client_id', '196553980693169');
	define('fb_client_secret', '23fab83ee8412cd4af6ee347ac4d2e28');
	define('fb_redirect_uri', 'https://russiancoders.tech/fb-auth.php');

	function genFbAuthLink()
	{
		$url = 'https://www.facebook.com/dialog/oauth';
		$params = array(
		    'client_id'     => fb_client_id,
		    'redirect_uri'  => fb_redirect_uri,
		    'response_type' => 'code',
		    'scope'         => 'email,user_birthday'
		);
		return $url . '?' . urldecode(http_build_query($params));
	}

	function getFbToken($code)
	{
		$params = array(
	        'client_id'     => fb_client_id,
	        'redirect_uri'  => fb_redirect_uri,
	        'client_secret' => fb_client_secret,
	        'code'          => $code
	    );
	    $url = 'https://graph.facebook.com/oauth/access_token';
	    $token = null;
		parse_str(file_get_contents($url . '?' . http_build_query($params)), $token);
		return $token;
	}

	define('ok_client_id', '1239898368');
	define('ok_client_public', 'CBAKFIOKEBABABABA');
	define('ok_client_secret', '2D18F9AB55B644E86765CBCA');
	define('ok_redirect_uri', 'http://russiancoders.tech/ok-auth.php');

	function genOkAuthLink()
	{
		$url = 'http://www.odnoklassniki.ru/oauth/authorize';
		$params = array(
		    'client_id'     => ok_client_id,
		    'response_type' => 'code',
		    'redirect_uri'  => ok_redirect_uri
		);
		return $url . '?' . urldecode(http_build_query($params));
	}
	
	function getOkToken($code)
	{
		$params = array(
	        'code' => $code,
	        'redirect_uri' => ok_redirect_uri,
	        'grant_type' => 'authorization_code',
	        'client_id' => ok_client_id,
	        'client_secret' => ok_client_secret
	    );
	    $url = 'http://api.odnoklassniki.ru/oauth/token.do';
	    
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($curl);
	    curl_close($curl);
	    $token = json_decode($result, true);
	    return $token;
	}