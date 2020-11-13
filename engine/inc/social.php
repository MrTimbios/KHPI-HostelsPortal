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
 File: social.php
-----------------------------------------------------
 Use: Setup social networking
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1) {

	msg("error", $lang['index_denied'], $lang['index_denied']);

}

require_once (ENGINE_DIR . '/data/socialconfig.php');

function showRow($title = "", $description = "", $field = "", $class = "") {
	echo "<tr>
       <td class=\"col-xs-6 col-sm-6 col-md-7\"><div class=\"media-heading text-semibold\">{$title}</div><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
       <td class=\"col-xs-6 col-sm-6 col-md-5\">{$field}</td>
       </tr>";
}
	
function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" name=\"$name\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function makeCheckBox($name, $selected) {
	$selected = $selected ? "checked" : "";
	
	return "<input class=\"switch\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";
}

if( $action == "save" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$save_con = $_POST['save_con'];
	$save_con['vk'] = intval($save_con['vk']);
	$save_con['od'] = intval($save_con['od']);
	$save_con['fc'] = intval($save_con['fc']);
	$save_con['google'] = intval($save_con['google']);
	$save_con['mailru'] = intval($save_con['mailru']);
	$save_con['yandex'] = intval($save_con['yandex']);


	$find = array();
	$replace = array();
	
	$find[] = "'\r'";
	$replace[] = "";
	$find[] = "'\n'";
	$replace[] = "";

	$save_con = $save_con + $social_config;
	
	$handler = fopen( ENGINE_DIR . '/data/socialconfig.php', "w" );
	
	fwrite( $handler, "<?PHP \n\n//Social Configurations\n\n\$social_config = array (\n\n" );
	foreach ( $save_con as $name => $value ) {
		
		$value = trim(strip_tags(stripslashes( $value )));
		$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
		$value = preg_replace( $find, $replace, $value );
			
		$name = trim(strip_tags(stripslashes( $name )));
		$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
		$name = preg_replace( $find, $replace, $name );
		
		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		$value = str_replace( chr(92), "", $value );
		$value = str_replace( chr(0), "", $value );
		$value = str_replace( '(', "", $value );
		$value = str_replace( ')', "", $value );
		$value = str_ireplace( "base64_decode", "base64_dec&#111;de", $value );
		
		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		$name = str_replace( ".", "", $name );
		$name = str_replace( '/', "", $name );
		$name = str_replace( chr(92), "", $name );
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '(', "", $name );
		$name = str_replace( ')', "", $name );
		$name = str_ireplace( "base64_decode", "base64_dec&#111;de", $name );
		
		fwrite( $handler, "'{$name}' => '{$value}',\n\n" );
	
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );
	
	clear_cache();

	if (function_exists('opcache_reset')) {
		opcache_reset();
	}

	msg( "success", $lang['opt_sysok'], $lang['opt_sysok_1'], "?mod=social" );


}

echoheader("<i class=\"fa fa-facebook-official position-left\"></i><span class=\"text-semibold\">{$lang['opt_social']}</span>", $lang['opt_socialc1']);

if (!$config['allow_social']) {

	$lang['hint_social3'] = "<br /><br /><span class=\"text-danger\">{$lang['hint_social3']}</span>";

} else {

	$lang['hint_social3'] = "";

}

echo "<div class=\"alert alert-info alert-styled-left alert-arrow-left alert-component text-size-small\">{$lang['hint_social']} <a onclick=\"javascript:Help('social'); return false;\" href=\"#\">{$lang['hint_social2']}</a>{$lang['hint_social3']}</div>";


echo <<<HTML
<form action="?mod=social&action=save" name="conf" id="conf" method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_social']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
      <thead>
      <tr>
        <th>{$lang['vconf_title']}</th>
        <th></th>
      </tr>
      </thead>
HTML;

