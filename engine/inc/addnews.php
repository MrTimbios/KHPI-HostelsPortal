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
 File: addnews.php
-----------------------------------------------------
 Use: Add news
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_addnews'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( $action == "addnews" ) {

	$id= "";
	
	if( $config['allow_admin_wysiwyg'] == 1 ) {
		$js_array[] = "engine/skins/codemirror/js/code.js";
		$js_array[] = "engine/editor/jscripts/froala/editor.js";
		$js_array[] = "engine/editor/jscripts/froala/languages/{$lang['wysiwyg_language']}.js";
		$css_array[] = "engine/editor/jscripts/froala/css/editor.css";
	}
	
	if( $config['allow_admin_wysiwyg'] == 2 ) {
		$js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";
	}
	
	if( !$config['allow_admin_wysiwyg'] ) {
		$js_array[] = "engine/classes/js/typograf.min.js";
	}
	
	$js_array[] = "engine/classes/js/sortable.js";
	$js_array[] = "engine/classes/uploads/html5/fileuploader.js";
	
	echoheader( "<i class=\"fa fa-file-text-o position-left\"></i><span class=\"text-semibold\">{$lang['header_n_title']}</span>", $lang['addnews'] );

	if ( !$user_group[$member_id['user_group']]['allow_html'] ) $config['allow_admin_wysiwyg'] = 0;	

	if( $config['allow_admin_wysiwyg'] == "2" ) $save = "tinyMCE.triggerSave();"; else $save = "";

	$xfieldsaction = "categoryfilter";
	include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
	echo $categoryfilter;
	

	echo "
    <script>
    function preview(){";

	if( $config['allow_admin_wysiwyg'] == 2 ) {
		echo "document.getElementById('short_story').value = $('#short_story').html();
	document.getElementById('full_story').value = $('#full_story').html();";
	}
	
	echo "if(document.addnews.title.value == ''){ 			Growl.error({
				title: '{$lang['p_info']}',
				text: '{$lang['addnews_alert']}'
			}); return false; }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1')
        document.addnews.mod.value='preview';document.addnews.target='prv'
        document.addnews.submit();dd.focus()
        setTimeout(\"document.addnews.mod.value='addnews';document.addnews.target='_self'\",500)
    }
    }

	function auto_keywords ( key )
	{

		var wysiwyg = '{$config['allow_admin_wysiwyg']}';

		if (wysiwyg == \"2\") {
			tinyMCE.triggerSave();
		}

		var short_txt = document.getElementById('short_story').value;
		var full_txt = document.getElementById('full_story').value;

		ShowLoading('');

		$.post(\"engine/ajax/controller.php?mod=keywords\", { short_txt: short_txt, full_txt: full_txt, key: key, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');

			if (key == 1) { $('#autodescr').val(data); }
			else { $('#keywords').tokenfield('setTokens', data); }
	
		});

		return false;
	}

	function find_related_ids (){

		var wysiwyg = '{$config['allow_admin_wysiwyg']}';

		if (wysiwyg == \"2\") {
			tinyMCE.triggerSave();
		}
		
		var title = document.getElementById('title').value;
		var short_txt = document.getElementById('short_story').value;
		var full_txt = document.getElementById('full_story').value;

		ShowLoading('');

		$.post(\"engine/ajax/controller.php?mod=adminfunction\", { action: 'relatedids', title: title, short_txt: short_txt, full_txt: full_txt, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');

			$('#related_ids').val(data);
	
		});

		return false;
	}

    function confirmDelete(url, id){

		var b = {};
	
		b[dle_act_lang[1]] = function() { 
						$(this).dialog(\"close\");						
				    };

		b['{$lang['p_message']}'] = function() { 
						$(this).dialog(\"close\");

						var bt = {};
					
						bt[dle_act_lang[3]] = function() { 
										$(this).dialog('close');						
								    };
					
						bt['{$lang['p_send']}'] = function() { 
										if ( $('#dle-promt-text').val().length < 1) {
											 $('#dle-promt-text').addClass('ui-state-error');
										} else {
											var response = $('#dle-promt-text').val()
											$(this).dialog('close');
											$('#dlepopup').remove();
											$.post('engine/ajax/controller.php?mod=message', { id: id,  text: response, user_hash: '{$dle_login_hash}' },
											  function(data){
											    if (data == 'ok') { document.location=url; } else { DLEalert('{$lang['p_not_send']}', '{$lang['p_info']}'); }
										  });
	
										}				
									};
					
						$('#dlepopup').remove();
					
						$('body').append(\"<div id='dlepopup' title='{$lang['p_title']}' style='display:none'><br />{$lang['p_text']}<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'></textarea></div>\");
					
						$('#dlepopup').dialog({
							autoOpen: true,
							width: 500,
							resizable: false,
							buttons: bt
						});
					
				    };
	
		b[dle_act_lang[0]] = function() { 
						$(this).dialog(\"close\");
						document.location=url;					
					};
	
		$(\"#dlepopup\").remove();
	
		$(\"body\").append(\"<div id='dlepopup' title='{$lang['p_confirm']}' style='display:none'><br /><div id='dlepopupmessage'>{$lang['edit_cdel']}</div></div>\");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 500,
			resizable: false,
			buttons: b
		});


    }

	function find_relates ( )
	{
		var title = document.getElementById('title').value;

		ShowLoading('');

		$.post('engine/ajax/controller.php?mod=find_relates', { title: title, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');
	
			$('#related_news').html(data);
	
		});

		return false;

	};

	
	function xfimagedelete( xfname, xfvalue )
	{
		
		DLEconfirm( '{$lang['image_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
			
			$.post('engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$row['id']}', author: '{$author}', 'images[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
			});
			
		} );

		return false;

	};
	
	function xfaddalt( id, xfname ) {
	
		var sel_alt = $('#xf_'+id).data('alt').toString().trim();
		sel_alt = sel_alt.replace(/\"/g, '&quot;');
		
		DLEprompt('{$lang['bb_alt_image']}', sel_alt, '{$lang['p_prompt']}', function (r) {
			r = r.replace(/</g, '');
			r = r.replace(/>/g, '');
			r = r.replace(/,/g, '&#44;');
			
			$('#xf_'+id).data('alt', r);
			xfsinc(xfname);
		
		}, true);
		
	};
	
	function xfsinc(xfname) {
	
		var order = [];
		
		$( '#uploadedfile_' + xfname + ' .uploadedfile' ).each(function() {
			var xfurl = $(this).data('id').toString().trim();
			var xfalt = $(this).data('alt').toString().trim();
			
			if(xfalt) {
				order.push(xfalt + '|'+ xfurl);
			} else {
				order.push(xfurl);
			}

		});
	
		$('#xf_' + xfname).val(order.join(','));
	};
	
	function xffiledelete( xfname, xfvalue )
	{
		DLEconfirm( '{$lang['file_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
	
			$.post('engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', 'files[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xf_'+xfname).hide('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
				
			});
			
		} );
		
		return false;

	};
	
	function checkxf ( )
	{

		var status = '';
		var xfempty = false;

		{$save}

		$('[uid=\"essential\"]:visible').each(function(indx) {

			if($.trim($(this).find('[rel=\"essential\"]').val()).length < 1) {
				xfempty = true;
				status = 'fail';
			}

		});

		if(xfempty) {
			Growl.error({
				title: '{$lang['p_info']}',
				text: '{$lang['addnews_xf_alert']}'
			});
		}

		if(document.addnews.title.value == ''){

			Growl.error({
				title: '{$lang['p_info']}',
				text: '{$lang['addnews_alert']}'
			});

			status = 'fail';

		}

		return status;

	};
	
	function moveCategoryChange(obj) {
  
	  var value = $(obj).val();
  
	  if (value == 5) {
		$('#movecatlist').show();
	  } else {
		$('#movecatlist').hide();
	  }
	  
	}
	
	function onPassChange(obj) {
  
	  var value = obj.checked;
	  
	  if (value == true) {
		$('#passlist').show();
	  } else {
		$('#passlist').hide();
	  }
	  
	}
	

	$(function(){

		$('#tags').tokenfield({
		  autocomplete: {
		    source: 'engine/ajax/controller.php?mod=find_tags&user_hash={$dle_login_hash}',
			minLength: 3,
		    delay: 500
		  },
		  createTokensOnBlur:true
		});

		$('[data-rel=links]').tokenfield({
		  autocomplete: {
		    source: 'engine/ajax/controller.php?mod=find_tags&user_hash={$dle_login_hash}&mode=xfield',
			minLength: 3,
		    delay: 500
		  },
		  createTokensOnBlur:true
		});

		$('.categoryselect').chosen({no_results_text: '{$lang['addnews_cat_fault']}'});

	});
    </script>";
		
	$categories_list = CategoryNewsSelection( 0, 0 );

	if( $config['allow_multi_category'] ) {
		$category_multiple = "class=\"categoryselect\" multiple";
	} else {
		$category_multiple = "class=\"uniform\" data-live-search=\"true\" data-none-results-text=\"{$lang['addnews_cat_fault']}\" data-width=\"350\"";
	}


	if( $member_id['user_group'] == 1 ) {
		
		$author_info = "<span class=\"position-left visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs\">{$lang['edit_eau']}</span><input type=\"text\" name=\"new_author\" class=\"form-control\" style=\"width:190px;\" value=\"{$member_id['name']}\">";
	
	} else {
		
		$author_info = "";
	
	}

echo <<<HTML
<div class="panel panel-default">
		
		    <div class="panel-heading">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#tabhome" data-toggle="tab"><i class="fa fa-home position-left"></i> {$lang['tabs_news']}</a></li>
					<li><a href="#tabvote" data-toggle="tab"><i class="fa fa-bar-chart position-left"></i> {$lang['tabs_vote']}</a></li>
					<li><a href="#tabextra" data-toggle="tab"><i class="fa fa-tasks position-left"></i> {$lang['tabs_extra']}</a></li>
					<li id="tab-perimit"><a href="#tabperm" data-toggle="tab"><i class="fa fa-lock position-left"></i> {$lang['tabs_perm']}</a></li>
				</ul>
                <div class="heading-elements">
	                <ul class="icons-list">
						<li><a href="#" class="panel-fullscreen"><i class="fa fa-expand"></i></a></li>
					</ul>
                </div>
			</div>
			
			<form method="post" name="addnews" id="addnews" onsubmit="if(checkxf()=='fail') return false;" class="form-horizontal">
                 <div class="panel-tab-content tab-content">			
                     <div class="tab-pane active" id="tabhome">
						<div class="panel-body">
						
							<div class="form-group">
							  <label class="control-label col-sm-2">{$lang['edit_et']}</label>
							  <div class="col-sm-10">
								<input type="text" class="form-control width-550 position-left" name="title" id="title" maxlength="250" ><button onclick="find_relates(); return false;" class="visible-lg-inline-block btn bg-info-800 btn-sm btn-raised">{$lang['b_find_related']}</button><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_title']}"></i> <span id="related_news"></span>
							  </div>	
							</div>
							 
							 <div class="form-group">
							  <label class="control-label col-sm-2">{$lang['addnews_date']}</label>
							  <div class="col-sm-10">
								<input data-rel="calendar" type="text" name="newdate" class="form-control" style="width:190px;" autocomplete="off"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_calendar']}" ></i>{$author_info}
							  </div>
							</div>
							
							 <div class="form-group">
							  <label class="control-label col-sm-2">{$lang['addnews_cat']}</label>
							  <div class="col-sm-10">
								<select data-placeholder="{$lang['addnews_cat_sel']}" title="{$lang['addnews_cat_sel']}" name="category[]" id="category" onchange="onCategoryChange(this)" $category_multiple style="width:100%;max-width:350px;">{$categories_list}</select>
							  </div>
							</div>

							 <div class="form-group editor-group">
							  <label class="control-label col-md-2">{$lang['addnews_short']}</label>
							  <div class="col-md-10">
HTML;

	if( $config['allow_admin_wysiwyg'] ) {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/editor/shortnews.php'));
	
	} else {

		$bb_editor = true;
		include (DLEPlugins::Check(ENGINE_DIR . '/inc/include/inserttag.php'));
		echo "<div class=\"editor-panel\"><div class=\"shadow-depth1\">{$bb_code}<textarea class=\"editor\" style=\"width:100%;height:300px;\" onfocus=\"setFieldName(this.name)\" name=\"short_story\" id=\"short_story\"></textarea></div></div>";
	}

echo <<<HTML
							  </div>
							</div>
							
							 <div class="form-group editor-group">
							  <label class="control-label col-md-2">{$lang['addnews_full']}</label>
							  <div class="col-md-10">
HTML;

	if( $config['allow_admin_wysiwyg'] ) {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/editor/fullnews.php'));
	
	} else {

		echo "<div class=\"editor-panel\"><div class=\"shadow-depth1\">{$bb_panel}<textarea class=\"editor\" style=\"width:100%;height:350px;\" onfocus=\"setFieldName(this.name)\" name=\"full_story\" id=\"full_story\"></textarea></div></div>";
	}
	
	// XFields Call
	$xfieldsaction = "list";
	$xfieldsadd = true;
	$news_id = 0;
	$author = urlencode($member_id['name']);
	include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
	// End XFields Call

	if( !$config['allow_admin_wysiwyg'] ) $output = str_replace("<!--panel-->", $bb_panel, $output);

	
	if( $user_group[$member_id['user_group']]['allow_fixed'] and $config['allow_fixed'] ) $fix_input = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" id=\"news_fixed\" name=\"news_fixed\" value=\"1\">{$lang['addnews_fix']}</label></div>"; else $fix_input = "";
	if( $user_group[$member_id['user_group']]['allow_main'] ) $main_input = "<div class=\"checkbox\" id=\"opt_holder_main\"><label><input class=\"icheck\" type=\"checkbox\" id=\"allow_main\" name=\"allow_main\" value=\"1\" checked>{$lang['addnews_main']}</label></div>"; else $main_input = "";

	if($member_id['user_group'] < 3 ) {
		$disable_index = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" id=\"disable_index\" name=\"disable_index\" value=\"1\">{$lang['add_disable_index']}</label></div>";
		$disable_search = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" id=\"disable_search\" name=\"disable_search\" value=\"1\">{$lang['cat_d_search']}</label></div>";
		$need_pass = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" id=\"need_pass\" name=\"need_pass\" value=\"1\" onchange=\"onPassChange(this)\">{$lang['pass_list_1']}</label></div>";

		if( $config['allow_yandex_turbo'] ) {
			$yandex_turbo = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"allow_rss_turbo\" id=\"allow_rss_turbo\" value=\"1\" checked>{$lang['allow_rss_turbo']}</label></div>";
		} else $yandex_turbo = "";

		if( $config['allow_yandex_dzen'] ) {
			$yandex_dzen = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"allow_rss_dzen\" id=\"allow_rss_dzen\" value=\"1\" checked>{$lang['allow_rss_dzen']}</label></div>";
		} else $yandex_dzen = "";
		
		if( $config['allow_rss'] ) {
			
			$rss_option = <<<HTML
				<div class="row mt-15" id="opt_cat_rss">
					<div class="col-sm-6" style="max-width:300px;">
						<div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_rss" value="1" checked>{$lang['allow_rss_news']}</label></div>
						{$yandex_turbo}
					</div>
					<div class="col-sm-6">
						{$yandex_dzen}
					</div>
				</div>
HTML;

		} else $rss_option = "";
		
	} else {
		$disable_index = "";
		$disable_search ="";
		$need_pass = "";
		$rss_option = "";
	}
	
    if( !$config['allow_admin_wysiwyg'] ) $fix_br = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" id=\"allow_br\" name=\"allow_br\" value=\"1\" checked>{$lang['allow_br']}</label></div>"; else $fix_br = "";
	
echo <<<HTML
							  </div>
							</div>
{$output}
							<div class="form-group">
							  <label class="control-label col-md-2">{$lang['addnews_option']}</label>
							  <div class="col-md-10">
								<div class="row">
									<div class="col-sm-6" style="max-width:300px;">
										<div class="checkbox"><label><input class="icheck" type="checkbox" id="approve" name="approve" value="1" checked>{$lang['addnews_mod']}</label></div>
										{$main_input}
										<div class="checkbox" id="opt_holder_rating"><label><input class="icheck" type="checkbox" id="allow_rating" name="allow_rating" value="1" checked>{$lang['addnews_allow_rate']}</label></div>
										{$fix_br}
									</div>
									<div class="col-sm-6">
										<div class="checkbox" id="opt_holder_comments"><label><input class="icheck" type="checkbox" id="allow_comm" name="allow_comm" value="1" checked>{$lang['addnews_comm']}</label></div>
										{$fix_input}
										{$disable_index}
										{$disable_search}
									</div>
								</div>
								{$rss_option}
							  </div>
							 </div>

						</div>
					</div>
                    <div class="tab-pane" id="tabvote" >
						<div class="panel-body">
						
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['v_ftitle']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="vote_title" class="form-control width-400" maxlength="200"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_ftitle']}" ></i>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['vote_title']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="frage" class="form-control width-400" maxlength="200"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_vtitle']}" ></i>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['vote_body']}<div class="text-muted text-size-small">{$lang['vote_str_1']}</div></label>
							  <div class="col-md-10 col-sm-9">
								<textarea rows="7" class="classic width-400" name="vote_body"></textarea>
							  </div>
							 </div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3"></label>
							  <div class="col-md-10 col-sm-9">
								<div class="checkbox"><label><input class="icheck" type="checkbox" id="allow_m_vote" name="allow_m_vote" value="1">{$lang['v_multi']}</label></div>
							  </div>
							 </div>
							<div class="form-group">
								<div class="col-md-12"><span class="text-muted text-size-small"> <i class="fa fa-exclamation-triangle position-left"></i>{$lang['v_info']}</span></div>
							</div>
							 
						</div>
                     </div>
                    <div class="tab-pane" id="tabextra" >
						<div class="panel-body">

							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['catalog_url']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="catalog_url" class="form-control" maxlength="3" style="width:55px;"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['catalog_hint_url']}" ></i>
							  </div>
							 </div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['addnews_url']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="alt_name" class="form-control width-500" maxlength="190"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_url']}" ></i>
							  </div>
							 </div>
							<div class="form-group">
							  <label class="control-label col-sm-2">{$lang['label_related']}</label>
							  <div class="col-sm-10">
								<input type="text" class="form-control width-350 position-left" name="related_ids" id="related_ids"><button onclick="find_related_ids(); return false;" class="visible-lg-inline-block btn bg-info-800 btn-sm btn-raised">{$lang['b_related_renew']}</button>
							  </div>	
							</div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['addnews_tags']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="tags" id="tags" autocomplete="off" />
							  </div>
							 </div>	
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['date_expires']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="expires" data-rel="calendardate" class="form-control" style="width:200px;" autocomplete="off"><span class="position-right position-left">{$lang['cat_action']}</span><select class="uniform" name="expires_action" onchange="moveCategoryChange(this)"><option value="0">{$lang['mass_noact']}</option><option value="1">{$lang['edit_dnews']}</option><option value="2" >{$lang['mass_edit_notapp']}</option><option value="3" >{$lang['mass_edit_notmain']}</option><option value="4" >{$lang['mass_edit_notfix']}</option><option value="5" >{$lang['m_cat_list_2']}</option></select><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_expires']}" ></i>
							  </div>
							 </div>
							 <div class="form-group" id="movecatlist" style="display:none;">
							  <label class="control-label col-sm-2">{$lang['m_cat_list_1']}</label>
							  <div class="col-sm-10">
								<select data-placeholder="{$lang['addnews_cat_sel']}" title="{$lang['addnews_cat_sel']}" name="movecat[]" $category_multiple style="width:100%;max-width:350px;">{$categories_list}</select>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3"></label>
							  <div class="col-md-10 col-sm-9">
								{$need_pass}
							  </div>
							 </div>
							<div class="form-group" id="passlist" style="display:none;">
							  <label class="control-label col-md-2 col-sm-3">{$lang['pass_list_2']}<div class="text-muted text-size-small">{$lang['pass_list_3']}</div></label>
							  <div class="col-md-10 col-sm-9">
								<textarea rows="5" class="classic width-500" name="password"></textarea>
							  </div>
							 </div>
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3"></label>
							  <div class="col-md-10 col-sm-9">
								<span class="text-muted text-size-small">{$lang['add_metatags']}</span><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_metas']}" ></i>
							  </div>
							 </div>	
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['meta_title']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="meta_title" class="form-control width-500" maxlength="140">
							  </div>
							 </div>	
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['meta_descr']}</label>
							  <div class="col-md-10 col-sm-9">
								<input type="text" name="descr" id="autodescr" class="form-control width-500" maxlength="300">
							  </div>
							 </div>	
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$lang['meta_keys']}</label>
							  <div class="col-md-10 col-sm-9">
								<textarea class="tags" name="keywords" id="keywords"></textarea><br /><br />
									<button onclick="auto_keywords(1); return false;" class="btn bg-primary-600 btn-sm btn-raised position-left"><i class="fa fa-exchange position-left"></i>{$lang['btn_descr']}</button>
									<button onclick="auto_keywords(2); return false;" class="btn bg-primary-600 btn-sm btn-raised"><i class="fa fa-exchange position-left"></i>{$lang['btn_keyword']}</button>
							  </div>
							 </div>	
							 
						</div>
                     </div>
                    <div class="tab-pane" id="tabperm" >
						<div class="panel-body">
