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
 File: comments.class.php
-----------------------------------------------------
 Use: comments class
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class DLE_Comments {

	var $db = false;
	var $query = false;
	var $cstart = 0;
	var $total_comments = 0;
	var $comments_per_pages = 0;
	var $intern_count = 0;
	var $extras_rules = array();
	var $comments_group = 0;
	var $xfields = array();
	var $xfound = false;
	var $indent = 0;
	var $customshow = false;
	
	function __construct( $db, $total_comments, $comments_per_pages ) {

		$this->db = $db;
		$this->total_comments = $total_comments;
		
		if($comments_per_pages < 1)  $comments_per_pages = 30;
	
		$this->comments_per_pages = $comments_per_pages;

		if ( isset( $_GET['cstart'] ) ) $this->cstart = intval( $_GET['cstart'] ); else $this->cstart = 0;

		if( $this->cstart > 0) {
			$this->cstart = $this->cstart - 1;
			$this->cstart = $this->cstart * $comments_per_pages;
		} else $this->cstart = 0;

	}

	function add_rules( $find, $replace, $type ) {

		$this->extras_rules[] = array($type, $find, $replace);

	}

	function build_tree( $data ) {

		$tree = array();
		foreach ($data as $id=>&$node) {
			if ($node['parent'] === false) {
				$tree[$id] = &$node;
			} else {
				if (!isset($data[$node['parent']]['children'])) $data[$node['parent']]['children'] = array();
				$data[$node['parent']]['children'][$id] = &$node;
			}
		}
		
		return $tree;

	}

	function compile_tree($nodes, $area, $sublevelmarker = false, $indent = 0 ) {
		global $config, $tpl;
		
		if ($config['tree_comments'] AND $config['tree_comments_level'] AND $indent > $config['tree_comments_level'] ) $sublevelmarker = false;
		
		$item = "";
		
		foreach ($nodes as $node) {
			
			$item .= "<li id=\"comments-tree-item-{$node['id']}\" class=\"comments-tree-item\" >".$this->compile_comment($tpl, $node, $area, $indent);
			
			if (isset($node['children'])) {
				$item .= $this->compile_tree($node['children'], $area, true, $indent+1);
			}
			
			$item .= "</li>";
			
		}
		
		if( $sublevelmarker ) return "<ol class=\"comments-tree-list\">".$item."</ol>"; else return $item;
	}

	function compile_comments($tpl, $rows, $area) {
		global $config;
		
		$item = "";
		
		foreach ($rows as $row) {
			$item .= $this->compile_comment($tpl, $row, $area, 0);
		}
		
		return $item;
	}	
	
	function compile_comment( $tpl, $row, $area, $indent ) {
		global $config, $is_logged, $member_id, $user_group, $lang, $dle_login_hash, $_TIME, $allow_comments_ajax, $ajax_adds, $news_date, $news_author, $replace_links, $category_id, $banners;

		$PHP_SELF = $config['http_home_url'] . "index.php";
				
		$this->intern_count ++;

		$tpl->result['comments'] = "";

		$row['date'] = strtotime( $row['date'] );

		$row['gast_name'] = stripslashes( $row['gast_name'] );
		$row['gast_email'] = stripslashes( $row['gast_email'] );
		$row['name'] = stripslashes( $row['name'] );
		
		if( ! $row['is_register'] or $row['name'] == '' ) {

			if( $row['gast_email'] != "" ) {

				$tpl->set( '{author}', "<a href=\"mailto:".htmlspecialchars($row['gast_email'], ENT_QUOTES, $config['charset'])."\">" . $row['gast_name'] . "</a>" );

			} else {
				$tpl->set( '{author}', $row['gast_name'] );
			}

			$tpl->set( '{login}', $row['gast_name'] );
			$tpl->set( '[profile]', "" );
			$tpl->set( '[/profile]', "" );

		} else {

			if( $config['allow_alt_url'] ) {

				$go_page = $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/";
				$tpl->set( '[profile]', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" );

			} else {

				$go_page = "$PHP_SELF?subaction=userinfo&user=" . urlencode( $row['name'] );
				$tpl->set( '[profile]', "<a href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" );
			}


			$go_page = "onclick=\"ShowProfile('" . urlencode( $row['name'] ) . "', '" . htmlspecialchars( $go_page, ENT_QUOTES, $config['charset'] ) . "', '" . $user_group[$member_id['user_group']]['admin_editusers'] . "'); return false;\"";

			if( $config['allow_alt_url'] ) $tpl->set( '{author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" . $row['name'] . "</a>" );
			else $tpl->set( '{author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" . $row['name'] . "</a>" );

			$tpl->set( '{login}', $row['name'] );
			$tpl->set( '[/profile]', "</a>" );

		}

		if( $config['allow_banner'] AND count( $banners ) ) {
			
			foreach ( $banners as $name => $value ) {
				$tpl->copy_template = str_replace( "{banner_" . $name . "}", $value, $tpl->copy_template );

				if ( $value ) {
					$tpl->copy_template = str_replace ( "[banner_" . $name . "]", "", $tpl->copy_template );
					$tpl->copy_template = str_replace ( "[/banner_" . $name . "]", "", $tpl->copy_template );
				}
			}
		}
		
		$tpl->set_block( "'{banner_(.*?)}'si", "" );
		$tpl->set_block ( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "" );
		
		if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onclick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" href=\"https://www.nic.ru/whois/?searchWord={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
		else $tpl->set( '{ip}', '' );

		$edit_limit = false;
		
		if (!$user_group[$member_id['user_group']]['edit_limit']) $edit_limit = true;
		elseif ( ($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) > $_TIME ) {
			$edit_limit = true;
		}

		if( $is_logged AND $edit_limit AND !$this->customshow AND (($member_id['name'] == $row['name'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_editc']) OR $user_group[$member_id['user_group']]['edit_allc']) ) {
			
			$tpl->set( '[com-edit]', "<a onclick=\"ajax_comm_edit('" . $row['id'] . "', '" . $area . "'); return false;\" href=\"#\">" );
			$tpl->set( '[/com-edit]', "</a>" );
			$allow_comments_ajax = true;
			
		} else $tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );


		if( $is_logged AND $edit_limit AND !$this->customshow AND (($member_id['name'] == $row['name'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_delc']) OR $member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc']) ) {
			
			$tpl->set( '[com-del]', "<a href=\"javascript:DeleteComments('{$row['id']}', '{$dle_login_hash}')\">" );
			$tpl->set( '[/com-del]', "</a>" );
			
		} else $tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );

		if( $is_logged AND $user_group[$member_id['user_group']]['allow_admin'] AND $user_group[$member_id['user_group']]['del_allc'] ) {
			
			$tpl->set( '[spam]', "<a href=\"javascript:MarkSpam('{$row['id']}', '{$dle_login_hash}');\">" );
			$tpl->set( '[/spam]', "</a>" );
			
		} else $tpl->set_block( "'\\[spam\\](.*?)\\[/spam\\]'si", "" );

		if ( $user_group[$member_id['user_group']]['del_allc'] AND !$user_group[$member_id['user_group']]['edit_limit'] AND !$this->customshow ) {

			$tpl->set( '{mass-action}', "<input name=\"selected_comments[]\" value=\"{$row['id']}\" type=\"checkbox\" />" );

		} else $tpl->set( '{mass-action}', "" );
		
		if( !$row['is_register'] OR $row['name'] == '' ) $d_name = $row['gast_name'];
		else $d_name = $row['name'];
				
		if ($area == 'lastcomments') {

			$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
			
			if($user_group[$member_id['user_group']]['allow_addc'] AND $config['allow_comments'] AND $config['tree_comments'] ) {
				$allow_comments_ajax = true;
				$tpl->set( '[reply]', "<a onclick=\"dle_reply('{$row['id']}', '0', '{$config['simple_reply']}'); return false;\" href=\"#\">" );
				$tpl->set( '[/reply]', "</a>" );
			} else {
				$tpl->set_block( "'\\[reply\\](.*?)\\[/reply\\]'si", "" );
			}

		} else {

			if( $user_group[$member_id['user_group']]['allow_addc'] AND $config['allow_comments'] ) {
				
				$tpl->set( '[fast]', "<a onmouseover=\"dle_copy_quote('" . str_replace( array (" ", "&#039;" ), array ("&nbsp;", "&amp;#039;" ), $d_name ) . "');\" href=\"#\" onclick=\"dle_ins('{$row['id']}'); return false;\">" );
				$tpl->set( '[/fast]', "</a>" );
				$tpl->set( '[/reply]', "</a>" );
				
				if( $config['tree_comments'] ) {
						
					if($this->indent) $indent = $this->indent;
					$allow_comments_ajax = true;
					$tpl->set( '[reply]', "<a onclick=\"dle_reply('{$row['id']}', '{$indent}', '{$config['simple_reply']}'); return false;\" href=\"#\">" );	

				} else {
					
					$tpl->set( '[reply]', "<a onclick=\"dle_fastreply('" . str_replace( array (" ", "&#039;" ), array ("&nbsp;", "&amp;#039;" ), $d_name ) . "'); return false;\" href=\"#\">" );

				}
				
			} else {
				$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
				$tpl->set_block( "'\\[reply\\](.*?)\\[/reply\\]'si", "" );
			}

		}

		$tpl->set( '{mail}', $row['gast_email'] );
		$tpl->set( '{id}', $row['id'] );

		if( date( 'Ymd', $row['date'] ) == date( 'Ymd', $_TIME ) ) {

			$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );

		} elseif( date( 'Ymd', $row['date'] ) == date( 'Ymd', ($_TIME - 86400) ) ) {

			$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );

		} else {

			$tpl->set( '{date}', langdate( $config['timestamp_comment'], $row['date'] ) );

		}

		$news_date = $row['date'];
		$tpl->copy_template = preg_replace_callback ( "#\{date=(.+?)\}#i", "formdate", $tpl->copy_template );

		if ($area == 'lastcomments') {
			
			$category_id = $row['category'];
			
			$row['category'] = intval( $row['category'] );

			if( $config['allow_alt_url'] ) {

				if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {

					if( $row['category'] and $config['seo_type'] == 2 ) {

						$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['post_id'] . "-" . $row['alt_name'] . ".html";

					} else {

						$full_link = $config['http_home_url'] . $row['post_id'] . "-" . $row['alt_name'] . ".html";

					}

				} else {

					$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime ($row['newsdate']) ) . $row['alt_name'] . ".html";
				}

			} else {

				$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['post_id'];

			}

			$tpl->set( '{news_title}', "<a href=\"" . $full_link . "#comment\">" . stripslashes( $row['title'] ) . "</a>" );
			$tpl->set( '{news-link}', $full_link."#comment" );
			$tpl->set( '{news-title}', htmlspecialchars( stripslashes( $row['title'] ), ENT_QUOTES, $config['charset'] ) );

		} else 	{
			$tpl->set( '{news_title}', "" );
			$tpl->set( '{news-link}', "" );
			$tpl->set( '{news-title}', "" );
		}
		
		if( strpos( $tpl->copy_template, "[catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(catlist)=(.+?)\\](.*?)\\[/catlist\\]#is", "check_category", $tpl->copy_template );
		}
								
		if( strpos( $tpl->copy_template, "[not-catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-catlist)=(.+?)\\](.*?)\\[/not-catlist\\]#is", "check_category", $tpl->copy_template );
		}
		
		if( $this->xfound ) {
			$xfieldsdata = xfieldsdataload( $row['xfields'] );

			foreach ( $this->xfields as $value ) {
				$preg_safe_name = preg_quote( $value[0], "'" );

				if($xfieldsdata[$value[0]] == "") $xfgiven = false; else $xfgiven = true;

				if( $value[5] != 1 OR $member_id['user_group'] == 1 OR ($is_logged AND $row['is_register'] AND $member_id['name'] == $row['name']) ) {

					if( !$xfgiven ) {

						$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template );

					} else {
						$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[xfgiven_{$value[0]}]", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[/xfgiven_{$value[0]}]", "", $tpl->copy_template );
					}

					$tpl->set( "[xfvalue_{$value[0]}]", stripslashes( $xfieldsdata[$value[0]] ) );

				} else {

					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
					$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", "", $tpl->copy_template );
					$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );

				}
			}
		}

		if ($area == 'ajax' AND isset($ajax_adds) ) {

			$tpl->set( '{comment-id}', "--" );

		} elseif($area == 'lastcomments') {

			$tpl->set( '{comment-id}', $this->total_comments - $this->cstart - $this->intern_count + 1 );

		} else {

			if( $config['comm_msort'] == "ASC" ) $tpl->set( '{comment-id}', $this->cstart + $this->intern_count );
			else $tpl->set( '{comment-id}', $this->total_comments - $this->cstart - $this->intern_count + 1 );

		}
		
		if ( count(explode("@", $row['foto'])) == 2 ) {

			$tpl->set( '{foto}', 'https://www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']) );

		} else {

			if( $row['foto'] ) {
				
				if (strpos($row['foto'], "//") === 0) $avatar = "http:".$row['foto']; else $avatar = $row['foto'];
	
				$avatar = @parse_url ( $avatar );

				if($avatar['host']) {
					
					$tpl->set( '{foto}', $row['foto'] );
					
				} else $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
				
			} else $tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

		}

		if( $row['is_register'] AND $row['fullname'] ) {
			$tpl->set( '[fullname]', "" );
			$tpl->set( '[/fullname]', "" );
			$tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
			$tpl->set_block( "'\\[not-fullname\\](.*?)\\[/not-fullname\\]'si", "" );

		} else {
			$tpl->set_block( "'\\[fullname\\](.*?)\\[/fullname\\]'si", "" );
			$tpl->set( '{fullname}', "" );
			$tpl->set( '[not-fullname]', "" );
			$tpl->set( '[/not-fullname]', "" );
		}

		if( $config['tree_comments'] ) {
			$tpl->set( '[treecomments]', "" );
			$tpl->set( '[/treecomments]', "" );
			$tpl->set_block( "'\\[not-treecomments\\](.*?)\\[/not-treecomments\\]'si", "" );
		} else {
			$tpl->set( '[not-treecomments]', "" );
			$tpl->set( '[/not-treecomments]', "" );
			$tpl->set_block( "'\\[treecomments\\](.*?)\\[/treecomments\\]'si", "" );			
		}

		if( $indent OR $this->indent ) {
			$tpl->set_block( "'\\[rootcomments\\](.*?)\\[/rootcomments\\]'si", "" );
			$tpl->set( '[childrencomments]', "" );
			$tpl->set( '[/childrencomments]', "" );	
		} else {
			$tpl->set( '[rootcomments]', "" );
			$tpl->set( '[/rootcomments]', "" );
			$tpl->set_block( "'\\[childrencomments\\](.*?)\\[/childrencomments\\]'si", "" );
		}
		
		if ( isset($row['children']) ) {
			$tpl->set( '{replycount}', count( $row['children'] ) );
		} else {
			$tpl->set( '{replycount}', 0 );
		}

		if ( $row['user_id'] AND $row['user_id'] == $member_id['user_id'] ) {
			$tpl->set( '[comments-author]', "" );
			$tpl->set( '[/comments-author]', "" );
			$tpl->set_block( "'\\[not-comments-author\\](.*?)\\[/not-comments-author\\]'si", "" );
		} else {
			$tpl->set_block( "'\\[comments-author\\](.*?)\\[/comments-author\\]'si", "" );
			$tpl->set( '[not-comments-author]', "" );
			$tpl->set( '[/not-comments-author]', "" );
		}
		
		if ( $news_author AND $row['user_id'] AND $row['user_id'] == $news_author ) {
			$tpl->set( '[news-author]', "" );
			$tpl->set( '[/news-author]', "" );
			$tpl->set_block( "'\\[not-news-author\\](.*?)\\[/not-news-author\\]'si", "" );
		} else {
			$tpl->set_block( "'\\[news-author\\](.*?)\\[/news-author\\]'si", "" );
			$tpl->set( '[not-news-author]', "" );
			$tpl->set( '[/not-news-author]', "" );
		}
		
		if( $row['is_register'] AND $row['land'] ) {
			$tpl->set( '[land]', "" );
			$tpl->set( '[/land]', "" );
			$tpl->set( '{land}', stripslashes( $row['land'] ) );
			$tpl->set_block( "'\\[not-land\\](.*?)\\[/not-land\\]'si", "" );

		} else {
			$tpl->set_block( "'\\[land\\](.*?)\\[/land\\]'si", "" );
			$tpl->set( '{land}', "" );
			$tpl->set( '[not-land]', "" );
			$tpl->set( '[/not-land]', "" );
		}

		if( $row['comm_num'] ) {

			$tpl->set( '[comm-num]', "" );
			$tpl->set( '[/comm-num]', "" );
			$tpl->set( '{comm-num}', number_format($row['comm_num'], 0, ',', ' ') );
			$tpl->set_block( "'\\[not-comm-num\\](.*?)\\[/not-comm-num\\]'si", "" );

		} else {

			$tpl->set( '{comm-num}', 0 );
			$tpl->set( '[not-comm-num]', "" );
			$tpl->set( '[/not-comm-num]', "" );
			$tpl->set_block( "'\\[comm-num\\](.*?)\\[/comm-num\\]'si", "" );
		}

		if( $row['news_num'] ) {

			$tpl->set( '[news-num]', "" );
			$tpl->set( '[/news-num]', "" );
			$tpl->set( '{news-num}', number_format($row['news_num'], 0, ',', ' ') );
			$tpl->set_block( "'\\[not-news-num\\](.*?)\\[/not-news-num\\]'si", "" );

		} else {

			$tpl->set( '{news-num}', 0 );
			$tpl->set( '[not-news-num]', "" );
			$tpl->set( '[/not-news-num]', "" );
			$tpl->set_block( "'\\[news-num\\](.*?)\\[/news-num\\]'si", "" );
		}

		if( $row['is_register'] AND $row['reg_date'] ) $tpl->set( '{registration}', langdate( "j.m.Y", $row['reg_date'] ) );
		else $tpl->set( '{registration}', '--' );

		if( $row['is_register'] AND $row['lastdate'] ) {

			$tpl->set( '{lastdate}', langdate( "j.m.Y", $row['lastdate'] ) );

			if ( ($row['lastdate'] + 1200) > $_TIME OR ($row['user_id'] AND $row['user_id'] == $member_id['user_id'])) {

				$tpl->set( '[online]', "" );
				$tpl->set( '[/online]', "" );
				$tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );

			} else {
				$tpl->set( '[offline]', "" );
				$tpl->set( '[/offline]', "" );
				$tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );
			}

		} else {

			$tpl->set( '{lastdate}', '--' );
			$tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );
			$tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );

		}

		if( $row['is_register'] AND $row['signature'] and $user_group[$row['user_group']]['allow_signature'] ) {

			$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "\\1" );
			$tpl->set( '{signature}', stripslashes( $row['signature'] ) );

		} else {
			$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
		}

		$tpl->set( '[complaint]', "<a href=\"javascript:AddComplaint('" . $row['id'] . "', 'comments')\">" );
		$tpl->set( '[/complaint]', "</a>" );

		if ( $config['comments_rating_type'] == "1" ) {
				$tpl->set( '[rating-type-2]', "" );
				$tpl->set( '[/rating-type-2]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
		} elseif ( $config['comments_rating_type'] == "2" ) {
				$tpl->set( '[rating-type-3]', "" );
				$tpl->set( '[/rating-type-3]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
		} elseif ( $config['comments_rating_type'] == "3" ) {
				$tpl->set( '[rating-type-4]', "" );
				$tpl->set( '[/rating-type-4]', "" );
				$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
		} else {
				$tpl->set( '[rating-type-1]', "" );
				$tpl->set( '[/rating-type-1]', "" );
				$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
				$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );	
		}
			
		if( $config['allow_comments_rating'] ) {
			
			$dislikes = ($row['vote_num'] - $row['rating'])/2;
			$likes = $row['vote_num'] - $dislikes;
			
			if( $row['vote_num'] ) $ratingscore = str_replace( ',', '.', round( ($row['rating'] / $row['vote_num']), 1 ) );
			else $ratingscore = 0;
		
			$tpl->set( '{likes}', "<span id=\"comments-likes-id-".$row['id']."\" class=\"ignore-select\">".$likes."</span>" );
			$tpl->set( '{dislikes}', "<span id=\"comments-dislikes-id-".$row['id']."\" class=\"ignore-select\">".$dislikes."</span>" );
			$tpl->set( '{rating}', ShowCommentsRating( $row['id'], $row['rating'], $row['vote_num'], $user_group[$member_id['user_group']]['allow_comments_rating'] ) );
			$tpl->set( '{vote-num}', "<span id=\"comments-vote-num-id-".$row['id']."\">".$row['vote_num']."</span>" );
			$tpl->set( '{ratingscore}', $ratingscore );
			$tpl->set( '[rating]', "" );
			$tpl->set( '[/rating]', "" );

			if($row['rating'] > 0 ) {
				$tpl->set( '[positive-comment]', "" );
				$tpl->set( '[/positive-comment]', "" );
				$tpl->set_block( "'\\[negative-comment\\](.*?)\\[/negative-comment\\]'si", "" );
				$tpl->set_block( "'\\[neutral-comment\\](.*?)\\[/neutral-comment\\]'si", "" );
			} elseif($row['rating'] < 0){
				$tpl->set( '[negative-comment]', "" );
				$tpl->set( '[/negative-comment]', "" );
				$tpl->set_block( "'\\[positive-comment\\](.*?)\\[/positive-comment\\]'si", "" );
				$tpl->set_block( "'\\[neutral-comment\\](.*?)\\[/neutral-comment\\]'si", "" );	
			} else {
				$tpl->set( '[neutral-comment]', "" );
				$tpl->set( '[/neutral-comment]', "" );
				$tpl->set_block( "'\\[positive-comment\\](.*?)\\[/positive-comment\\]'si", "" );
				$tpl->set_block( "'\\[negative-comment\\](.*?)\\[/negative-comment\\]'si", "" );
			}
				
			if( $user_group[$member_id['user_group']]['allow_comments_rating'] ) {
	
				if ( $config['comments_rating_type'] ) {
						
					$tpl->set( '[rating-plus]', "<a href=\"#\" onclick=\"doCommentsRate('plus', '{$row['id']}'); return false;\" >" );
					$tpl->set( '[/rating-plus]', '</a>' );
					
					if ( $config['comments_rating_type'] == "2" OR $config['comments_rating_type'] == "3") {
						
						$tpl->set( '[rating-minus]', "<a href=\"#\" onclick=\"doCommentsRate('minus', '{$row['id']}'); return false;\" >" );
						$tpl->set( '[/rating-minus]', '</a>' );
						
					} else {
						$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
					}
					
				} else {
					$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
					$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
				}
				
			} else {
				$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
				$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );				
			}
	
		} else { 

			$tpl->set( '{rating}', "" );
			$tpl->set( '{ratingscore}', 0 );
			$tpl->set( '{vote-num}', "" );
			$tpl->set_block( "'\\[rating\\](.*?)\\[/rating\\]'si", "" );
			$tpl->set_block( "'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", "" );
			$tpl->set_block( "'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", "" );
		}

		if( ! $row['user_group'] ) $row['user_group'] = 5;
		
		$this->comments_group = $row['user_group'];

		if (strpos ( $tpl->copy_template, "[commentsgroup=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(commentsgroup)=(.+?)\\](.*?)\\[/commentsgroup\\]#is", array( &$this, 'check_group'), $tpl->copy_template );
		}

		if (strpos ( $tpl->copy_template, "[not-commentsgroup=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-commentsgroup)=(.+?)\\](.*?)\\[/not-commentsgroup\\]#is", array( &$this, 'check_group'), $tpl->copy_template );
		}

		if (strpos ( $tpl->copy_template, "[commentscount=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(commentscount)=(.+?)\\](.*?)\\[/commentscount\\]#is", array( &$this, 'check_commentscount'), $tpl->copy_template );
		}

		if (strpos ( $tpl->copy_template, "[not-commentscount=" ) !== false) {
			$tpl->copy_template = preg_replace_callback ( "#\\[(not-commentscount)=(.+?)\\](.*?)\\[/not-commentscount\\]#is", array( &$this, 'check_commentscount'), $tpl->copy_template );
		}

		if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" alt=\"\" />" );
		else $tpl->set( '{group-icon}', "" );

		$tpl->set( '{group-name}', $user_group[$row['user_group']]['group_prefix'].$user_group[$row['user_group']]['group_name'].$user_group[$row['user_group']]['group_suffix'] );

		if ( count($this->extras_rules) ) {

			foreach ($this->extras_rules as $rules) {

				if ($rules[0] == 'set') {

					$tpl->set( $rules[1], $rules[2] );

				} else {

					$tpl->set_block( $rules[1], $rules[2] );
				}

			}


		}

		if ($config['allow_links'] AND function_exists('replace_links') AND isset($replace_links['comments'])) $row['text'] = replace_links ( $row['text'], $replace_links['comments'] );
		
		$row['text'] = stripslashes( $row['text'] );
		
		if( $this->customshow ) {
			
			$row['text'] = preg_replace ( "#\[hide(.*?)\](.+?)\[/hide\]#is", "", $row['text'] );
			
			$tpl->set( '{comment}', $row['text'] );
			
		} else {
			
			if (stripos ( $row['text'], "[hide" ) !== false ) {
				
				$row['text'] = preg_replace_callback ( "#\[hide(.*?)\](.+?)\[/hide\]#is", 
					function ($matches) use ($member_id, $user_group, $lang) {
						
						$matches[1] = str_replace(array("=", " "), "", $matches[1]);
						$matches[2] = $matches[2];
		
						if( $matches[1] ) {
							
							$groups = explode( ',', $matches[1] );
		
							if( in_array( $member_id['user_group'], $groups ) OR $member_id['user_group'] == "1") {
								return $matches[2];
							} else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
							
						} else {
							
							if( $user_group[$member_id['user_group']]['allow_hide'] ) return $matches[2]; else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
							
						}
		
				}, $row['text'] );
			}
			
			$tpl->set( '{comment}', "<div id='comm-id-" . $row['id'] . "'>" . $row['text'] . "</div>" );
		}

		if ( preg_match( "#\\{comment limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches ) ) {
			$count= intval($matches[1]);
			
			$row['text'] = preg_replace( "#<!--QuoteBegin(.*)<!--QuoteEEnd-->#is", '', $row['text'] );
			$row['text'] = preg_replace( "#<!--dle_spoiler-->(.+?)<!--spoiler_text-->#is", '', $row['text'] );
			$row['text'] = preg_replace( "#<!--dle_spoiler (.+?) -->(.+?)<!--spoiler_text-->#is", '', $row['text'] );
			$row['text'] = str_replace( "<!--spoiler_text_end--></div><!--/dle_spoiler-->", '', $row['text'] );
			$row['text'] = str_replace( "</p><p>", " ", $row['text'] );
			$row['text'] = strip_tags( $row['text'], "<br>" );
			$row['text'] = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $row['text'] ) ) ) ));
			
			if( !$row['text'] ) $row['text'] = $lang['comments_empty'];
			
			if( $count AND dle_strlen( $row['text'], $config['charset'] ) > $count ) {
						
				$row['text'] = dle_substr( $row['text'], 0, $count, $config['charset'] );
						
				if( ($temp_dmax = dle_strrpos( $row['text'], ' ', $config['charset'] )) ) $row['text'] = dle_substr( $row['text'], 0, $temp_dmax, $config['charset'] );

			}
	
			$tpl->set( $matches[0], $row['text'] );
	
		}
			
		$tpl->compile( 'comments' );
		
		return $tpl->result['comments'];
		
	}
	
	function build_customcomments( $tpl, $template ) {
		
		$this->customshow = true;
		
		$tpl->load_template( $template );
		
		if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) $this->xfound = true;
		else $this->xfound = false;

		if( $this->xfound ) $this->xfields = xfieldsload( true );
		
		$rows = array();

		$sql_result = $this->db->query(  $this->query );
		
		while ( $row = $this->db->get_row( $sql_result ) ) {
			$rows[$row['id']] = array ();
		
			foreach ( $row as $key => $value ) {
				if ($key == "parent" AND $value == 0 ) $value = false;
				$rows[$row['id']][$key] = $value;
			}				
		}
			
		$this->db->free( $sql_result );
		unset($row);
		
		if ( count( $rows ) ) {
			
			return $this->compile_comments($tpl, $rows, 'lastcomments');

		}
		
	}
	
	function build_comments( $template, $area, $allow_cache = false, $re_url = false ) {
		global $config, $tpl, $is_logged, $member_id, $user_group, $lang, $ajax_adds, $dle_tree_comments, $dle_login_hash;

		$tpl->load_template( $template );

		if ( $area == "news" OR ( $area == 'ajax' AND !isset($ajax_adds) ) ) {
			$build_full_news = true;
		} else $build_full_news = false;
		
		
		$tpl->copy_template = "<div id='comment-id-{id}'>" . $tpl->copy_template . "</div>";
		$tpl->template = "<div id='comment-id-{id}'>" . $tpl->template . "</div>";

		if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) $this->xfound = true;
		else $this->xfound = false;

		if( $this->xfound ) $this->xfields = xfieldsload( true );

		$rows = false;

		if ( $allow_cache ) $rows = dle_cache ( "comm_".$allow_cache, $this->query );
		
		if( $rows ) {
	
			$rows = json_decode($rows, true);
	
		}

		if( is_array($rows) ) {

			$full_cache = true;
			
		} else {
			
			$rows = array();

			$sql_result = $this->db->query(  $this->query );
			
			while ( $row = $this->db->get_row( $sql_result ) ) {
				$rows[$row['id']] = array ();
		
				foreach ( $row as $key => $value ) {
					if ($key == "parent" AND $value == 0 ) $value = false;
					$rows[$row['id']][$key] = $value;
				}				
			}
			
			$this->db->free( $sql_result );
			unset($row);
			
			if ( $build_full_news AND $config['tree_comments'] ) {
				$rows = $this->build_tree($rows);
				
				if( $config['comm_msort'] == "DESC" ) $rows = array_reverse($rows, true);
			}
			
			if ( $allow_cache ) create_cache ( "comm_".$allow_cache, json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), $this->query );
			
		}

		if ( $build_full_news AND count($rows) ) {
			$this->total_comments = count($rows);
			if( $this->cstart < $this->total_comments ) $rows = array_slice($rows, $this->cstart, $this->comments_per_pages, true); else $rows = array();
		}

		if ( count( $rows ) ) {
			
			if ( $build_full_news AND $config['tree_comments'] ) {
				$dle_tree_comments = 1;				
				$tpl->result['comments'] = "<ol class=\"comments-tree-list\">".$this->compile_tree($rows, $area)."</ol>";
			} else {
				
				$tpl->result['comments'] = $this->compile_comments($tpl, $rows, $area);
			}


		} else {

			if ($config['seo_control']  AND $_GET['cstart'] AND $re_url) {

				$re_url = parse_url($re_url, PHP_URL_PATH);
				header("HTTP/1.0 301 Moved Permanently");
				header("Location: {$re_url}");
				die("Redirect");

			}

			$tpl->result['comments'] = "";

		}

		$tpl->clear();
		
		if ($area != 'ajax')
			$tpl->result['comments'] = "<div id=\"comment\"></div>" . $tpl->result['comments'];
				
		if($config['comments_lazyload'] AND $area != 'ajax' AND $this->total_comments > $this->comments_per_pages) {

			$tpl->result['comments'] .= "\n<div class=\"ajax_comments_area\"><div class=\"ajax_loaded_comments\"></div><div class=\"ajax_comments_next\"></div></div>\n";

		}
		
		if ($area == 'news' AND $config['comm_msort'] == "DESC" )
			$tpl->result['comments'] = "\n<div id=\"dle-ajax-comments\"></div>\n" . $tpl->result['comments'];

		if ($area == 'news' AND $config['comm_msort'] == "ASC" )
			$tpl->result['comments'] .= "\n<div id=\"dle-ajax-comments\"></div>\n";

		if ($area != 'ajax' AND $user_group[$member_id['user_group']]['del_allc'] AND !$user_group[$member_id['user_group']]['edit_limit'])
			$tpl->result['comments'] .= "\n<div class=\"mass_comments_action\">{$lang['mass_comments']}&nbsp;<select name=\"mass_action\"><option value=\"\">{$lang['edit_selact']}</option><option value=\"mass_combine\">{$lang['edit_selcomb']}</option><option value=\"mass_delete\">{$lang['edit_seldel']}</option></select>&nbsp;&nbsp;<input type=\"submit\" class=\"bbcodes\" value=\"{$lang['b_start']}\" /></div>\n<input type=\"hidden\" name=\"do\" value=\"comments\" /><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\" /><input type=\"hidden\" name=\"area\" value=\"{$area}\" />";

			
		if ($area != 'ajax')
			$tpl->result['comments'] = "<form method=\"post\" name=\"dlemasscomments\" id=\"dlemasscomments\"><div id=\"dle-comments-list\">\n" . $tpl->result['comments']. "</div></form>\n";

		if ( strpos ( $tpl->result['content'], "<!--dlecomments-->" ) !== false ) {

			$tpl->result['content'] = str_replace ( "<!--dlecomments-->", $tpl->result['comments'], $tpl->result['content'] );

		} else {

			$tpl->result['content'] .= $tpl->result['comments'];

		}

	}

	function build_navigation( $template, $alternative_link, $link, $re_url = false ) {
		global $tpl, $config, $lang, $news_id, $js_array, $onload_scripts, $canonical;

		if( $this->total_comments <= $this->comments_per_pages ) return;

		$PHP_SELF = $config['http_home_url'] . "index.php";

		if($config['comments_lazyload'] AND $news_id ) {

			$js_array[] = "engine/classes/js/waypoints.js";
			$enpages_count = @ceil( $this->total_comments / $this->comments_per_pages );

			$onload_scripts[] = <<<HTML
	var dle_news_id= '{$news_id}';
	var total_comments_pages= '{$enpages_count}';
	var current_comments_page= '1';

	$('#dle-ajax-comments').waypoint(function() {

		if (current_comments_page < total_comments_pages ) {

			Waypoint.disableAll();
			current_comments_page ++;
			ShowLoading('');

			$.get(dle_root + "engine/ajax/controller.php?mod=comments", { cstart: current_comments_page, news_id: dle_news_id, skin: dle_skin, massact:'disable' }, function(data){

				$(".ajax_loaded_comments").append(data.comments);

				HideLoading('');
				Waypoint.refreshAll();
				setTimeout(function() { Waypoint.enableAll(); }, 500);

			}, "json");

		} else {

			Waypoint.destroyAll();
		}


	}, {
	  offset: 'bottom-in-view'
	});
HTML;

			return;

		}

		if( isset( $_GET['cstart'] ) ) $this->cstart = intval( $_GET['cstart'] );
		if( !$this->cstart OR $this->cstart < 0 ) $this->cstart = 1;

		$news_id = intval($news_id) > 0 ? intval($news_id): 0;

		$tpl->load_template( $template );

		if( $this->cstart > 1 ) {
			$prev = $this->cstart - 1;

			if( $config['allow_alt_url'] AND $alternative_link) {

				if ( $prev == 1 AND $re_url ) $url = $re_url."#comment";
				else $url = str_replace ("{page}", $prev, $alternative_link );

				if ( $config['comments_ajax'] AND $news_id ) {
					$url = str_replace($config['http_home_url'], '/', $url);
					$go_page = " onclick=\"CommentsPage('{$prev}', '{$news_id}', '{$url}'); return false;\"";
				} else $go_page = "";

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $url . "\"{$go_page}>\\1</a>" );

			} else {

				if ( $prev == 1 AND $re_url ) $url = $re_url."#comment";
				else $url = "$PHP_SELF?cstart={$prev}&amp;{$link}#comment";

				if ( $config['comments_ajax'] AND $news_id ) {
					$url = str_replace($config['http_home_url'], '/', $url);
					$go_page = " onclick=\"CommentsPage('{$prev}', '{$news_id}', '{$url}'); return false;\"";
				} else $go_page = "";

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"{$url}\"{$go_page}>\\1</a>" );
			}

		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
			$no_prev = TRUE;
		}

		if( $this->comments_per_pages ) {

			$enpages_count = @ceil( $this->total_comments / $this->comments_per_pages );
			$pages = "";

			if($this->cstart != 1 AND $canonical ) {
				
				if( $config['allow_alt_url'] AND $alternative_link ) {
					
					$canonical = str_replace ("{page}", $this->cstart, $alternative_link );
					$canonical = str_replace ("#comment", "", $canonical );

				} else {
					
					$canonical = "{$PHP_SELF}?cstart={$this->cstart}&".str_replace('&amp;', '&', $link);
					
				}
					
			}

			if( $enpages_count <= 10 ) {

				for($j = 1; $j <= $enpages_count; $j ++) {

					if( $j != $this->cstart  ) {

						if( $config['allow_alt_url'] AND $alternative_link ) {

							if ( $j == 1 AND $re_url ) $url = $re_url."#comment";
							else $url = str_replace ("{page}", $j, $alternative_link );

							if ( $config['comments_ajax'] AND $news_id ) {
								$url = str_replace($config['http_home_url'], '/', $url);
								$go_page = " onclick=\"CommentsPage('{$j}', '{$news_id}', '{$url}'); return false;\"";
							} else $go_page = "";

							$pages .= "<a href=\"" . $url . "\"{$go_page}>$j</a> ";

						} else {

							if ( $j == 1 AND $re_url ) $url = $re_url."#comment";
							else $url = "{$PHP_SELF}?cstart={$j}&amp;{$link}#comment";

							if ( $config['comments_ajax'] AND $news_id ) {
								$url = str_replace($config['http_home_url'], '/', $url);
								$go_page = " onclick=\"CommentsPage('{$j}', '{$news_id}', '{$url}'); return false;\"";
							} else $go_page = "";

							$pages .= "<a href=\"{$url}\"{$go_page}>$j</a> ";
						}

					} else {

						$pages .= "<span>$j</span> ";
					}

				}

			} else {

				$start = 1;
				$end = 10;
				$nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";

				if( $this->cstart  > 0 ) {

					if( $this->cstart  > 6 ) {

						$start = $this->cstart  - 4;
						$end = $start + 8;

						if( $end >= $enpages_count-1 ) {
							$start = $enpages_count - 9;
							$end = $enpages_count - 1;
						}

					}

				}
				
				if( $end >= $enpages_count-1 ) $nav_prefix = ""; else $nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";

				if( $start >= 2 ) {

					if( $re_url ) {

						$url = $re_url."#comment";

					} else $url = "{$PHP_SELF}?cstart=1&amp;{$link}#comment";

					if ( $config['comments_ajax'] AND $news_id ) {
						$url = str_replace($config['http_home_url'], '/', $url);
						$go_page = " onclick=\"CommentsPage('1', '{$news_id}', '{$url}'); return false;\"";
					} else $go_page = "";

					if( $start >= 3 ) $before_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> "; else $before_prefix = "";
					
					$pages .= "<a href=\"{$url}\"{$go_page}>1</a> ".$before_prefix;

				}

				for($j = $start; $j <= $end; $j ++) {

					if( $j != $this->cstart ) {

						if( $config['allow_alt_url'] AND $alternative_link) {

							if ( $j == 1 AND $re_url ) $url = $re_url."#comment";
							else $url = str_replace ("{page}", $j, $alternative_link );

							if ( $config['comments_ajax'] AND $news_id ) {
								$url = str_replace($config['http_home_url'], '/', $url);
								$go_page = " onclick=\"CommentsPage('{$j}', '{$news_id}', '{$url}'); return false;\"";
							} else $go_page = "";

							$pages .= "<a href=\"" . $url . "\"{$go_page}>$j</a> ";

						} else {

							if ( $j == 1 AND $re_url ) $url = $re_url."#comment";
							else $url = "{$PHP_SELF}?cstart={$j}&amp;{$link}#comment";

							if ( $config['comments_ajax'] AND $news_id ) {
								$url = str_replace($config['http_home_url'], '/', $url);
								$go_page = " onclick=\"CommentsPage('{$j}', '{$news_id}', '{$url}'); return false;\"";
							} else $go_page = "";

							$pages .= "<a href=\"{$url}\"{$go_page}>$j</a> ";
						}
					} else {

						$pages .= "<span>$j</span> ";
					}

				}

				if( $this->cstart != $enpages_count ) {

					if( $config['allow_alt_url'] AND $alternative_link) {

						$url = str_replace ("{page}", $enpages_count, $alternative_link );

						if ( $config['comments_ajax'] AND $news_id ) {
							$url = str_replace($config['http_home_url'], '/', $url);
							$go_page = " onclick=\"CommentsPage('{$enpages_count}', '{$news_id}', '{$url}'); return false;\"";
						} else $go_page = "";

						$pages .= $nav_prefix . "<a href=\"" . $url . "\"{$go_page}>{$enpages_count}</a>";

					} else {
						
						$url = "{$PHP_SELF}?cstart={$enpages_count}&amp;{$link}#comment";

						if ( $config['comments_ajax'] AND $news_id ) {
							$url = str_replace($config['http_home_url'], '/', "{$PHP_SELF}?cstart={$enpages_count}&amp;{$link}#comment");
							$go_page = " onclick=\"CommentsPage('{$enpages_count}', '{$news_id}', '{$url}'); return false;\"";
						} else $go_page = "";

						$pages .= $nav_prefix . "<a href=\"{$url}\"{$go_page}>{$enpages_count}</a>";
					}

				} else
					$pages .= "<span>{$enpages_count}</span> ";

			}

			$tpl->set( '{pages}', $pages );

		}

		if( $this->cstart < $enpages_count ) {


			$next_page = $this->cstart + 1;

			if( $config['allow_alt_url'] AND $alternative_link ) {

				$url = str_replace ("{page}", $next_page, $alternative_link );

				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $url . "\"{$go_page}>\\1</a>" );

			} else $url = "{$PHP_SELF}?cstart=$next_page&amp;{$link}#comment";

			if ( $config['comments_ajax'] AND $news_id ) {
				$url = str_replace($config['http_home_url'], '/', $url);
				$go_page = " onclick=\"CommentsPage('{$next_page}', '{$news_id}', '{$url}'); return false;\"";
			} else $go_page = "";

			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $url . "\"{$go_page}>\\1</a>" );

		} else {

			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
			$no_next = TRUE;

		}

		$tpl->compile( 'commentsnavigation' );

		$tpl->clear();

		if ( strpos ( $tpl->result['content'], "<!--dlenavigationcomments-->" ) !== false ) {

			$tpl->result['content'] = str_replace ( "<!--dlenavigationcomments-->", "<div class=\"dle-comments-navigation\">".$tpl->result['commentsnavigation']."</div>", $tpl->result['content'] );

		} else {

			$tpl->result['content'] .= "<div class=\"dle-comments-navigation\">".$tpl->result['commentsnavigation']."</div>";

		}

	}

	function check_group( $matches=array() ) {

		$groups = $matches[2];
		$block = $matches[3];

		if ($matches[1] == "commentsgroup") $action = true; else $action = false;

		$groups = explode( ',', $groups );

		if( $action ) {

			if( !in_array( $this->comments_group, $groups ) ) return "";

		} else {

			if( in_array( $this->comments_group, $groups ) ) return "";

		}


		return $block;

	}

	function check_commentscount( $matches=array() ) {

		$block = $matches[3];

		$counts = explode( ',', $matches[2] );

	    if( $matches[1] == "commentscount" ) {

			if( !in_array( $this->intern_count, $counts ) ) return "";

		} else {

			if( in_array( $this->intern_count, $counts ) ) return "";

		}

		return $block;

	}
}
?>