showRow( $lang['sconf_vk'], $lang['sconf_vkd'], makeCheckBox( "save_con[vk]", "{$social_config['vk']}" ) );
showRow( $lang['sconf_vk1'], $lang['sconf_vk1d'], "<input type=text class=\"form-control\" name=\"save_con[vkid]\" value=\"{$social_config['vkid']}\" >" );
showRow( $lang['sconf_vk2'], $lang['sconf_vk2d'], "<input type=text class=\"form-control\" name=\"save_con[vksecret]\" value=\"{$social_config['vksecret']}\" >" );


showRow( $lang['sconf_od'], $lang['sconf_odd'], makeCheckBox( "save_con[od]", "{$social_config['od']}" ) );
showRow( $lang['sconf_od1'], $lang['sconf_od1d'], "<input type=text class=\"form-control\" name=\"save_con[odid]\" value=\"{$social_config['odid']}\" >" );
showRow( $lang['sconf_od3'], $lang['sconf_od3d'], "<input type=text class=\"form-control\" name=\"save_con[odpublic]\" value=\"{$social_config['odpublic']}\" >" );
showRow( $lang['sconf_od2'], $lang['sconf_od2d'], "<input type=text class=\"form-control\" name=\"save_con[odsecret]\" value=\"{$social_config['odsecret']}\" >" );

showRow( $lang['sconf_fc'], $lang['sconf_fcd'], makeCheckBox( "save_con[fc]", "{$social_config['fc']}" ) );
showRow( $lang['sconf_fc1'], $lang['sconf_fc1d'], "<input type=text class=\"form-control\" name=\"save_con[fcid]\" value=\"{$social_config['fcid']}\" >" );
showRow( $lang['sconf_fc2'], $lang['sconf_fc2d'], "<input type=text class=\"form-control\" name=\"save_con[fcsecret]\" value=\"{$social_config['fcsecret']}\" >" );

showRow( $lang['sconf_google'], $lang['sconf_googled'], makeCheckBox( "save_con[google]", "{$social_config['google']}" ) );
showRow( $lang['sconf_google1'], $lang['sconf_google1d'], "<input type=text class=\"form-control\" name=\"save_con[googleid]\" value=\"{$social_config['googleid']}\" >" );
showRow( $lang['sconf_google2'], $lang['sconf_google2d'], "<input type=text class=\"form-control\" name=\"save_con[googlesecret]\" value=\"{$social_config['googlesecret']}\" >" );

showRow( $lang['sconf_mailru'], $lang['sconf_mailrud'], makeCheckBox( "save_con[mailru]", "{$social_config['mailru']}" ) );
showRow( $lang['sconf_mailru1'], $lang['sconf_mailru1d'], "<input type=text class=\"form-control\" name=\"save_con[mailruid]\" value=\"{$social_config['mailruid']}\" >" );
showRow( $lang['sconf_mailru2'], $lang['sconf_mailru2d'], "<input type=text class=\"form-control\" name=\"save_con[mailrusecret]\" value=\"{$social_config['mailrusecret']}\" >" );

showRow( $lang['sconf_yandex'], $lang['sconf_yandexd'], makeCheckBox( "save_con[yandex]", "{$social_config['yandex']}" ) );
showRow( $lang['sconf_yandex1'], $lang['sconf_yandex1d'], "<input type=text class=\"form-control\" name=\"save_con[yandexid]\" value=\"{$social_config['yandexid']}\" >" );
showRow( $lang['sconf_yandex2'], $lang['sconf_yandex2d'], "<input type=text class=\"form-control\" name=\"save_con[yandexsecret]\" value=\"{$social_config['yandexsecret']}\" >" );


echo <<<HTML
</table></div></div>
<div style="margin-bottom:30px;">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>

</form>
HTML;


if(!is_writable(ENGINE_DIR . '/data/socialconfig.php')) {

	$lang['stat_system'] = str_replace ("{file}", "engine/data/socialconfig.php", $lang['stat_system']);

	echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_system']}</div>";

}

echofooter();
?>