HTML;

	if( $member_id['user_group'] < 3 ) {
		foreach ( $user_group as $group ) {
			if( $group['id'] > 1 ) {
				echo <<<HTML
							<div class="form-group">
							  <label class="control-label col-md-2 col-sm-3">{$group['group_name']}</label>
							  <div class="col-md-10 col-sm-9">
								<select class="uniform" name="group_extra[{$group['id']}]">
										<option value="0">{$lang['ng_group']}</option>
										<option value="1">{$lang['ng_read']}</option>
										<option value="2">{$lang['ng_all']}</option>
										<option value="3">{$lang['ng_denied']}</option>
								</select>
							   </div>
							 </div>	
HTML;
			}
		}
	} else {
		
		echo <<<HTML
	<div class="text-center pt-20 pb-20">{$lang['tabs_not']}</div>
HTML;
	
	}

echo <<<HTML
							<div class="row">
								<div class="col-md-12"><span class="text-muted text-size-small"><i class="fa fa-exclamation-triangle position-left"></i>{$lang['tabs_g_info']}</span></div>
							</div>
						</div>
                     </div>
				<div class="panel-footer">
					<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['news_add']}</button>
					<button onclick="preview(); return false;" class="btn bg-slate-600 btn-sm btn-raised"><i class="fa fa-desktop position-left"></i>{$lang['btn_preview']}</button>
					<input type="hidden" name="mod" value="addnews">
					<input type="hidden" name="action" value="doaddnews">
					<input type="hidden" name="user_hash" value="{$dle_login_hash}">
				</div>
