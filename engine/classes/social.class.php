<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: social.class.php
-----------------------------------------------------
 Use: Authorization through social networks
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class AuthViaVK {

    function get_user( $social_config ) {
		global $config, $lang;

		if ( !isset($_SESSION['vk_access_token']) ) { 
			$params = array(
				'client_id'     => $social_config['vkid'],
				'client_secret' => $social_config['vksecret'],
				'code' => $_GET['code'],
				'redirect_uri'  => $config['http_home_url'] . "index.php?do=auth-social&provider=vk"
			);
	
			$token = @json_decode(http_get_contents('https://oauth.vk.com/access_token' . '?' . http_build_query($params)), true);
			
		} else $token=array('user_id' => $_SESSION['vk_access_user_id'], 'access_token' => $_SESSION['vk_access_token'] );

		if (isset($token['access_token'])) {

			$params = array(
				'user_ids'     => $token['user_id'],
				'fields'       => 'id,first_name,last_name,nickname,photo_max',
				'access_token' => $token['access_token'],
				'v'	=> '5.90'
			);

			$user = @json_decode(http_get_contents('https://api.vk.com/method/users.get' . '?' . http_build_query($params)), true);

			if (isset($user['response'][0]['id'])) {

	            $user = $user['response'][0];
			
				if ( !isset($_SESSION['vk_access_token']) AND !$token['email']) { $_SESSION['vk_access_token'] = $token['access_token']; $_SESSION['vk_access_user_id'] = $token['user_id']; $_SESSION['vk_access_code'] = $_GET['code']; }

				if( !$token['email'] AND isset($_GET['email']) ) $token['email'] = $_GET['email'];

				return array ('sid' => sha1 ('vkontakte'.$user['id']), 'nickname' => $user['nickname'], 'name' => $user['first_name'].' '.$user['last_name'], 'email' => $token['email'], 'avatar' => $user['photo_max'], 'provider' => 'vkontakte' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class AuthViaGoogle {

    function get_user( $social_config ) {
		global $config, $lang;

		$params = array(
			'client_id'     => $social_config['googleid'],
			'client_secret' => $social_config['googlesecret'],
			'grant_type' 	=> 'authorization_code',
			'code' => $_GET['code'],
			'redirect_uri'  => $config['http_home_url'] . "index.php?do=auth-social&provider=google",

		);

		$token = @json_decode(http_get_contents('https://accounts.google.com/o/oauth2/token', $params), true);

		if (isset($token['access_token'])) {

			$params['access_token'] = $token['access_token'];

			$user = @json_decode(http_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . http_build_query($params)), true);

			if (isset($user['id'])) {

				return array ('sid' => sha1 ('google'.$user['id']), 'nickname' => $user['name'], 'name' => $user['given_name'].' '.$user['family_name'], 'email' => $user['email'], 'avatar' => $user['picture'], 'provider' => 'Google' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class AuthViaMailru {

    function get_user( $social_config ) {
		global $config, $lang;

		$params = array(
			'client_id'     => $social_config['mailruid'],
			'client_secret' => $social_config['mailrusecret'],
			'grant_type' 	=> 'authorization_code',
			'code' => $_GET['code'],
			'redirect_uri'  => $config['http_home_url'] . "index.php?do=auth-social&provider=mailru",

		);

		$token = @json_decode(http_get_contents('https://oauth.mail.ru/token', $params), true);

		if (isset($token['access_token'])) {

			$params = array(
				'access_token'  => $token['access_token']
			);

			$user = @json_decode(http_get_contents('https://oauth.mail.ru/userinfo' . '?' . http_build_query($params)), true);

			if (isset($user['nickname']) AND $user['nickname'] AND isset($user['email']) AND $user['email']) {
				
				$uid = $user['nickname'].$user['email'];

				return array ('sid' => sha1 ('mailru'.$uid), 'nickname' => $user['nickname'], 'name' => $user['name'], 'email' => $user['email'], 'avatar' => $user['image'], 'provider' => 'Mail.Ru' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class AuthViaYandex {

    function get_user( $social_config ) {
		global $config, $lang;

		$params = array(
			'client_id'     => $social_config['yandexid'],
			'client_secret' => $social_config['yandexsecret'],
			'grant_type' 	=> 'authorization_code',
			'code' => $_GET['code']

		);

		$token = @json_decode(http_get_contents('https://oauth.yandex.ru/token', $params), true);

		if (isset($token['access_token'])) {

			$params = array(
				'format'       => 'json',
				'oauth_token'  => $token['access_token']
			);

			$user = @json_decode(http_get_contents('https://login.yandex.ru/info' . '?' . http_build_query($params)), true);

			if (isset($user['id'])) {
				
				if( $user['default_avatar_id'] ) {
					$user['avatar'] = "https://avatars.yandex.net/get-yapic/{$user['default_avatar_id']}/islands-200";
				} else $user['avatar'] = "";

				return array ('sid' => sha1 ('yandex'.$user['id']), 'nickname' => $user['display_name'], 'name' => $user['real_name'], 'email' => $user['default_email'], 'avatar' => $user['avatar'], 'provider' => 'Yandex' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class AuthViaFacebook {

    function get_user( $social_config ) {
		global $config, $lang;

		$params = array(
			'client_id'     => $social_config['fcid'],
			'client_secret' => $social_config['fcsecret'],
			'code' => $_GET['code'],
			'redirect_uri'  => $config['http_home_url'] . "index.php?do=auth-social&provider=fc"
		);

		$token = @json_decode(http_get_contents('https://graph.facebook.com/oauth/access_token' . '?' . http_build_query($params)), true);

		if (isset($token['access_token'])) {

			$params = array('access_token' => $token['access_token'], 'fields' => "id,name,email,first_name,last_name,picture");

			$user = @json_decode(http_get_contents('https://graph.facebook.com/me' . '?' . http_build_query($params)), true);

			if (isset($user['id'])) {

				return array ('sid' => sha1 ('facebook'.$user['id']), 'nickname' => $user['name'], 'name' => $user['first_name'].' '.$user['last_name'], 'email' => $user['email'], 'avatar' => "https://graph.facebook.com/".$user['id']."/picture?type=large", 'provider' => 'Facebook' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class AuthViaOdnoklassniki {

    function get_user( $social_config ) {
		global $config, $lang;

		if ( !isset($_SESSION['od_access_token']) ) {

			$params = array(
				'client_id'     => $social_config['odid'],
				'client_secret' => $social_config['odsecret'],
				'grant_type' => 'authorization_code',
				'code' => $_GET['code'],
				'redirect_uri'  => $config['http_home_url'] . "index.php?do=auth-social&provider=od"
			);

			$token = @json_decode(http_get_contents('https://api.odnoklassniki.ru/oauth/token.do', $params), true);

		} else $token=array('access_token' => $_SESSION['od_access_token'] );

		if (isset($token['access_token'])) {

			$sign = md5("application_key={$social_config['odpublic']}fields=name,first_name,last_name,email,pic_2format=jsonmethod=users.getCurrentUser" . md5("{$token['access_token']}{$social_config['odsecret']}"));

			$params = array(
				'method'          => 'users.getCurrentUser',
				'access_token'    => $token['access_token'],
				'application_key' => $social_config['odpublic'],
				'fields'       	  => 'name,first_name,last_name,email,pic_2',
				'format'          => 'json',
				'sig'             => $sign
			);

			$user = @json_decode(http_get_contents('https://api.odnoklassniki.ru/fb.do' . '?' . http_build_query($params)), true);

			if (isset($user['uid'])) {

				if ( !isset($_SESSION['od_access_token']) ) { $_SESSION['od_access_token'] = $token['access_token']; $_SESSION['od_access_code'] = $_GET['code']; }

				if(!$user['email'] AND isset($_GET['email']) ) $user['email'] = $_GET['email'];

				return array ('sid' => sha1 ('odnoklassniki'.$user['uid']), 'nickname' => $user['name'], 'name' => $user['first_name'].' '.$user['last_name'], 'email' => $user['email'], 'avatar' => $user['pic_2'], 'provider' => 'Odnoklassniki' );

			} else return $lang['social_err_3'];

		} else return $lang['social_err_1'];

    }

}

class SocialAuth {

	private $auth = false;
	private $social_config = array();

    function __construct( $social_config ){

        if ($_GET['provider'] == "vk" AND $social_config['vk']) {

            $this->auth = new AuthViaVK();

        } elseif ($_GET['provider'] == "google" AND $social_config['google']) {

            $this->auth = new AuthViaGoogle();

        } elseif ( $_GET['provider'] == "mailru" AND $social_config['mailru']) {

            $this->auth = new AuthViaMailru();

        } elseif ($_GET['provider'] == "yandex" AND $social_config['yandex']) {

            $this->auth = new AuthViaYandex();

        } elseif ($_GET['provider'] == "fc" AND $social_config['fc']) {

            $this->auth = new AuthViaFacebook();

        } elseif ($_GET['provider'] == "od" AND $social_config['od']) {

            $this->auth = new AuthViaOdnoklassniki();

        } else {

            $this->auth = false;

        }

		$this->social_config = $social_config;

    }

    function getuser(){
		global $config, $lang;

		if( $this->auth !== false ) {

			$user = $this->auth->get_user( $this->social_config );

			if( is_array($user) ) {

				if( !$user['nickname'] ) {

					$user['nickname'] = $user['name'];

				}

				$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" );
				$user['email'] = str_replace( $not_allow_symbol, '',  $user['email']);

				$user['nickname'] = preg_replace("/[\||\'|\<|\>|\[|\]|\%|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\}\+]/", '', $user['nickname'] );
				$user['nickname'] = str_ireplace( ".php", ".ppp", $user['nickname'] );
				$user['nickname'] = trim( htmlspecialchars( $user['nickname'], ENT_QUOTES, $config['charset'] ) );
				$user['name'] = trim( htmlspecialchars( $user['name'], ENT_QUOTES, $config['charset'] ) );

				if(dle_strlen( $user['nickname'], $config['charset'] ) > 37) $user['nickname'] = dle_substr( $user['nickname'], 0, 37, $config['charset'] );

			}

			return $user;

		} else return $lang['social_err_2'];

	}

}

?>