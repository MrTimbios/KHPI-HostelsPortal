<?PHP
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
 File: options.php
-----------------------------------------------------
 Use: options
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( isset( $_REQUEST['subaction'] ) ) $subaction = $_REQUEST['subaction']; else $subaction = "";

if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

if( $action == "options" or $action == '' ) {
	
	echoheader( "<i class=\"fa fa-th-list position-left\"></i><span class=\"text-semibold\">{$lang['opt_all_rublik']}</span>", $lang['opt_all_rublikc'] );

	$options = array ();
	
	$options['config'] = array (
								
								array (
											'name' => $lang['opt_all'], 
											'url' => "?mod=options&action=syscon", 
											'descr' => $lang['opt_allc'], 
											'image' => "tools.png", 
											'access' => "admin" 
								), 
								
								array (
											'name' => $lang['opt_cat'], 
											'url' => "?mod=categories", 
											'descr' => $lang['opt_catc'], 
											'image' => "cats.png", 
											'access' => $user_group[$member_id['user_group']]['admin_categories'] 
								), 
								
								array (
											'name' => $lang['opt_db'], 
											'url' => "?mod=dboption", 
											'descr' => $lang['opt_dbc'], 
											'image' => "dbset.png", 
											'access' => "admin" 
								), 

								array (
											'name' => $lang['opt_vconf'], 
											'url' => "?mod=videoconfig", 
											'descr' => $lang['opt_vconfc'], 
											'image' => "video.png", 
											'access' => "admin" 
								),
								
								array (
											'name' => $lang['opt_xfil'], 
											'url' => "?mod=xfields&xfieldsaction=configure", 
											'descr' => $lang['opt_xfilc'], 
											'image' => "xfset.png", 
											'access' => $user_group[$member_id['user_group']]['admin_xfields'] 
								),

								array (
											'name' => $lang['opt_question'], 
											'url' => "?mod=question", 
											'descr' => $lang['opt_questionc'], 
											'image' => "question.png", 
											'access' => "admin" 
								)
	);
	
	$options['user'] = array (
							
							array (
										'name' => $lang['opt_priv'], 
										'url' => "?mod=editusers&action=edituser&id=".$member_id['user_id'], 
										'descr' => $lang['opt_privc'], 
										'image' => "pset.png", 
										'access' => "all" 
							), 
							
							array (
										'name' => $lang['opt_user'], 
										'url' => "?mod=editusers&action=list", 
										'descr' => $lang['opt_userc'], 
										'image' => "uset.png", 
										'access' => $user_group[$member_id['user_group']]['admin_editusers'] 
							), 
							
							array (
										'name' => $lang['opt_xprof'], 
										'url' => "?mod=userfields&xfieldsaction=configure", 
										'descr' => $lang['opt_xprofd'], 
										'image' => "xprof.png", 
										'access' => $user_group[$member_id['user_group']]['admin_userfields'] 
							), 
							
							array (
										'name' => $lang['opt_group'], 
										'url' => "?mod=usergroup", 
										'descr' => $lang['opt_groupc'], 
										'image' => "usersgroup.png", 
										'access' => "admin" 
							),

							array (
										'name' => $lang['opt_social'], 
										'url' => "?mod=social", 
										'descr' => $lang['opt_socialc'], 
										'image' => "social.png", 
										'access' => "admin" 
							),
							
							array (
										'name' => $lang['opt_ipban'], 
										'url' => "?mod=blockip", 
										'descr' => $lang['opt_ipbanc'], 
										'image' => "blockip.png", 
										'access' => $user_group[$member_id['user_group']]['admin_blockip'] 
							)
	);
	
	$options['templates'] = array (
									
									array (
											'name' => $lang['opt_t'], 
											'url' => "?mod=templates&user_hash=" . $dle_login_hash, 
											'descr' => $lang['opt_tc'], 
											'image' => "tmpl.png", 
											'access' => "admin" 
									), 
									
									array (
											'name' => $lang['opt_email'], 
											'url' => "?mod=email", 
											'descr' => $lang['opt_emailc'], 
											'image' => "mset.png", 
											'access' => "admin" 
									) 
	);

	
	
	$options['filter'] = array (
		
								array (
											'name' => $lang['opt_plugins'], 
											'url' => "?mod=plugins", 
											'descr' => $lang['opt_pluginsc'], 
											'image' => "plugins.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_srebuild'], 
											'url' => "?mod=rebuild", 
											'descr' => $lang['opt_srebuildc'], 
											'image' => "refresh.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_fil'], 
											'url' => "?mod=wordfilter", 
											'descr' => $lang['opt_filc'], 
											'image' => "fset.png", 
											'access' => $user_group[$member_id['user_group']]['admin_wordfilter'] 
								), 
								
								array (
											'name' => $lang['opt_iptools'], 
											'url' => "?mod=iptools", 
											'descr' => $lang['opt_iptoolsc'], 
											'image' => "iptools.png", 
											'access' => $user_group[$member_id['user_group']]['admin_iptools'] 
								), 
								array (
											'name' => $lang['opt_sfind'], 
											'url' => "?mod=search", 
											'descr' => $lang['opt_sfindc'], 
											'image' => "find_base.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_complaint'], 
											'url' => "?mod=complaint", 
											'descr' => $lang['opt_complaintc'], 
											'image' => "complaint.png", 
											'access' => $user_group[$member_id['user_group']]['admin_complaint'] 
								),
								array (
											'name' => $lang['opt_metatags'], 
											'url' => "?mod=metatags", 
											'descr' => $lang['opt_metatagsc'], 
											'image' => "metatags.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_redirects'], 
											'url' => "?mod=redirects", 
											'descr' => $lang['opt_redirectsc'], 
											'image' => "redirects.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_links'], 
											'url' => "?mod=links", 
											'descr' => $lang['opt_linksc'], 
											'image' => "links.png", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_check'], 
											'url' => "?mod=check", 
											'descr' => $lang['opt_checkc'], 
											'image' => "check.png", 
											'access' => "admin" 
								),
	);

	
	
	$options['others'] = array (
								array (
											'name' => $lang['opt_rules'], 
											'url' => "?mod=static&action=doedit&page=rules", 
											'descr' => $lang['opt_rulesc'], 
											'image' => "rules.png", 
											'access' => $user_group[$member_id['user_group']]['admin_static'] 
								), 
								
								array (
											'name' => $lang['opt_static'], 
											'url' => "?mod=static", 
											'descr' => $lang['opt_staticd'], 
											'image' => "spset.png", 
											'access' => $user_group[$member_id['user_group']]['admin_static'] 
								), 
								
								array (
											'name' => $lang['opt_clean'], 
											'url' => "?mod=clean", 
											'descr' => $lang['opt_cleanc'], 
											'image' => "clean.png", 
											'access' => "admin" 
								), 								
								
								array (
											'name' => $lang['main_newsl'], 
											'url' => "?mod=newsletter", 
											'descr' => $lang['main_newslc'], 
											'image' => "nset.png", 
											'access' => $user_group[$member_id['user_group']]['admin_newsletter'] 
								), 
								array (
											'name' => $lang['opt_vote'], 
											'url' => "?mod=editvote", 
											'descr' => $lang['opt_votec'], 
											'image' => "votes.png", 
											'access' => $user_group[$member_id['user_group']]['admin_editvote'] 
								), 
								
								array (
											'name' => $lang['opt_img'], 
											'url' => "?mod=files", 
											'descr' => $lang['opt_imgc'], 
											'image' => "iset.png", 
											'access' => "admin" 
								), 
								
								array (
											'name' => $lang['opt_banner'], 
											'url' => "?mod=banners&action=list", 
											'descr' => $lang['opt_bannerc'], 
											'image' => "rkl.png", 
											'access' => $user_group[$member_id['user_group']]['admin_banners'] 
								), 
								array (
											'name' => $lang['opt_google'], 
											'url' => "?mod=googlemap", 
											'descr' => $lang['opt_googlec'], 
											'image' => "googlemap.png", 
											'access' => $user_group[$member_id['user_group']]['admin_googlemap'] 
								),
								array (
											'name' => $lang['opt_rss'], 
											'url' => "?mod=rss", 
											'descr' => $lang['opt_rssc'], 
											'image' => "rss_import.png", 
											'access' => $user_group[$member_id['user_group']]['admin_rss'] 
								), 
								array (
											'name' => $lang['opt_rssinform'], 
											'url' => "?mod=rssinform", 
											'descr' => $lang['opt_rssinformc'], 
											'image' => "rss_inform.png", 
											'access' => $user_group[$member_id['user_group']]['admin_rssinform'] 
								),
								array (
											'name' => $lang['opt_tagscloud'], 
											'url' => "?mod=tagscloud", 
											'descr' => $lang['opt_tagscloudc'], 
											'image' => "admin_tagscloud.png", 
											'access' => $user_group[$member_id['user_group']]['admin_tagscloud'] 
								),

								array (
											'name' => $lang['opt_logs'], 
											'url' => "?mod=logs", 
											'descr' => $lang['opt_logsc'], 
											'image' => "admin_logs.png", 
											'access' => "admin" 
								),
	);

	
	foreach ( $options as $sub_options => $value ) {
		$count_options = count( $value );
		
		for($i = 0; $i < $count_options; $i ++) {

			if ($member_id['user_group'] == 1 ) continue;

			if ($member_id['user_group'] != 1 AND  $value[$i]['access'] == "admin") unset( $options[$sub_options][$i] );

			if ( !$value[$i]['access'] ) unset( $options[$sub_options][$i] );
		}
	}
	
	$subs = 0;
	
	foreach ( $options as $sub_options ) {
		
		if( $subs == 1 ) $lang['opt_hopt'] = $lang['opt_s_acc'];
		if( $subs == 2 ) $lang['opt_hopt'] = $lang['opt_s_tem'];
		if( $subs == 3 ) $lang['opt_hopt'] = $lang['opt_s_fil'];
		if( $subs == 4 ) $lang['opt_hopt'] = $lang['opt_s_oth'];
		
		$subs ++;
		
		if( ! count( $sub_options ) ) continue;
		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_hopt']}
  </div>
  <div class="list-bordered">
	<div class="row box-section">
HTML;
		
		$i = 0;
		
		foreach ( $sub_options as $option ) {
			
			if( $i > 1 ) {
				echo "</div><div class=\"row box-section\">";
				$i = 0;
			}
			
			$i ++;

			echo <<<HTML
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="{$option['url']}">
			<div class="media-left"><img src="engine/skins/images/{$option['image']}" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$option['name']}</h6>
				<span class="text-muted text-size-small">{$option['descr']}</span>
			</div>
		</a>
	  </div>
HTML;
			
		}
		
		echo <<<HTML
	</div>
  </div>
</div>
HTML;
	
	}

	$db->query( "SELECT * FROM " . PREFIX . "_admin_sections" );


	$i = 0;
	$sections = "";
		
	while ( $row = $db->get_array() ) {

		if ($row['allow_groups'] != "all") {

			$groups = explode(",", $row['allow_groups']);

			if ( !in_array($member_id['user_group'], $groups) AND $member_id['user_group'] !=1 ) continue;

		}
			
		if( $i > 1 ) {
			$sections .= "</div><div class=\"row box-section\">";
			$i = 0;
		}
			
		$i ++;

		$row['name'] = totranslit($row['name'], true, false);

		if ( !$row['icon'] OR !@file_exists( $row['icon'] )) $row['icon'] = "engine/skins/images/default_icon.png";

		$row['title'] = strip_tags(stripslashes($row['title']));
		$row['descr'] = strip_tags(stripslashes($row['descr']));

			$sections .= <<<HTML
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod={$row['name']}">
			<div class="media-left"><img src="{$row['icon']}" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$row['title']}</h6>
				<span class="text-muted text-size-small">{$row['descr']}</span>
			</div>
		</a>
	  </div>