</form>
			</div>
</div>
HTML;
	
	
	echofooter();

}

// ********************************************************************************
// Do add News
// ********************************************************************************
elseif( $action == "doaddnews" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['sess_error'], "javascript:history.go(-1)" );
	}
	
	@header('X-XSS-Protection: 0;');

	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
	$parse = new ParseFilter();
	
	$allow_comm = isset( $_POST['allow_comm'] ) ? intval( $_POST['allow_comm'] ) : 0;
	$approve = isset( $_POST['approve'] ) ? intval( $_POST['approve'] ) : 0;
	$allow_rating = isset( $_POST['allow_rating'] ) ? intval( $_POST['allow_rating'] ) : 0;
	$news_fixed = isset( $_POST['news_fixed'] ) ? intval( $_POST['news_fixed'] ) : 0;
	$allow_br = isset( $_POST['allow_br'] ) ? intval( $_POST['allow_br'] ) : 0;
	$category = $_POST['category'];
	$disable_index = isset( $_POST['disable_index'] ) ? intval( $_POST['disable_index'] ) : 0;
	$disable_search = isset( $_POST['disable_search'] ) ? intval( $_POST['disable_search'] ) : 0;
	$allow_rss = isset( $_POST['allow_rss'] ) ? intval( $_POST['allow_rss'] ) : 0;
	$allow_rss_turbo = isset( $_POST['allow_rss_turbo'] ) ? intval( $_POST['allow_rss_turbo'] ) : 0;
	$allow_rss_dzen = isset( $_POST['allow_rss_dzen'] ) ? intval( $_POST['allow_rss_dzen'] ) : 0;
	
	$need_pass = isset( $_POST['need_pass'] ) ? intval( $_POST['need_pass'] ) : 0;

	$mail_send = false;

	if( $user_group[$member_id['user_group']]['allow_main'] ) $allow_main = intval( $_POST['allow_main'] );
	else $allow_main = 0;

	$disable_rss_dzen = 0;
	$disable_rss_turbo = 0;
		
	if($member_id['user_group'] > 2 ) {
		$disable_index = 0;
		$disable_search = 0;
		$need_pass = 0;
		$allow_rss = 1;
		$allow_rss_turbo = 1;
		$allow_rss_dzen = 1;
	}

	if( !$config['allow_rss'] ) { $allow_rss = 1; }
	if( !$config['allow_yandex_dzen'] ) { $allow_rss_dzen = 0; }
	if( !$config['allow_yandex_turbo'] ) { $allow_rss_turbo = 0; }
	
	if( !trim($_POST['password']) ) $need_pass = 0;
	
	if( !is_array($category) ) $category = array ();
	
	if( !count($category) ) $category[] = '0';

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	if($member_id['cat_add']) $allow_list = explode( ',', $member_id['cat_add'] );
	else $allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
	
	foreach ( $category_list as $selected ) {
		
		if( $allow_list[0] != "all" AND !in_array( $selected, $allow_list ) ) {
			$approve = 0;
			$mail_send = true;
		}
		
		if($cat_info[$selected]['disable_main']) $allow_main = 0;
		if($cat_info[$selected]['disable_comments']) $allow_comm = 0;
		if($cat_info[$selected]['disable_rating']) $allow_rating = 0;
		
		if($member_id['user_group'] > 2 ) {
			if(!$cat_info[$selected]['enable_dzen']) $disable_rss_dzen ++;
			if(!$cat_info[$selected]['enable_turbo']) $disable_rss_turbo ++;
		}
		
	}
	
	if($member_id['user_group'] > 2 ) {
		if( $disable_rss_dzen AND $disable_rss_dzen = count($category_list) ) $allow_rss_dzen = 0;
		if( $disable_rss_turbo AND $disable_rss_turbo = count($category_list) ) $allow_rss_turbo = 0;
	}
		
	if($member_id['cat_allow_addnews']) $allow_list = explode( ',', $member_id['cat_allow_addnews'] );
	else $allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_allow_addnews'] );
	
	foreach ( $category_list as $selected ) {
		if( $allow_list[0] != "all" AND !in_array( $selected, $allow_list ) ) msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['news_err_41'], "javascript:history.go(-1)" );
	}
	
	$category_list = $db->safesql( implode( ',', $category_list ) );

	if( !$user_group[$member_id['user_group']]['moderation'] ) {
		$approve = 0;
		$mail_send = true;
	}

	$title = $parse->process(  trim( strip_tags ($_POST['title']) ) );

	if ( !$user_group[$member_id['user_group']]['allow_html'] ) {

		$_POST['short_story'] = strip_tags ($_POST['short_story']);
		$_POST['full_story'] = strip_tags ($_POST['full_story']);

	}

	if ( $config['allow_admin_wysiwyg'] ) $parse->allow_code = false;
	
	$full_story = $parse->process( $_POST['full_story'] );
	$short_story = $parse->process( $_POST['short_story'] );

	if( $config['allow_admin_wysiwyg'] OR $allow_br != '1' ) {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story ) );
	
	} else {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story, false ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story, false ) );
	}

	if( $parse->not_allowed_text ) {
		msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['news_err_39'], "javascript:history.go(-1)" );
	}

	$alt_name = trim($_POST['alt_name']);
	
	if(!$alt_name) $alt_name = totranslit( stripslashes( $title ), true, false );
	else $alt_name = totranslit( stripslashes( $alt_name ), true, false );
	
	if( dle_strlen( $alt_name, $config['charset'] ) > 190 ) {
		$alt_name = dle_substr( $alt_name, 0, 190, $config['charset'] );
	}
	
	$title = $db->safesql( $title );
	$alt_name = $db->safesql( $alt_name );

	if( $config['allow_alt_url'] AND !$config['seo_type'] ) {
		
		$db->query( "SELECT id, date FROM " . PREFIX . "_post WHERE alt_name ='{$alt_name}'" );

		while($found_news = $db->get_row()) {
			if( $found_news['id'] AND date( 'Y-m-d', strtotime( $found_news['date'] ) ) == date( 'Y-m-d', $_TIME ) ) {
				msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['news_err_42'], "javascript:history.go(-1)" );
			}	
		}
	
	}

	$metatags = create_metatags( $short_story." ".$full_story );
	
	$catalog_url = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['catalog_url'] ) ) ), ENT_QUOTES, $config['charset'] ), 0, 3, $config['charset'] ) );

	if ($config['create_catalog'] AND !$catalog_url) $catalog_url = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $title ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	
	if( @preg_match( "/[\||\<|\>]/", $_POST['tags'] ) ) $_POST['tags'] = "";
	else $_POST['tags'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['tags'] ) ) ), ENT_COMPAT, $config['charset'] ) );

	if ( $_POST['tags'] ) {

		$temp_array = array();
		$tags_array = array();
		$temp_array = explode (",", $_POST['tags']);

		if (count($temp_array)) {

			foreach ( $temp_array as $value ) {
				if( trim($value) ) $tags_array[] = trim( $value );
			}

		}

		if ( count($tags_array) ) $_POST['tags'] = implode(", ", $tags_array); else $_POST['tags'] = "";

	}
	
	
	if( trim( $_POST['vote_title'] ) ) {
		
		$add_vote = 1;
		$vote_title = trim( $db->safesql( $parse->process( strip_tags($_POST['vote_title']) ) ) );
		$frage = trim( $db->safesql( $parse->process( strip_tags($_POST['frage']) ) ) );
		$vote_body = $db->safesql( $parse->BB_Parse( $parse->process( strip_tags($_POST['vote_body']) ), false ) );
		$allow_m_vote = intval( $_POST['allow_m_vote'] );
	
	} else $add_vote = 0;

	if( trim( $_POST['related_ids'] ) ) {
		
		$_POST['related_ids'] = explode(',', $_POST['related_ids']);
		
		foreach ( $_POST['related_ids'] as $value ) {
			if( intval($value) ){
				$related_ids[] = intval($value);
			}
		}
		
		$related_ids = implode(',', $related_ids);
	
	} else $related_ids = '';

	if( $member_id['user_group'] < 3 ) {
		
		$group_regel = array ();
		
		foreach ( $_POST['group_extra'] as $key => $value ) {
			if( $value ) $group_regel[] = intval( $key ) . ':' . intval( $value );
		}
		
		if( count( $group_regel ) ) $group_regel = implode( "||", $group_regel );
		else $group_regel = "";
	
	} else $group_regel = '';
	
	if( trim( $_POST['expires'] ) != "" ) {
		$expires = $_POST['expires'];
		if( (($expires = strtotime( $expires )) === - 1) OR !$expires ) {
			msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['addnews_erdate'], "javascript:history.go(-1)" );
		} 
	} else $expires = '';

	$added_time = time();
	$newdate = trim($_POST['newdate']);
	
	if( $newdate ) {
		
		if( (($newsdate = strtotime( $newdate )) === - 1) OR !$newsdate ) {
			msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['addnews_erdate'], "javascript:history.go(-1)" );
		} else {
			$thistime = date( "Y-m-d H:i:s", $newsdate );
		}
		
		if( ! intval( $config['no_date'] ) and $newsdate > $added_time ) {
			$thistime = date( "Y-m-d H:i:s", $added_time );
		}
	
	} else $thistime = date( "Y-m-d H:i:s", $added_time );
	
	////////////////////////////	

	if( !$title ) {
		msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['addnews_alert'], "javascript:history.go(-1)" );
		
	}

	if( dle_strlen( $title, $config['charset'] ) > 255 ) {
		msg( "error", array('javascript:history.go(-1)' => $lang['addnews'], '' => $lang['addnews_error'] ), $lang['addnews_ermax'], "javascript:history.go(-1)" );
	}

	$author = $member_id['name'];
	$userid = $member_id['user_id'];

	if( $member_id['user_group'] == 1 AND $_POST['new_author'] != $member_id['name'] ) {

		$_POST['new_author'] = $db->safesql( $_POST['new_author'] );
					
		$row = $db->super_query( "SELECT name, user_id  FROM " . USERPREFIX . "_users WHERE name = '{$_POST['new_author']}'" );
					
		if( $row['user_id'] ) {

			$author = $row['name'];
			$userid = $row['user_id'];

		}
	}

	$xfieldsid = $added_time;
	$xfieldsaction = "init";
	$xf_existing = array();
	include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
	
	$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, xfields, title, descr, keywords, category, alt_name, allow_comm, approve, allow_main, fixed, allow_br, symbol, tags, metatitle) values ('$thistime', '{$author}', '$short_story', '$full_story', '$filecontents', '$title', '{$metatags['description']}', '{$metatags['keywords']}', '$category_list', '$alt_name', '$allow_comm', '$approve', '$allow_main', '$news_fixed', '$allow_br', '$catalog_url', '{$_POST['tags']}', '{$metatags['title']}')" );
	
	$id = $db->insert_id();

	$db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, votes, disable_index, related_ids, access, user_id, disable_search, need_pass, allow_rss, allow_rss_turbo, allow_rss_dzen) VALUES('{$id}', '{$allow_rating}', '{$add_vote}', '{$disable_index}', '{$related_ids}', '{$group_regel}', '{$userid}', '{$disable_search}', '{$need_pass}', '{$allow_rss}', '{$allow_rss_turbo}', '{$allow_rss_dzen}')" );
	
	if( $add_vote ) {
		$db->query( "INSERT INTO " . PREFIX . "_poll (news_id, title, frage, body, votes, multiple, answer) VALUES('{$id}', '$vote_title', '$frage', '$vote_body', 0, '$allow_m_vote', '')" );
	}
	
    if ( $need_pass ) {
		$post_password = $db->safesql($_POST['password']);
		$db->query( "INSERT INTO " . PREFIX . "_post_pass (news_id, password) VALUES('{$id}', '{$post_password}')" );		
	}
	
	$expires_action = intval($_POST['expires_action']);

	if( $expires AND $expires_action) {
		
		$movecat = $_POST['movecat'];
		
		if( !is_array($movecat) ) $movecat = array ();
	
		if( !count($movecat) ) $movecat[] = '0';
	
		$movecat_list = array();
	
		foreach ( $movecat as $value ) {
			$movecat_list[] = intval($value);
		}
	
		$movecat_list = $db->safesql( implode( ',', $movecat_list ) );
	
		$db->query( "INSERT INTO " . PREFIX . "_post_log (news_id, expires, action, move_cat) VALUES('{$id}', '$expires', '$expires_action', '$movecat_list')" );
	}
	
	if( $_POST['tags'] != "" AND $approve ) {
		
		$tags = array ();
		
		$_POST['tags'] = explode( ",", $_POST['tags'] );
		
		foreach ( $_POST['tags'] as $value ) {
			
			$tags[] = "('" . $id . "', '" . trim( $value ) . "')";
		}
		
		$tags = implode( ", ", $tags );
		$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
	
	}
	
	if( $category_list AND $approve ) {

		$cat_ids = array ();
		
		$cat_ids_arr = explode( ",", $category_list );
		
		foreach ( $cat_ids_arr as $value ) {
			
			$cat_ids[] = "('" . $id . "', '" . trim( $value ) . "')";
		}
		
		$cat_ids = implode( ", ", $cat_ids );
		$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	
	}
	
	if ( count($xf_search_words) AND $approve ) {
		
		$temp_array = array();
		
		foreach ( $xf_search_words as $value ) {
			
			$temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "')";
		}
		
		$xf_search_words = implode( ", ", $temp_array );
		$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	}
	
	$db->query( "UPDATE " . PREFIX . "_images SET news_id='{$id}', author = '{$author}' WHERE author = '{$member_id['name']}' AND news_id = '0'" );
	$db->query( "UPDATE " . PREFIX . "_files SET news_id='{$id}', author = '{$author}' WHERE author = '{$member_id['name']}' AND news_id = '0'" );
	$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num+1 WHERE user_id='{$userid}'" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '1', '{$title}')" );
	
	clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats') );
	
	if( !$approve AND $mail_send AND $config['mail_news'] ) {
		
		include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_email WHERE name='new_news' LIMIT 0,1" );
		$mail = new dle_mail( $config, $row['use_html'] );
		
		$row['template'] = stripslashes( $row['template'] );
		$row['template'] = str_replace( "{%username%}", $member_id['name'], $row['template'] );
		$row['template'] = str_replace( "{%date%}", langdate( "j F Y H:i", $added_time, true ), $row['template'] );
		$row['template'] = str_replace( "{%title%}", stripslashes( stripslashes( $title ) ), $row['template'] );
		
		$category_list = explode( ",", $category_list );
		$my_cat = array ();
		
		foreach ( $category_list as $element ) {
			
			$my_cat[] = $cat_info[$element]['name'];
		
		}
		
		$my_cat = stripslashes( implode( ', ', $my_cat ) );
		
		$row['template'] = str_replace( "{%category%}", $my_cat, $row['template'] );
		
		$mail->send( $config['admin_mail'], $lang['mail_news'], $row['template'] );
	
	}

	$row = $db->super_query( "SELECT id, date, category, alt_name FROM " . PREFIX . "_post WHERE id='{$id}' LIMIT 1" );
	
	if( $config['allow_alt_url'] ) {
		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
			if( intval( $row['category'] ) and $config['seo_type'] == 2 ) {
				$full_link = $config['http_home_url'] . get_url( intval( $row['category'] ) ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
			} else {
				$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
			}
		} else {
			$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
		}
	} else {
		$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
	}

	msg( "success", $lang['addnews_ok'], $lang['addnews_ok_1'] . " \"" . stripslashes( stripslashes( $title ) ) . "\" " . $lang['addnews_ok_2'], array('?mod=addnews&action=addnews' => $lang['add_s_1'], '?mod=editnews&action=editnews&id='.$id => $lang['add_s_2'], '?mod=editnews&action=list' => $lang['add_s_3'], $full_link => $lang['add_s_5'] ) );
}
?>