HTML;

	}

	if ( $sections ) {


		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['admin_other_section']}
  </div>
  <div class="list-bordered">
	<div class="row box-section">
{$sections}
	</div>
  </div>
</div>
HTML;


	}

	echofooter();

} elseif( $action == "syscon" ) {

	if( $member_id['user_group'] != 1 ) {
		msg( "error", $lang['opt_denied'], $lang['opt_denied'] );
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	$parse = new ParseFilter();
	$parse->safe_mode = true;
	
	$config['offline_reason'] = str_replace( '&quot;', '"', $config['offline_reason'] );
	
	$config['offline_reason'] = $parse->decodeBBCodes( $config['offline_reason'], false );
	if( $auto_detect_config ) $config['http_home_url'] = "";

	$config['admin_allowed_ip'] = str_replace( "|", "\n", $config['admin_allowed_ip'] );

	$config['speedbar_separator'] = htmlspecialchars( $config['speedbar_separator'], ENT_QUOTES, $config['charset'] );
	$config['category_separator'] = htmlspecialchars( $config['category_separator'], ENT_QUOTES, $config['charset'] );

	echoheader( "<i class=\"fa fa-cogs position-left\"></i><span class=\"text-semibold\">{$lang['opt_all']}</span>", $lang['opt_general_sys'] );
	
	function showRow($title = "", $description = "", $field = "", $class = "") {
		
		
		echo "<tr>
        <td class=\"col-xs-6 col-sm-6 col-md-7\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
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
	
		return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";

	}
	
	if( !$handle = opendir( "./templates" ) ) {
		die( $lang['opt_errfo'] );
	}
	while ( false !== ($file = readdir( $handle )) ) {
		if( is_dir( ROOT_DIR . "/templates/$file" ) and ($file != "." and $file != "..") ) {
			$sys_con_skins_arr[$file] = $file;
		}
	}
	closedir( $handle );
	
	if( ! $handle = opendir( "./language" ) ) {
		die( "Can't load dir /language/" );
	}
	while ( false !== ($file = readdir( $handle )) ) {
		if( is_dir( ROOT_DIR . "/language/$file" ) and ($file != "." and $file != "..") ) {
			$sys_con_langs_arr[$file] = $file;
		}
	}
	closedir( $handle );
	
	foreach ( $user_group as $group )
		$sys_group_arr[$group['id']] = $group['group_name'];
	
	echo <<<HTML
<script>
<!--
        function ChangeOption(obj, selectedOption) {
		
				$("#navbar-filter li").removeClass('active');
				$(obj).parent().addClass('active');
                document.getElementById('general').style.display = "none";
                document.getElementById('security').style.display = "none";
                document.getElementById('news').style.display = "none";
                document.getElementById('comments').style.display = "none";
                document.getElementById('optimisation').style.display = "none";
                document.getElementById('files').style.display = "none";
                document.getElementById('mail').style.display = "none";
                document.getElementById('users').style.display = "none";
                document.getElementById('imagesconf').style.display = "none";
                document.getElementById('rss').style.display = "none";
                document.getElementById('smartphone').style.display = "none";
                document.getElementById(selectedOption).style.display = "";
				
				return false;

       }

		function change_domain() {
			var b = {};
		
			b[dle_act_lang[3]] = function() { 
							$(this).dialog("close");						
						};
		
			b[dle_act_lang[2]] = function() { 
							if ( $("#dle-promt-oldurl").val().length < 1) {
								 $("#dle-promt-oldurl").addClass('ui-state-error');
							} else if ( $("#dle-promt-newurl").val().length < 1 ) {
								 $("#dle-promt-oldurl").removeClass('ui-state-error');
								 $("#dle-promt-newurl").addClass('ui-state-error');
							} else {
								var oldurl = $("#dle-promt-oldurl").val();
								var newurl = $("#dle-promt-newurl").val();
	
								$(this).dialog("close");
								$("#dlepopup").remove();
	
								document.location='?mod=options&user_hash={$dle_login_hash}&action=changedomain&oldomain=' + encodeURIComponent(oldurl) + '&newdomain=' + encodeURIComponent(newurl);
	
							}				
						};
	
			$("#dlepopup").remove();
	
			$("body").append("<div id='dlepopup' title='{$lang['change_domain']}' style='display:none'>{$lang['old_domain']}<br><input type='text' name='dle-promt-oldurl' id='dle-promt-oldurl' class='classic' style='width:100%;' value='{$config['http_home_url']}'/><br><br>{$lang['new_domain']}<br><input type='text' name='dle-promt-newurl' id='dle-promt-newurl' class='classic' style='width:100%;' value=''><br><span class='text-muted text-size-small'>{$lang['change_domain_h']}</span></div>");
		
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				resizable: false,
				buttons: b
			});
	
		}
	
//-->
</script>



<!-- Toolbar -->
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
			<li class="active"><a onclick="ChangeOption(this, 'general');" class="tip" title="{$lang['opt_allsys']}"><i class="fa fa-cog"></i> {$lang['opt_b_1']}</a></li>
			<li><a onclick="ChangeOption(this,'security');" class="tip" title="{$lang['opt_secrsys']}"><i class="fa fa-shield"></i> {$lang['opt_b_2']}</a></li>
			<li><a onclick="ChangeOption(this, 'news');" class="tip" title="{$lang['opt_newssys']}"><i class="fa fa-file-text-o"></i> {$lang['opt_b_3']}</a></li>
			<li><a onclick="ChangeOption(this, 'comments');" class="tip" title="{$lang['opt_commsys']}"><i class="fa fa-commenting-o"></i> {$lang['opt_b_4']}</a></li>
			<li><a onclick="ChangeOption(this, 'optimisation');" class="tip" title="{$lang['opt_dbsys']}"><i class="fa fa-bar-chart"></i> {$lang['opt_b_5']}</a></li>
			<li><a onclick="ChangeOption(this, 'files');" class="tip" title="{$lang['opt_filesys']}"><i class="fa fa-upload"></i> {$lang['opt_b_6']}</a></li>
			<li><a onclick="ChangeOption(this,'mail');" class="tip" title="{$lang['opt_sys_mail']}"><i class="fa fa-envelope-o"></i> {$lang['opt_b_7']}</a></li>
			<li><a onclick="ChangeOption(this,'users');" class="tip" title="{$lang['opt_usersys']}"><i class="fa fa-user-circle-o"></i> {$lang['opt_b_8']}</a></li>
			<li><a onclick="ChangeOption(this,'imagesconf');" class="tip" title="{$lang['opt_imagesys']}"><i class="fa fa-picture-o"></i> {$lang['opt_b_9']}</a></li>
			<li><a onclick="ChangeOption(this,'smartphone');" class="tip" title="{$lang['opt_smartphone']}"><i class="fa fa-mobile"></i> {$lang['opt_b_10']}</a></li>
			<li><a onclick="ChangeOption(this,'rss');" class="tip" title="{$lang['opt_rsssys']}"><i class="fa fa-rss"></i> RSS</a></li>
		</ul>
	</div>
</div>
<!-- /toolbar -->
HTML;
	
	echo <<<HTML
<form action="" method="post" class="systemsettings">
<div id="general" class="panel panel-flat">
  <div class="panel-body">
    {$lang['opt_sys_all']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_ht'], $lang['opt_sys_htd'], "<input type=\"text\" class=\"form-control\" name=\"save_con[home_title]\" value=\"{$config['home_title']}\">", "white-line" );
	showRow( $lang['opt_sys_hu'], $lang['opt_sys_hud'], "<input type=\"text\" class=\"form-control\" name=\"save_con[http_home_url]\" value=\"{$config['http_home_url']}\"><a href=\"#\" onclick=\"change_domain(); return false;\">{$lang['change_domain']}</a>" );
	showRow( $lang['opt_sys_https'], $lang['opt_sys_httpsd'], makeCheckBox( "save_con[only_ssl]", "{$config['only_ssl']}" ) );
	showRow( $lang['opt_sys_descr'], $lang['opt_sys_descrd'], "<input type=\"text\" class=\"form-control\" name=\"save_con[description]\" value=\"{$config['description']}\">" );
	showRow( $lang['opt_sys_key'], $lang['opt_sys_keyd'], "<textarea class=\"classic\" style=\"width:100%;height:100px;\" name=\"save_con[keywords]\">{$config['keywords']}</textarea>" );
	showRow( $lang['opt_sys_short_name'], $lang['opt_sys_short_named'], "<input type=\"text\" class=\"form-control\" name=\"save_con[short_title]\" value=\"{$config['short_title']}\">" );
	showRow( $lang['opt_sys_sts'], $lang['opt_sys_stsd'], makeDropDown( array ("1" => $lang['opt_sys_sts1'], "2" => $lang['opt_sys_sts2'] , "3" => $lang['opt_sys_sts3']), "save_con[start_site]", "{$config['start_site']}" ) );
	showRow( $lang['opt_sys_at'], $lang['opt_sys_atd']." ".date ( $langformatdatefull, time () ), makeDropDown( $langtimezones, "save_con[date_adjust]", "{$config['date_adjust']}" ) );
	showRow( $lang['opt_sys_dc'], $lang['opt_sys_dcd'], makeCheckBox( "save_con[allow_alt_url]", "{$config['allow_alt_url']}" ) );
	showRow( $lang['opt_sys_seotype'], $lang['opt_sys_seotyped'], makeDropDown( array ("1" => $lang['opt_sys_seo_1'], "2" => $lang['opt_sys_seo_2'], "0" => $lang['opt_sys_seo_3'] ), "save_con[seo_type]", "{$config['seo_type']}" ) );
	showRow( $lang['opt_sys_seoc'], $lang['opt_sys_seocd'], makeCheckBox( "save_con[seo_control]", "{$config['seo_control']}" ) );
	showRow( $lang['opt_sys_own404'], $lang['opt_sys_own404d'], makeCheckBox( "save_con[own_404]", "{$config['own_404']}" ) );
	showRow( $lang['opt_sys_al'], $lang['opt_sys_ald'], makeDropDown( $sys_con_langs_arr, "save_con[langs]", "{$config['langs']}" ) );
	showRow( $lang['opt_sys_as'], $lang['opt_sys_asd'], makeDropDown( $sys_con_skins_arr, "save_con[skin]", "{$config['skin']}" ) );
	
	showRow( $lang['opt_sys_jqv'], $lang['opt_sys_jqvd'], makeDropDown( array ("0" => "jQuery 2.xx", "3" => "jQuery 3.xx"), "save_con[jquery_version]", "{$config['jquery_version']}" ) );
	
	showRow( $lang['opt_sys_wda'], $lang['opt_sys_wdad'], makeDropDown( array ("0" => $lang['editor_def'], "1" => "FroalaEditor (WYSIWYG)", "2" => "TinyMCE (WYSIWYG)"), "save_con[allow_admin_wysiwyg]", "{$config['allow_admin_wysiwyg']}" ) );
	showRow( $lang['opt_sys_wdst'], $lang['opt_sys_wdasd'], makeDropDown( array ("0" => $lang['editor_def'], "1" => "FroalaEditor (WYSIWYG)", "2" => "TinyMCE (WYSIWYG)" ), "save_con[allow_static_wysiwyg]", "{$config['allow_static_wysiwyg']}" ) );
	showRow( $lang['opt_sys_smc'], $lang['opt_sys_smcd'], makeCheckBox( "save_con[allow_complaint_mail]", "{$config['allow_complaint_mail']}" ) );
	showRow( $lang['opt_sys_offline'], $lang['opt_sys_offlined'], makeCheckBox( "save_con[site_offline]", "{$config['site_offline']}" ) );
	showRow( $lang['opt_sys_reason'], $lang['opt_sys_reasond'], "<textarea class=\"classic\" style=\"width:100%;height:150px;\" name=\"save_con[offline_reason]\">{$config['offline_reason']}</textarea>" );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="security" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_secrsys']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_path'], $lang['opt_sys_pathd'], "<input type=\"text\" name=\"save_con[admin_path]\" value=\"{$config['admin_path']}\" class=\"form-control\">", "white-line" );
	showRow( $lang['opt_sys_logextra'], $lang['opt_sys_logextrad'], makeDropDown( array ("0" => $lang['opt_sys_stdm'], "1" => $lang['opt_sys_extram'] ), "save_con[extra_login]", "{$config['extra_login']}" ) );
	showRow( $lang['opt_sys_dfc'], $lang['opt_sys_dfcd'], makeCheckBox( "save_con[disable_frame]", "{$config['disable_frame']}" ) );
	showRow( $lang['opt_sys_ownip'], $lang['opt_sys_ownipd'].get_ip(), "<input type=\"text\" name=\"save_con[own_ip]\" value=\"{$config['own_ip']}\" class=\"form-control\">");
	showRow( $lang['opt_sys_iprest'], $lang['opt_sys_iprestd'], "<textarea class=\"classic\" style=\"width:100%;height:100px;\" name=\"save_con[admin_allowed_ip]\">{$config['admin_allowed_ip']}</textarea>" );
	showRow( $lang['opt_sys_llog'], $lang['opt_sys_llogd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[login_log]\" value=\"{$config['login_log']}\">" );
	showRow( $lang['opt_sys_tban'], $lang['opt_sys_tband'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[login_ban_timeout]\" value=\"{$config['login_ban_timeout']}\">" );
	showRow( $lang['opt_sys_ip'], $lang['opt_sys_ipd'], makeDropDown( array ("0" => $lang['opt_sys_ipn'], "1" => $lang['opt_sys_ipm'], "2" => $lang['opt_sys_iph'] ), "save_con[ip_control]", "{$config['ip_control']}" ) );
	showRow( $lang['opt_sys_loghash'], $lang['opt_sys_loghashd'], makeCheckBox( "save_con[log_hash]", "{$config['log_hash']}" ) );
	showRow( $lang['opt_sys_recapt'], $lang['opt_sys_recaptd'], makeDropDown( array ("0" => $lang['opt_sys_gd2'], "1" => 'reCAPTCHA v2', "2" => 'reCAPTCHA v3' ), "save_con[allow_recaptcha]", "{$config['allow_recaptcha']}" ) );
	showRow( $lang['opt_sys_recaptpub'], $lang['opt_sys_recaptpubd'], "<input  type=\"text\" class=\"form-control\" name=\"save_con[recaptcha_public_key]\" value=\"{$config['recaptcha_public_key']}\">" );
	showRow( $lang['opt_sys_recaptpriv'], $lang['opt_sys_recaptpubd'], "<input  type=\"text\" class=\"form-control\" name=\"save_con[recaptcha_private_key]\" value=\"{$config['recaptcha_private_key']}\">" );
	showRow( $lang['opt_sys_recapttheme'], $lang['opt_sys_recaptthemed'], makeDropDown( array ("light" => "Light", "dark" => "Dark" ), "save_con[recaptcha_theme]", "{$config['recaptcha_theme']}" ) );
	showRow( $lang['opt_sys_mdl'], $lang['opt_sys_mdld'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[adminlog_maxdays]\" value=\"{$config['adminlog_maxdays']}\">" );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="news" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_newssys']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_newc'], $lang['opt_sys_newd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[news_number]\" value=\"{$config['news_number']}\">", "white-line" );
	showRow( $lang['opt_sys_snumc'], $lang['opt_sys_snumd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[search_number]\" value=\"{$config['search_number']}\">" );
	showRow( $lang['opt_sys_findr'], $lang['opt_sys_findrd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[search_pages]\" value=\"{$config['search_pages']}\">" );
	showRow( $lang['opt_sys_minsearch'], $lang['opt_sys_minsearchd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[search_length_min]\" value=\"{$config['search_length_min']}\">" );
	showRow( $lang['opt_sys_related_num'], $lang['opt_sys_related_numd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[related_number]\" value=\"{$config['related_number']}\">" );
	showRow( $lang['opt_sys_top_num'], $lang['opt_sys_top_numd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[top_number]\" value=\"{$config['top_number']}\">" );
	showRow( $lang['opt_sys_cloud_num'], $lang['opt_sys_cloud_numd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[tags_number]\" value=\"{$config['tags_number']}\">" );
	showRow( $lang['opt_sys_max_mod'], $lang['opt_sys_max_modd'], "<input type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[max_moderation]\" value=\"{$config['max_moderation']}\">" );
	showRow( $lang['group_n_restr'], $lang['group_n_restrd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[news_restricted]\" value=\"{$config['news_restricted']}\">" );
	showRow( $lang['opt_sys_cls'], $lang['opt_sys_clsd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[category_separator]\" value=\"{$config['category_separator']}\">" );
	showRow( $lang['opt_sys_spbs'], $lang['opt_sys_spbsd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[speedbar_separator]\" value=\"{$config['speedbar_separator']}\">" );
	showRow( $lang['opt_sys_am'], $lang['opt_sys_amd'], "<input  type=\"text\" class=\"form-control\"  name=\"save_con[smilies]\" value=\"{$config['smilies']}\" >" );
	showRow( $lang['opt_sys_emoji'], $lang['opt_sys_emojid'], makeCheckBox( "save_con[emoji]", "{$config['emoji']}" ) );
	showRow( $lang['opt_sys_an'], "<a onclick=\"javascript:Help('date'); return false;\" href=\"#\">$lang[opt_sys_and]</a>", "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[timestamp_active]\" value=\"{$config['timestamp_active']}\">" );
	showRow( $lang['opt_sys_navi'], $lang['opt_sys_navid'], makeDropDown( array ("0" => $lang['opt_sys_navi_1'], "1" => $lang['opt_sys_navi_2'], "2" => $lang['opt_sys_navi_3'], "3" => $lang['opt_sys_navi_4'] ), "save_con[news_navigation]", "{$config['news_navigation']}" ) );
	showRow( $lang['opt_sys_sort'], $lang['opt_sys_sortd'], makeDropDown( array ("date" => $lang['opt_sys_sdate'], "rating" => $lang['opt_sys_srate'], "news_read" => $lang['opt_sys_sview'], "title" => $lang['opt_sys_salph'], "comm_num" => $lang['opt_sys_scnum'] ), "save_con[news_sort]", "{$config['news_sort']}" ) );
	showRow( $lang['opt_sys_msort'], $lang['opt_sys_msortd'], makeDropDown( array ("DESC" => $lang['opt_sys_mminus'], "ASC" => $lang['opt_sys_mplus'] ), "save_con[news_msort]", "{$config['news_msort']}" ) );
	showRow( $lang['opt_sys_catsort'], $lang['opt_sys_catsortd'], makeDropDown( array ("date" => $lang['opt_sys_sdate'], "rating" => $lang['opt_sys_srate'], "news_read" => $lang['opt_sys_sview'], "title" => $lang['opt_sys_salph'], "comm_num" => $lang['opt_sys_scnum'] ), "save_con[catalog_sort]", "{$config['catalog_sort']}" ) );
	showRow( $lang['opt_sys_catmsort'], $lang['opt_sys_catmsortd'], makeDropDown( array ("DESC" => $lang['opt_sys_mminus'], "ASC" => $lang['opt_sys_mplus'] ), "save_con[catalog_msort]", "{$config['catalog_msort']}" ) );
	showRow( $lang['opt_sys_ddate'], $lang['opt_sys_ddated'], makeCheckBox( "save_con[decline_date]", "{$config['decline_date']}" ) );
	showRow( $lang['opt_sys_nfut'], $lang['opt_sys_nfutd'], makeCheckBox( "save_con[news_future]", "{$config['news_future']}" ) );
	showRow( $lang['opt_sys_amet'], $lang['opt_sys_ametd'], makeCheckBox( "save_con[create_metatags]", "{$config['create_metatags']}" ) );
	showRow( $lang['opt_sys_acat'], $lang['opt_sys_acatd'], makeCheckBox( "save_con[create_catalog]", "{$config['create_catalog']}" ) );
	showRow( $lang['opt_sys_plink'], $lang['opt_sys_plinkd'], makeCheckBox( "save_con[parse_links]", "{$config['parse_links']}" ) );
	showRow( $lang['opt_sys_nmail'], $lang['opt_sys_nmaild'], makeCheckBox( "save_con[mail_news]", "{$config['mail_news']}" ) );
	showRow( $lang['opt_sys_sub'], $lang['opt_sys_subd'], makeCheckBox( "save_con[show_sub_cats]", "{$config['show_sub_cats']}" ) );
	showRow( $lang['opt_sys_ad'], $lang['opt_sys_add'], makeCheckBox( "save_con[hide_full_link]", "{$config['hide_full_link']}" ) );
	showRow( $lang['opt_sys_asp'], $lang['opt_sys_aspd'], makeCheckBox( "save_con[allow_search_print]", "{$config['allow_search_print']}" ) );
	showRow( $lang['opt_sys_adt'], $lang['opt_sys_adtd'], makeCheckBox( "save_con[allow_add_tags]", "{$config['allow_add_tags']}" ) );
	showRow( $lang['opt_sys_mhs'], $lang['opt_sys_mhsd'], makeCheckBox( "save_con[allow_share]", "{$config['allow_share']}" ) );
	showRow( $lang['opt_sys_rfc'], $lang['opt_sys_rfcd'], makeCheckBox( "save_con[related_only_cats]", "{$config['related_only_cats']}" ) );
	showRow( $lang['opt_sys_asrate'], $lang['opt_sys_asrated'], makeCheckBox( "save_con[short_rating]", "{$config['short_rating']}" ) );
	showRow( $lang['opt_sys_rtp'], $lang['opt_sys_rtpd'], makeDropDown( array ("0" => $lang['opt_sys_rtp_1'], "1" => $lang['opt_sys_rtp_2'], "2" => $lang['opt_sys_rtp_3'], "3" => $lang['opt_sys_rtp_4']), "save_con[rating_type]", "{$config['rating_type']}" ) );
	showRow( $lang['opt_sys_wds'], $lang['opt_sys_wdsd'], makeDropDown( array ("0" => $lang['editor_def'], "1" => "FroalaEditor (WYSIWYG)", "2" => "TinyMCE (WYSIWYG)"), "save_con[allow_site_wysiwyg]", "{$config['allow_site_wysiwyg']}" ) );
	showRow( $lang['opt_sys_wdq'], $lang['opt_sys_wdsd1'], makeDropDown( array ("0" => $lang['editor_def'], "1" => "FroalaEditor (WYSIWYG)", "2" => "TinyMCE (WYSIWYG)"), "save_con[allow_quick_wysiwyg]", "{$config['allow_quick_wysiwyg']}" ) );
	showRow( $lang['opt_sys_biw'], $lang['opt_sys_biwd'], makeCheckBox( "save_con[bbimages_in_wysiwyg]", "{$config['bbimages_in_wysiwyg']}" ) );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="comments" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_sys_cch']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $lang['opt_sys_alc'], $lang['opt_sys_alcd'], makeCheckBox( "save_con[allow_comments]", "{$config['allow_comments']}" ), "white-line" );
	showRow( $lang['opt_sys_trc'], $lang['opt_sys_trcd'], makeCheckBox( "save_con[tree_comments]", "{$config['tree_comments']}" ) );
	showRow( $lang['opt_sys_trcl'], $lang['opt_sys_trcld'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[tree_comments_level]\" value=\"{$config['tree_comments_level']}\">" );
	showRow( $lang['opt_sys_trcf'], $lang['opt_sys_trcfd'], makeCheckBox( "save_con[simple_reply]", "{$config['simple_reply']}" ) );
	showRow( $lang['group_c_restr'], $lang['group_c_restrd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name=\"save_con[comments_restricted]\" value=\"{$config['comments_restricted']}\">" );
	showRow( $lang['opt_sys_subs'], $lang['opt_sys_subsd'], makeCheckBox( "save_con[allow_subscribe]", "{$config['allow_subscribe']}" ) );
	showRow( $lang['opt_sys_comb'], $lang['opt_sys_combd'], makeCheckBox( "save_con[allow_combine]", "{$config['allow_combine']}" ) );
	showRow( $lang['opt_sys_mcommd'], $lang['opt_sys_mcommdd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_comments_days]' value=\"{$config['max_comments_days']}\">" );
	showRow( $lang['opt_sys_minc'], $lang['opt_sys_mincd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[comments_minlen]' value=\"{$config['comments_minlen']}\">" );
	showRow( $lang['opt_sys_maxc'], $lang['opt_sys_maxcd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[comments_maxlen]' value=\"{$config['comments_maxlen']}\">" );
	showRow( $lang['opt_sys_cpm'], $lang['opt_sys_cpmd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[comm_nummers]' value=\"{$config['comm_nummers']}\">" );
	showRow( $lang['opt_sys_clazy'], $lang['opt_sys_clazyd'], makeCheckBox( "save_con[comments_lazyload]", "{$config['comments_lazyload']}" ) );
	showRow( $lang['opt_sys_csort'], $lang['opt_sys_csortd'], makeDropDown( array ("DESC" => $lang['opt_sys_mminus'], "ASC" => $lang['opt_sys_mplus'] ), "save_con[comm_msort]", "{$config['comm_msort']}" ) );
	showRow( $lang['opt_sys_af'], $lang['opt_sys_afd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[flood_time]' value=\"{$config['flood_time']}\">" );
	showRow( $lang['opt_sys_aw'], $lang['opt_sys_awd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[auto_wrap]' value=\"{$config['auto_wrap']}\">" );
	showRow( $lang['opt_sys_ct'], "<a onclick=\"javascript:Help('date'); return false;\" href=\"#\">$lang[opt_sys_and]</a>", "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name='save_con[timestamp_comment]' value=\"{$config['timestamp_comment']}\">" );
	showRow( $lang['opt_sys_asc'], $lang['opt_sys_ascd'], makeCheckBox( "save_con[allow_search_link]", "{$config['allow_search_link']}" ) );
	showRow( $lang['opt_sys_cmail'], $lang['opt_sys_cmaild'], makeCheckBox( "save_con[mail_comments]", "{$config['mail_comments']}" ) );	
	showRow( $lang['opt_sys_acrate'], $lang['opt_sys_acrated'], makeCheckBox( "save_con[allow_comments_rating]", "{$config['allow_comments_rating']}" ) );
	showRow( $lang['opt_sys_rtc'], $lang['opt_sys_rtcd'], makeDropDown( array ("0" => $lang['opt_sys_rtp_1'], "1" => $lang['opt_sys_rtp_2'], "2" => $lang['opt_sys_rtp_3'], "3" => $lang['opt_sys_rtp_4']), "save_con[comments_rating_type]", "{$config['comments_rating_type']}" ) );
	showRow( $lang['opt_sys_wdcom'], $lang['opt_sys_wdscomd'], makeDropDown( array ("-1" => $lang['editor_none'], "0" => $lang['editor_def'], "1" => "FroalaEditor (WYSIWYG)", "2" => "TinyMCE (WYSIWYG)" ), "save_con[allow_comments_wysiwyg]", "{$config['allow_comments_wysiwyg']}" ) );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="optimisation" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_sys_dch']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow( $lang['opt_sys_cac'], $lang['opt_sys_cad'], makeCheckBox( "save_con[allow_cache]", "{$config['allow_cache']}" ), "white-line" );
	showRow( $lang['opt_sys_cc'], $lang['opt_sys_ccd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[clear_cache]\" value=\"{$config['clear_cache']}\">" );
	showRow( $lang['opt_sys_mcac'], $lang['opt_sys_mcacd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[max_cache_pages]\" value=\"{$config['max_cache_pages']}\">" );
	showRow( $lang['opt_sys_fc'], $lang['opt_sys_fcd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\" name=\"save_con[fullcache_days]\" value=\"{$config['fullcache_days']}\">" );
	showRow( $lang['opt_sys_ctype'], $lang['opt_sys_ctyped'], makeDropDown( array ("0" => $lang['opt_sys_filec'], "1" => "Memcache" ), "save_con[cache_type]", "{$config['cache_type']}" ) );
	showRow( $lang['opt_sys_memserv'], $lang['opt_sys_memservd'], "<input  type=\"text\" name=\"save_con[memcache_server]\" value=\"{$config['memcache_server']}\" class=\"form-control\">" );
	showRow( $lang['opt_sys_ccache'], $lang['opt_sys_ccached'], makeCheckBox( "save_con[allow_comments_cache]", "{$config['allow_comments_cache']}" ) );
	showRow( $lang['opt_sys_ag'], $lang['opt_sys_agd'], makeCheckBox( "save_con[allow_gzip]", "{$config['allow_gzip']}" ) );
	showRow( $lang['opt_sys_ajsm'], $lang['opt_sys_ajsmd'], makeCheckBox( "save_con[js_min]", "{$config['js_min']}" ) );
	showRow( $lang['opt_sys_search'], $lang['opt_sys_searchd'], makeDropDown( array ("1" => $lang['opt_sys_advance'], "0" => $lang['opt_sys_simple'] ), "save_con[full_search]", "{$config['full_search']}" ) );
	showRow( $lang['opt_sys_fastsearch'], $lang['opt_sys_fastsearchd'], makeCheckBox( "save_con[fast_search]", "{$config['fast_search']}" ) );
	showRow( $lang['opt_sys_ur'], $lang['opt_sys_urd'], makeCheckBox( "save_con[allow_registration]", "{$config['allow_registration']}" ) );
	showRow( $lang['opt_sys_multiple'], $lang['opt_sys_multipled'], makeCheckBox( "save_con[allow_multi_category]", "{$config['allow_multi_category']}" ) );
	showRow( $lang['opt_sys_related'], $lang['opt_sys_relatedd'], makeCheckBox( "save_con[related_news]", "{$config['related_news']}" ) );
	showRow( $lang['opt_sys_lastview'], $lang['opt_sys_lastviewd'], makeCheckBox( "save_con[last_viewed]", "{$config['last_viewed']}" ) );
	showRow( $lang['opt_sys_nodate'], $lang['opt_sys_nodated'], makeCheckBox( "save_con[no_date]", "{$config['no_date']}" ) );
	showRow( $lang['opt_sys_afix'], $lang['opt_sys_afixd'], makeCheckBox( "save_con[allow_fixed]", "{$config['allow_fixed']}" ) );	
	showRow( $lang['opt_sys_sbar'], $lang['opt_sys_sbard'], makeCheckBox( "save_con[speedbar]", "{$config['speedbar']}" ) );
	showRow( $lang['opt_sys_ban'], $lang['opt_sys_band'], makeCheckBox( "save_con[allow_banner]", "{$config['allow_banner']}" ) );
	showRow( $lang['opt_sys_cmod'], $lang['opt_sys_cmodd'], makeCheckBox( "save_con[allow_cmod]", "{$config['allow_cmod']}" ) );
	showRow( $lang['opt_sys_voc'], $lang['opt_sys_vocd'], makeCheckBox( "save_con[allow_votes]", "{$config['allow_votes']}" ) );
	showRow( $lang['opt_sys_toc'], $lang['opt_sys_tocd'], makeCheckBox( "save_con[allow_topnews]", "{$config['allow_topnews']}" ) );
	showRow( $lang['opt_sys_rn'], $lang['opt_sys_rnd'], makeDropDown( array ("0" => $lang['opt_sys_r1'], "1" => $lang['opt_sys_r2'], "2" => $lang['opt_sys_r3'] ), "save_con[allow_read_count]", "{$config['allow_read_count']}" ) );
	showRow( $lang['cache_c'], $lang['cache_cd'], makeCheckBox( "save_con[cache_count]", "{$config['cache_count']}" ) );
	showRow( $lang['opt_sys_cnic'], $lang['opt_sys_cnicd'], makeCheckBox( "save_con[category_newscount]", "{$config['category_newscount']}" ) );

	showRow( $lang['opt_sys_dk'], $lang['opt_sys_dkd'], makeCheckBox( "save_con[allow_calendar]", "{$config['allow_calendar']}" ) );
	showRow( $lang['opt_sys_da'], $lang['opt_sys_dad'], makeCheckBox( "save_con[allow_archives]", "{$config['allow_archives']}" ) );
	showRow( $lang['opt_sys_inform'], $lang['opt_sys_informd'], makeCheckBox( "save_con[rss_informer]", "{$config['rss_informer']}" ) );
	showRow( $lang['opt_sys_tags'], $lang['opt_sys_tagsd'], makeCheckBox( "save_con[allow_tags]", "{$config['allow_tags']}" ) );
	showRow( $lang['opt_sys_change_s'], $lang['opt_sys_change_sd'], makeCheckBox( "save_con[allow_change_sort]", "{$config['allow_change_sort']}" ) );
	showRow( $lang['opt_sys_cajax'], $lang['opt_sys_cajaxd'], makeCheckBox( "save_con[comments_ajax]", "{$config['comments_ajax']}" ) );
	showRow( $lang['opt_sys_online'], $lang['opt_sys_onlined'], makeCheckBox( "save_con[online_status]", "{$config['online_status']}" ) );
	showRow( $lang['opt_sys_links'], $lang['opt_sys_linksd'], makeCheckBox( "save_con[allow_links]", "{$config['allow_links']}" ) );
	showRow( $lang['opt_sys_redirects'], $lang['opt_sys_redirectsd'], makeCheckBox( "save_con[allow_redirects]", "{$config['allow_redirects']}" ) );
	showRow( $lang['opt_sys_metatags'], $lang['opt_sys_metatagsd'], makeCheckBox( "save_con[allow_own_meta]", "{$config['allow_own_meta']}" ) );
	showRow( $lang['opt_sys_plugins'], $lang['opt_sys_pluginsd'], makeCheckBox( "save_con[allow_plugins]", "{$config['allow_plugins']}" ) );

	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="files" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_filesys']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_file'], $lang['opt_sys_filed'], makeCheckBox( "save_con[files_allow]", "{$config['files_allow']}" ), "white-line" );
	showRow( $lang['opt_sys_maxfilec'], $lang['opt_sys_maxfilecd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_file_count]' value=\"{$config['max_file_count']}\">" );
	showRow( $lang['opt_sys_file4'], $lang['opt_sys_file4d'], makeCheckBox( "save_con[files_force]", "{$config['files_force']}" ) );
	showRow( $lang['opt_sys_file3'], $lang['opt_sys_file3d'], makeCheckBox( "save_con[files_antileech]", "{$config['files_antileech']}" ) );
	showRow( $lang['opt_sys_file2'], $lang['opt_sys_file2d'], makeCheckBox("save_con[files_count]", "{$config['files_count']}" ) );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="mail" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_sys_mail']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;

	showRow( $lang['opt_sys_amail'], $lang['opt_sys_amaild'], "<input  type=\"text\" name='save_con[admin_mail]' value='{$config['admin_mail']}' class=\"form-control\" style=\"width:100%;max-width:250px\">", "white-line" );
	showRow( $lang['opt_sys_mt'], $lang['opt_sys_mtd'], "<input  type=\"text\" name='save_con[mail_title]' value=\"{$config['mail_title']}\" class=\"form-control\" style=\"width:100%;max-width:250px\">" );

	showRow( $lang['opt_sys_mm'], $lang['opt_sys_mmd'], makeDropDown( array ("php" => "PHP Mail()", "smtp" => "SMTP" ), "save_con[mail_metod]", "{$config['mail_metod']}" ) );
	showRow( $lang['opt_sys_smtph'], $lang['opt_sys_smtphd'], "<input  type=\"text\" name='save_con[smtp_host]' value=\"{$config['smtp_host']}\" class=\"form-control\" style=\"width:100%;max-width:250px\">" );
	showRow( $lang['opt_sys_smtpp'], $lang['opt_sys_smtppd'], "<input  type=\"text\" name='save_con[smtp_port]' class=\"form-control\" style=\"max-width:150px; text-align: center;\" value=\"{$config['smtp_port']}\">" );
	showRow( $lang['opt_sys_smtup'], $lang['opt_sys_smtpud'], "<input  type=\"text\" name='save_con[smtp_user]' value=\"{$config['smtp_user']}\" class=\"form-control\" style=\"width:100%;max-width:250px\">" );
	showRow( $lang['opt_sys_smtupp'], $lang['opt_sys_smtpupd'], "<input  type=\"text\" name='save_con[smtp_pass]' value=\"{$config['smtp_pass']}\" class=\"form-control\" style=\"width:100%;max-width:250px\">" );
	showRow( $lang['opt_sys_msec'], $lang['opt_sys_msecd'], makeDropDown( array ("" => $lang['opt_sys_no'], "ssl" => "SSL", "tls" => "TLS" ), "save_con[smtp_secure]", "{$config['smtp_secure']}" ) );
	showRow( $lang['opt_sys_smtpm'], $lang['opt_sys_smtpmd'], "<input  type=\"text\" name='save_con[smtp_mail]' value=\"{$config['smtp_mail']}\" class=\"form-control\" style=\"width:100%;max-width:250px\">" );
	showRow( $lang['opt_sys_mbcc'], $lang['opt_sys_mbccd'], makeCheckBox( "save_con[mail_bcc]", "{$config['mail_bcc']}" ) );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="users" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_sys_uch']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;

	showRow( $lang['opt_sys_mauth'], $lang['opt_sys_mauthd'], makeDropDown( array ("0" => $lang['opt_sys_login'], "1" => $lang['opt_sys_email'] ), "save_con[auth_metod]", $config['auth_metod'] ), "white-line" );
	showRow( $lang['opt_sys_tfa'], $lang['opt_sys_tfad'], makeCheckBox( "save_con[twofactor_auth]", "{$config['twofactor_auth']}" ) );
	showRow( $lang['opt_sys_reggroup'], $lang['opt_sys_reggroupd'], makeDropDown( $sys_group_arr, "save_con[reg_group]", $config['reg_group'] ) );
	showRow( $lang['opt_sys_ut'], $lang['opt_sys_utd'], makeDropDown( array ("0" => $lang['opt_sys_reg'], "1" => $lang['opt_sys_reg_1'] ), "save_con[registration_type]", "{$config['registration_type']}" ) );
	showRow( $lang['opt_sys_addsec'], $lang['opt_sys_addsecd'], makeDropDown( array ( "0" => $lang['opt_sys_r1'], "3" => $lang['opt_sys_r6'], "2" => $lang['opt_sys_r4'], "1" => $lang['opt_sys_r5'] ), "save_con[sec_addnews]", "{$config['sec_addnews']}" ) );
	showRow( $lang['opt_sys_sapi'], $lang['opt_sys_sapid'], "<input type=\"text\" name=\"save_con[spam_api_key]\" value=\"{$config['spam_api_key']}\" class=\"form-control\">" );
	showRow( $lang['opt_sys_pn'], $lang['opt_sys_pnd'], makeCheckBox( "save_con[profile_news]", "{$config['profile_news']}" ) );
	showRow( $lang['opt_sys_soc'], $lang['opt_sys_socd'], makeCheckBox( "save_con[allow_social]", "{$config['allow_social']}" ) );
	showRow( $lang['opt_sys_rsc'], $lang['opt_sys_rscd'], makeCheckBox( "save_con[auth_only_social]", "{$config['auth_only_social']}" ) );
	showRow( $lang['opt_sys_aasc'], $lang['opt_sys_aascd'], makeCheckBox( "save_con[allow_admin_social]", "{$config['allow_admin_social']}" ) );

	showRow( $lang['opt_sys_rmip'], $lang['opt_sys_rmipd'], makeCheckBox( "save_con[reg_multi_ip]", "{$config['reg_multi_ip']}" ) );
	showRow( $lang['opt_sys_adr'], $lang['opt_sys_adrd'], makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "save_con[auth_domain]", "{$config['auth_domain']}" ) );
	showRow( $lang['opt_sys_rules'], $lang['opt_sys_rulesd'], makeCheckBox( "save_con[registration_rules]", "{$config['registration_rules']}" ) );
	showRow( $lang['opt_sys_code'], $lang['opt_sys_coded'], makeCheckBox( "save_con[allow_sec_code]", "{$config['allow_sec_code']}" ) );
	showRow( $lang['opt_sys_question'], $lang['opt_sys_questiond'], makeCheckBox( "save_con[reg_question]", "{$config['reg_question']}" ) );
	showRow( $lang['opt_sys_sc'], $lang['opt_sys_scd'], makeCheckBox( "save_con[allow_skin_change]", "{$config['allow_skin_change']}" ) );
	showRow( $lang['opt_sys_pmail'], $lang['opt_sys_pmaild'], makeCheckBox( "save_con[mail_pm]", "{$config['mail_pm']}" ) );
	showRow( $lang['opt_sys_um'], $lang['opt_sys_umd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_users]' value=\"{$config['max_users']}\">" );
	showRow( $lang['opt_sys_ud'], $lang['opt_sys_udd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_users_day]' value=\"{$config['max_users_day']}\">" );
	
	echo "</table></div></div>";
	
	echo <<<HTML
<div id="imagesconf" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_sys_ich']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;

	showRow( $lang['opt_sys_minside'], $lang['opt_sys_minsided'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[min_up_side]' value=\"{$config['min_up_side']}\" >", "white-line" );
	
	showRow( $lang['opt_sys_maxside'], $lang['opt_sys_maxsided'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_up_side]' value=\"{$config['max_up_side']}\" >" );
	showRow( $lang['opt_sys_sdefm'], $lang['opt_sys_sdefmd'], makeDropDown( array ("0" => $lang['upload_t_seite_1'], "1" => $lang['upload_t_seite_2'], "2" => $lang['upload_t_seite_3'] ), "save_con[o_seite]", "{$config['o_seite']}" ) );
	showRow( $lang['opt_sys_maxsize'], $lang['opt_sys_maxsized'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_up_size]' value=\"{$config['max_up_size']}\">" );
	showRow( $lang['opt_sys_dim'], $lang['opt_sys_dimd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_image_days]' value=\"{$config['max_image_days']}\">" );
	showRow( $lang['opt_sys_iw'], $lang['opt_sys_iwd'], makeCheckBox( "save_con[allow_watermark]", "{$config['allow_watermark']}" ) );
	showRow( $lang['opt_sys_im'], $lang['opt_sys_imd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_watermark]' value=\"{$config['max_watermark']}\">" );
	showRow( $lang['opt_sys_wms'], $lang['opt_sys_wmsd'], makeDropDown( array ("1" => $lang['opt_sys_wms_1'], "2" => $lang['opt_sys_wms_2'], "3" => $lang['opt_sys_wms_3'], "4" => $lang['opt_sys_wms_4'] ), "save_con[watermark_seite]", "{$config['watermark_seite']}" ) );
	showRow( $lang['opt_sys_ia'], $lang['opt_sys_iad'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[max_image]' value=\"{$config['max_image']}\">" );
	showRow( $lang['opt_sys_mi'], $lang['opt_sys_mid'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[medium_image]' value=\"{$config['medium_image']}\">" );
	showRow( $lang['opt_sys_sdef'], $lang['opt_sys_sdefd'], makeDropDown( array ("0" => $lang['upload_t_seite_1'], "1" => $lang['upload_t_seite_2'], "2" => $lang['upload_t_seite_3'] ), "save_con[t_seite]", "{$config['t_seite']}" ) );
	showRow( $lang['opt_sys_ij'], $lang['opt_sys_ijd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[jpeg_quality]' value=\"{$config['jpeg_quality']}\">" );
	showRow( $lang['opt_sys_av'], $lang['opt_sys_avd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[avatar_size]' value=\"{$config['avatar_size']}\">" );

	showRow( $lang['opt_sys_imw'], $lang['opt_sys_imwd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[tag_img_width]' value=\"{$config['tag_img_width']}\">" );
	showRow( $lang['opt_sys_align'], $lang['opt_sys_alignd'], makeDropDown( array ("" => $lang['opt_sys_none'], "left" => $lang['opt_sys_left'], "center" => $lang['opt_sys_center'], "right" => $lang['opt_sys_right'] ), "save_con[image_align]", "{$config['image_align']}" ) );
	showRow( $lang['opt_sys_dimm'], $lang['opt_sys_dimmd'], makeCheckBox( "save_con[thumb_dimming]", "{$config['thumb_dimming']}" ) );
	showRow( $lang['opt_sys_gall'], $lang['opt_sys_galld'], makeCheckBox( "save_con[thumb_gallery]", "{$config['thumb_gallery']}" ) );
	showRow( $lang['opt_sys_sim'], $lang['opt_sys_simd'], makeDropDown( array ("0" => $lang['outline_1'], "1" => $lang['outline_2'], "2" => $lang['outline_3'], "3" => $lang['outline_4'] ), "save_con[outlinetype]", "{$config['outlinetype']}" ) );
	showRow( $lang['opt_sys_laz'], $lang['opt_sys_lazd'], makeCheckBox( "save_con[image_lazy]", "{$config['image_lazy']}" ) );
	showRow( $lang['opt_sys_tiny'], $lang['opt_sys_tinyd'], makeCheckBox( "save_con[image_tinypng]", "{$config['image_tinypng']}" ) );
	showRow( $lang['opt_sys_tinykey'], $lang['opt_sys_tinykeyd'], "<input type=\"text\" name=\"save_con[tinypng_key]\" value=\"{$config['tinypng_key']}\" class=\"form-control\">" );
	showRow( $lang['opt_sys_tinyres'], $lang['opt_sys_tinyresd'], makeCheckBox( "save_con[tinypng_resize]", "{$config['tinypng_resize']}" ) );
	showRow( $lang['opt_sys_tinyav'], $lang['opt_sys_tinyavd'], makeCheckBox( "save_con[tinypng_avatar]", "{$config['tinypng_avatar']}" ) );
	
	echo "</table></div></div>";


	echo <<<HTML
<div id="smartphone" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_smartphone']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_smart'], $lang['opt_sys_smartd'], makeCheckBox( "save_con[allow_smartphone]", "{$config['allow_smartphone']}" ), "white-line" );
	showRow( $lang['opt_sys_sm_im'], $lang['opt_sys_sm_imd'], makeCheckBox( "save_con[allow_smart_images]", "{$config['allow_smart_images']}" ) );
	showRow( $lang['opt_sys_sm_iv'], $lang['opt_sys_sm_ivd'], makeCheckBox( "save_con[allow_smart_video]", "{$config['allow_smart_video']}" ) );
	showRow( $lang['opt_sys_sm_fm'], $lang['opt_sys_sm_fmd'], makeCheckBox( "save_con[allow_smart_format]", "{$config['allow_smart_format']}" ) );
	showRow( $lang['opt_sys_sm_n'], $lang['opt_sys_sm_nd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[mobile_news]' value=\"{$config['mobile_news']}\">" );
	
	echo "</table></div></div>";

	
	echo <<<HTML
<div id="rss" class="panel panel-flat" style='display:none'>
  <div class="panel-body">
    {$lang['opt_rsssys']}
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	
	showRow( $lang['opt_sys_arss'], $lang['opt_sys_arssd'], makeCheckBox( "save_con[allow_rss]", "{$config['allow_rss']}" ), "white-line" );
	showRow( $lang['opt_sys_trss'], $lang['opt_sys_trssd'], makeDropDown( array ("0" => $lang['opt_sys_rss_type_0'], "1" => $lang['opt_sys_rss_type_1'] ), "save_con[rss_mtype]", "{$config['rss_mtype']}" ) );
	showRow( $lang['opt_sys_nrss'], $lang['opt_sys_nrssd'], "<input  type=\"text\" class=\"form-control\" style=\"max-width:150px; text-align: center;\"  name='save_con[rss_number]' value=\"{$config['rss_number']}\">" );
	showRow( $lang['opt_sys_frss'], $lang['opt_sys_frssd'], makeDropDown( array ("0" => $lang['opt_sys_rss_type_2'], "1" => $lang['opt_sys_rss_type_3'], "2" => $lang['opt_sys_rss_type_4'] ), "save_con[rss_format]", "{$config['rss_format']}" ) );
	
	showRow( $lang['opt_sys_ayd'], $lang['opt_sys_aydd'], makeCheckBox( "save_con[allow_yandex_dzen]", "{$config['allow_yandex_dzen']}" ) );
	showRow( $lang['opt_sys_ayt'], $lang['opt_sys_aytd'], makeCheckBox( "save_con[allow_yandex_turbo]", "{$config['allow_yandex_turbo']}" ) );

	echo "</table></div></div>";

	if(!is_writable(ENGINE_DIR . '/data/config.php')) {

		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">".str_replace("{file}", "engine/data/config.php", $lang['stat_system'])."</div>";

	}
	
	echo <<<HTML
<div style="margin-bottom:30px;">
<input type="hidden" name="mod" value="options">
<input type="hidden" name="action" value="dosavesyscon">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>
</form>
HTML;
	
	echofooter();

} elseif( $action == "changedomain" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( $member_id['user_group'] != 1 ) {
		msg( "error", $lang['opt_denied'], $lang['opt_denied'] );
	}

	$newdomain = htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_GET['newdomain'] ) ) ) ), ENT_QUOTES, $config['charset'] );
	$newdomain = str_replace( "$", "&#036;", $newdomain );
	$newdomain = str_replace( "{", "&#123;", $newdomain );
	$newdomain = str_replace( "}", "&#125;", $newdomain );
	$newdomain = str_replace( chr(0), "", $newdomain );
	$newdomain = str_replace( chr(92), "", $newdomain );

	$oldomain = htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_GET['oldomain'] ) ) ) ), ENT_QUOTES, $config['charset'] );
	$oldomain = str_replace( "$", "&#036;", $oldomain );
	$oldomain = str_replace( "{", "&#123;", $oldomain );
	$oldomain = str_replace( "}", "&#125;", $oldomain );
	$oldomain = str_replace( chr(0), "", $oldomain );
	$oldomain = str_replace( chr(92), "", $oldomain );
	
	if (substr ( $oldomain, - 1, 1 ) != '/') $oldomain .= '/';
	if (substr ( $newdomain, - 1, 1 ) != '/') $newdomain .= '/';

	if (strpos($oldomain, "//") === 0) $avatar_url = $oldomain;
	elseif (strpos($oldomain, "/") === 0) $avatar_url = "//".$_SERVER['HTTP_HOST'].$oldomain;
	else $avatar_url = $oldomain;

	$avatar_url = str_ireplace("https:", "", $avatar_url);
	$avatar_url = str_ireplace("http:", "", $avatar_url);

	if (strpos($newdomain, "//") === 0) $new_avatar_url = $newdomain;
	elseif (strpos($newdomain, "/") === 0) $new_avatar_url = "//".$_SERVER['HTTP_HOST'].$newdomain;
	else $new_avatar_url = $newdomain;

	$new_avatar_url = str_ireplace("https:", "", $new_avatar_url);
	$new_avatar_url = str_ireplace("http:", "", $new_avatar_url);

	$db->query("UPDATE `" . USERPREFIX . "_users` SET `foto`=REPLACE(`foto`,'{$avatar_url}','{$new_avatar_url}')");
	$db->query("UPDATE `" . PREFIX . "_post` SET `short_story`=REPLACE(`short_story`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . PREFIX . "_post` SET `full_story`=REPLACE(`full_story`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . PREFIX . "_post` SET `xfields`=REPLACE(`xfields`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . PREFIX . "_comments` SET `text`=REPLACE(`text`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . USERPREFIX . "_pm` SET `text`=REPLACE(`text`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . PREFIX . "_static` SET `template`=REPLACE(`template`,'{$oldomain}','{$newdomain}')");
	$db->query("UPDATE `" . PREFIX . "_banners` SET `code`=REPLACE(`code`,'{$oldomain}','{$newdomain}')");

	$config['http_home_url'] = $newdomain;
			
	$handler = fopen( ENGINE_DIR . '/data/config.php', "w" );
	fwrite( $handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n" );
	foreach ( $config as $name => $value ) {
		fwrite( $handler, "'{$name}' => \"{$value}\",\n\n" );
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );
			
	if (function_exists('opcache_reset')) {
		opcache_reset();
	}
	
	msg( "success", $lang['opt_sysok'], $lang['opt_sysok_1'], "?mod=options&action=syscon" );
		
} elseif( $action == "dosavesyscon" ) {
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( $member_id['user_group'] != 1 ) {
		msg( "error", $lang['opt_denied'], $lang['opt_denied'] );
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '48', '')" );
	
	$save_con = $_POST['save_con'];
	
	$save_con['charset'] = "utf-8";
	$save_con['seo_control'] = intval($save_con['seo_control']);
	$save_con['allow_complaint_mail'] = intval($save_con['allow_complaint_mail']);
	$save_con['site_offline'] = intval($save_con['site_offline']);
	$save_con['allow_alt_url'] = intval($save_con['allow_alt_url']);
	$save_con['log_hash'] = intval($save_con['log_hash']);
	$save_con['news_future'] = intval($save_con['news_future']);
	$save_con['create_metatags'] = intval($save_con['create_metatags']);
	$save_con['create_catalog'] = intval($save_con['create_catalog']);
	$save_con['parse_links'] = intval($save_con['parse_links']);
	$save_con['mail_news'] = intval($save_con['mail_news']);
	$save_con['show_sub_cats'] = intval($save_con['show_sub_cats']);
	$save_con['short_rating'] = intval($save_con['short_rating']);
	$save_con['allow_search_print'] = intval($save_con['allow_search_print']);
	$save_con['allow_add_tags'] = intval($save_con['allow_add_tags']);
	$save_con['allow_share'] = intval($save_con['allow_share']);
	$save_con['related_only_cats'] = intval($save_con['related_only_cats']);
	$save_con['hide_full_link'] = intval($save_con['hide_full_link']);
	$save_con['allow_subscribe'] = intval($save_con['allow_subscribe']);
	$save_con['allow_combine'] = intval($save_con['allow_combine']);
	$save_con['allow_search_link'] = intval($save_con['allow_search_link']);
	$save_con['mail_comments'] = intval($save_con['mail_comments']);
	$save_con['allow_comments'] = intval($save_con['allow_comments']);
	$save_con['allow_comments_cache'] = intval($save_con['allow_comments_cache']);
	$save_con['js_min'] = intval($save_con['js_min']);
	$save_con['fast_search'] = intval($save_con['fast_search']);
	$save_con['allow_multi_category'] = intval($save_con['allow_multi_category']);
	$save_con['related_news'] = intval($save_con['related_news']);
	$save_con['no_date'] = intval($save_con['no_date']);
	$save_con['allow_fixed'] = intval($save_con['allow_fixed']);
	$save_con['speedbar'] = intval($save_con['speedbar']);
	$save_con['allow_banner'] = intval($save_con['allow_banner']);
	$save_con['allow_cmod'] = intval($save_con['allow_cmod']);
	$save_con['cache_count'] = intval($save_con['cache_count']);
	$save_con['rss_informer'] = intval($save_con['rss_informer']);
	$save_con['allow_tags'] = intval($save_con['allow_tags']);
	$save_con['allow_change_sort'] = intval($save_con['allow_change_sort']);
	$save_con['comments_ajax'] = intval($save_con['comments_ajax']);
	$save_con['online_status'] = intval($save_con['online_status']);
	$save_con['allow_links'] = intval($save_con['allow_links']);
	$save_con['allow_cache'] = intval($save_con['allow_cache']);
	$save_con['allow_gzip'] = intval($save_con['allow_gzip']);
	$save_con['allow_registration'] = intval($save_con['allow_registration']);
	$save_con['allow_votes'] = intval($save_con['allow_votes']);
	$save_con['allow_topnews'] = intval($save_con['allow_topnews']);
	$save_con['allow_calendar'] = intval($save_con['allow_calendar']);
	$save_con['allow_archives'] = intval($save_con['allow_archives']);
	$save_con['files_allow'] = intval($save_con['files_allow']);
	$save_con['files_count'] = intval($save_con['files_count']);
	$save_con['allow_sec_code'] = intval($save_con['allow_sec_code']);
	$save_con['allow_skin_change'] = intval($save_con['allow_skin_change']);
	$save_con['allow_watermark'] = intval($save_con['allow_watermark']);
	$save_con['files_force'] = intval($save_con['files_force']);
	$save_con['files_antileech'] = intval($save_con['files_antileech']);
	$save_con['use_admin_mail'] = intval($save_con['use_admin_mail']);
	$save_con['mail_bcc'] = intval($save_con['mail_bcc']);
	$save_con['reg_multi_ip'] = intval($save_con['reg_multi_ip']);
	$save_con['registration_rules'] = intval($save_con['registration_rules']);
	$save_con['reg_question'] = intval($save_con['reg_question']);
	$save_con['mail_pm'] = intval($save_con['mail_pm']);
	$save_con['thumb_dimming'] = intval($save_con['thumb_dimming']);
	$save_con['thumb_gallery'] = intval($save_con['thumb_gallery']);
	$save_con['allow_smartphone'] = intval($save_con['allow_smartphone']);
	$save_con['allow_smart_images'] = intval($save_con['allow_smart_images']);
	$save_con['allow_smart_video'] = intval($save_con['allow_smart_video']);
	$save_con['allow_smart_format'] = intval($save_con['allow_smart_format']);
	$save_con['allow_rss'] = intval($save_con['allow_rss']);
	$save_con['comments_lazyload'] = intval($save_con['comments_lazyload']);
	$save_con['adminlog_maxdays'] = intval($save_con['adminlog_maxdays']);
	$save_con['allow_social'] = intval($save_con['allow_social']);
	$save_con['auth_only_social'] = intval($save_con['auth_only_social']);
	$save_con['allow_comments_rating'] = intval($save_con['allow_comments_rating']);
	$save_con['tree_comments'] = intval($save_con['tree_comments']);
	$save_con['tree_comments_level'] = intval($save_con['tree_comments_level']);
	$save_con['simple_reply'] = intval($save_con['simple_reply']);
	$save_con['profile_news'] = intval($save_con['profile_news']);
	$save_con['twofactor_auth'] = intval($save_con['twofactor_auth']);
	$save_con['category_newscount'] = intval($save_con['category_newscount']);
	$save_con['only_ssl'] = intval($save_con['only_ssl']);
	$save_con['allow_redirects'] = intval($save_con['allow_redirects']);
	$save_con['allow_own_meta'] = intval($save_con['allow_own_meta']);
	$save_con['bbimages_in_wysiwyg'] = intval($save_con['bbimages_in_wysiwyg']);
	$save_con['own_404'] = intval($save_con['own_404']);
	$save_con['disable_frame'] = intval($save_con['disable_frame']);
	$save_con['allow_plugins'] = intval($save_con['allow_plugins']);
	$save_con['allow_admin_social'] = intval($save_con['allow_admin_social']);
	$save_con['image_lazy'] = intval($save_con['image_lazy']);
	$save_con['search_length_min'] = intval($save_con['search_length_min']);
	$save_con['decline_date'] = intval($save_con['decline_date']);
	$save_con['allow_yandex_dzen'] = intval($save_con['allow_yandex_dzen']);
	$save_con['allow_yandex_turbo'] = intval($save_con['allow_yandex_turbo']);
	$save_con['emoji'] = intval($save_con['emoji']);
	$save_con['last_viewed'] = intval($save_con['last_viewed']);
	$save_con['image_tinypng'] = intval($save_con['image_tinypng']);
	$save_con['tinypng_avatar'] = intval($save_con['tinypng_avatar']);
	$save_con['tinypng_resize'] = intval($save_con['tinypng_resize']);
	
	if( $save_con['adminlog_maxdays'] < 30 ) $save_con['adminlog_maxdays'] = 30;
	if( $save_con['comments_maxlen'] > 65000 ) $save_con['comments_maxlen'] = 65000;
	
	if (substr( trim($save_con['http_home_url']), - 1, 1 ) != '/') $save_con['http_home_url'] .= '/';
	
	if( $save_con['only_ssl'] ) {
		$save_con['http_home_url'] = str_replace( "http://", "https://", $save_con['http_home_url'] );
	}
	
	$save_con['offline_reason'] = trim(strip_tags(stripslashes( $save_con['offline_reason'] )));
	$save_con['offline_reason'] = htmlspecialchars( $save_con['offline_reason'], ENT_QUOTES, $config['charset']);
	$save_con['offline_reason'] = str_replace( "\r", '', $save_con['offline_reason'] );
	$save_con['offline_reason'] = str_replace( "\n", '<br>', $save_con['offline_reason'] );

	$save_con['admin_allowed_ip'] = str_replace( "\r", "", trim( $save_con['admin_allowed_ip'] ) );
	$save_con['admin_allowed_ip'] = str_replace( "\n", "|", $save_con['admin_allowed_ip'] );


	$temp_array = explode ("|", $save_con['admin_allowed_ip']);
	$allowed_ip	= array();
	
	if (count($temp_array)) {
	
		foreach ( $temp_array as $value ) {
			$value1 = str_replace( "*", "0", trim($value) );
			$value1 = explode ('/', $value1);
					
			$value1 = ip2long($value1[0]);
	
			if( $value1 != -1 AND $value1 !== FALSE ) $allowed_ip[] = trim( $value );
		}
		
	}
	
	if ( count($allowed_ip) ) $save_con['admin_allowed_ip'] = implode("|", $allowed_ip); else $save_con['admin_allowed_ip'] = "";

	if($save_con['related_number'] != $config['related_number']) {
		$db->query( "UPDATE " . PREFIX . "_post_extras SET related_ids=''" );
	}

	$find = array();
	$replace = array();
	
	$find[] = "'\r'";
	$replace[] = "";
	$find[] = "'\n'";
	$replace[] = "";

	if( $auto_detect_config ) $config['http_home_url'] = "";
	
	$save_con = $save_con + $config;

	$handler = fopen( ENGINE_DIR . '/data/config.php', "w" );
	
	fwrite( $handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n" );
	foreach ( $save_con as $name => $value ) {
		
		if( $name != "offline_reason" ) {
			
			$value = trim( strip_tags(stripslashes( $value )) );
			$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
			
			$name = trim( strip_tags(stripslashes( $name )) );
			$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
	
		}

		if( $name == "speedbar_separator" OR $name == "category_separator") {
			$value = str_replace( '&amp;', '&', $value );
		}
		
		$value = preg_replace( $find, $replace, $value );
		$value = str_replace( "$", "&#036;", $value );
		$value = str_replace( "{", "&#123;", $value );
		$value = str_replace( "}", "&#125;", $value );
		$value = str_replace( chr(0), "", $value );
		$value = str_replace( chr(92), "", $value );
		$value = str_ireplace( "decode", "dec&#111;de", $value );
		
		$name = preg_replace( $find, $replace, $name );
		$name = str_replace( "$", "&#036;", $name );
		$name = str_replace( "{", "&#123;", $name );
		$name = str_replace( "}", "&#125;", $name );
		$name = str_replace( chr(0), "", $name );
		$name = str_replace( chr(92), "", $name );
		$name = str_replace( '(', "", $name );
		$name = str_replace( ')', "", $name );
		$name = str_ireplace( "decode", "dec&#111;de", $name );
		
		fwrite( $handler, "'{$name}' => '{$value}',\n\n" );
	
	}
	fwrite( $handler, ");\n\n?>" );
	fclose( $handler );
	
	clear_cache();
	
	if (function_exists('opcache_reset')) {
		opcache_reset();
	}
	
	msg( "success", $lang['opt_sysok'], $lang['opt_sysok_1'], "?mod=options&action=syscon" );
}

?>