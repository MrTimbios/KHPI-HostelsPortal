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
 File: calendar.php
-----------------------------------------------------
 Use: AJAX for calendar
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$PHP_SELF = $config['http_home_url'] . "index.php";

function cal($cal_month, $cal_year, $events) {
	global $f, $r, $year, $month, $config, $lang, $langdateshortweekdays, $PHP_SELF;
	
	$next = true;
	
	if( intval( $cal_year . $cal_month ) >= date( 'Ym' ) AND !$config['news_future'] ) $next = false;

	$cur_date=date( 'Ymj', time() );
	$cal_date = $cal_year.$cal_month;

	$cal_month = intval( $cal_month );
	$cal_year = intval( $cal_year );
	
	if( $cal_month < 0 ) $cal_month = 1;
	if( $cal_year < 0 ) $cal_year = 2008;
	
	$first_of_month = mktime( 0, 0, 0, $cal_month, 7, $cal_year );
	$maxdays = date( 't', $first_of_month ) + 1; // 28-31
	$prev_of_month = mktime( 0, 0, 0, ($cal_month - 1), 7, $cal_year );
	$next_of_month = mktime( 0, 0, 0, ($cal_month + 1), 7, $cal_year );
	$cal_day = 1;
	$weekday = date( 'w', $first_of_month ); // 0-6
	

	if( $config['allow_alt_url'] ) {
		
		$date_link['prev'] = '<a class="monthlink" onclick="doCalendar(' . date( "'m','Y'", $prev_of_month ) . ',\'right\'); return false;" href="' . $config['http_home_url'] . date( 'Y/m/', $prev_of_month ) . '" title="' . $lang['prev_moth'] . '">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
		$date_link['next'] = '&nbsp;&nbsp;&nbsp;&nbsp;<a class="monthlink" onclick="doCalendar(' . date( "'m','Y'", $next_of_month ) . ',\'left\'); return false;" href="' . $config['http_home_url'] . date( 'Y/m/', $next_of_month ) . '" title="' . $lang['next_moth'] . '">&raquo;</a>';
	
	} else {
		
		$date_link['prev'] = '<a class="monthlink" onclick="doCalendar(' . date( "'m','Y'", $prev_of_month ) . ',\'right\'); return false;" href="' . $PHP_SELF . '?year=' . date( "Y", $prev_of_month ) . '&amp;month=' . date( "m", $prev_of_month ) . '" title="' . $lang['prev_moth'] . '">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
		$date_link['next'] = '&nbsp;&nbsp;&nbsp;&nbsp;<a class="monthlink" onclick="doCalendar(' . date( "'m','Y'", $next_of_month ) . ',\'left\'); return false;" href="' . $PHP_SELF . '?year=' . date( "Y", $next_of_month ) . '&amp;month=' . date( "m", $next_of_month ) . '" title="' . $lang['next_moth'] . '">&raquo;</a>';
	
	}
	
	if( !$next ) $date_link['next'] = "&nbsp;&nbsp;&nbsp;&nbsp;&raquo;";
	
	$buffer = '<table id="calendar" class="calendar"><tr><th colspan="7" class="monthselect">' . $date_link['prev'] . langdate( 'F', $first_of_month, true ) . ' ' . $cal_year . $date_link['next'] . '</th></tr><tr>';
	
	$buffer = str_replace( $f, $r, $buffer );
	
	for($it = 1; $it < 6; $it ++) $buffer .= '<th class="workday">' . $langdateshortweekdays[$it] . '</th>';
		
	$buffer .= '<th class="weekday">' . $langdateshortweekdays[6] . '</th>';
	$buffer .= '<th class="weekday">' . $langdateshortweekdays[0] . '</th>';
	
	$buffer .= '</tr><tr>';
	
	if( $weekday > 0 ) {
		$buffer .= '<td colspan="' . $weekday . '">&nbsp;</td>';
	}
	
	while ( $maxdays > $cal_day ) {

		$cal_pos = $cal_date.$cal_day;

		if( $weekday == 7 ) {
			$buffer .= '</tr><tr>';
			$weekday = 0;
		}
		
		if( isset( $events[$cal_day] ) ) {
			$date['title'] = langdate( 'd F Y', $events[$cal_day], true );
			
			if( $weekday == '5' or $weekday == '6' ) {
				
				if( $config['allow_alt_url'] ) $buffer .= '<td '.(($cal_pos==$cur_date)?' class="day-active day-current" ':' class="day-active" ').'><a class="day-active" href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date['title'] . '">' . $cal_day . '</a></td>';
				else $buffer .= '<td '.(($cal_pos==$cur_date)?' class="day-active day-current" ':' class="day-active" ').'><a class="day-active" href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&amp;month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date['title'] . '">' . $cal_day . '</a></td>';
			
			} else {
				
				if( $config['allow_alt_url'] ) $buffer .= '<td '.(($cal_pos==$cur_date)?' class="day-active-v day-current" ':' class="day-active-v" ').'><a class="day-active-v" href="' . $config['http_home_url'] . '' . date( "Y/m/d", $events[$cal_day] ) . '/" title="' . $lang['cal_post'] . ' ' . $date['title'] . '">' . $cal_day . '</a></td>';
				else $buffer .= '<td '.(($cal_pos==$cur_date)?' class="day-active-v day-current" ':' class="day-active-v" ').'><a class="day-active-v" href="' . $PHP_SELF . '?year=' . date( "Y", $events[$cal_day] ) . '&amp;month=' . date( "m", $events[$cal_day] ) . '&day=' . date( "d", $events[$cal_day] ) . '" title="' . $lang['cal_post'] . ' ' . $date['title'] . '">' . $cal_day . '</a></td>';
			
			}
		} else {
			

			if( $weekday == "5" or $weekday == "6" ) {
				$buffer .= '<td '.(($cal_pos==$cur_date)?' class="weekday day-current" ':' class="weekday" ').'>' . $cal_day . '</td>';
			} else {
				$buffer .= '<td '.(($cal_pos==$cur_date)?' class="day day-current" ':' class="day" ').'>' . $cal_day . '</td>';
			}
		}
		
		$cal_day ++;
		$weekday ++;
	}
	
	if( $weekday != 7 ) {
		$buffer .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';
	}
	
	return $buffer . '</tr></table>';
}

$buffer = false;
$time = time();
$thisdate = date( "Y-m-d H:i:s", $time );
if( $config['no_date'] AND !$config['news_future'] ) $where_date = " AND date < '" . $thisdate . "'"; else $where_date = "";

$this_month = date( 'm', $time );
$this_year = date( 'Y', $time );

if( isset($_GET['month']) ) {
	
	if( intval ( $_GET['month'] ) < 1 OR intval ( $_GET['month'] ) > 12 ) $_GET['month'] = 1;
	$month = $db->safesql( sprintf("%02d", intval ( $_GET['month'] ) ) );
	
} else $month='';

if( isset($_GET['year']) ) {
	
	if( intval ( $_GET['year'] ) < 1970 ) $_GET['year'] = 1970;
	if( intval ( $_GET['year'] ) > 2100 ) $_GET['year'] = 2100;
	
	$year = intval( $_GET['year'] );
	
} else $year='';

$sql = "";

if( $year != '' AND $month != '' ) {

	if( ($year == $this_year AND $month < $this_month) OR ($year < $this_year) ) {

		$where_date = "";
		$approve = "";

	} else {
		$approve = " AND approve=1";
	}
	
	$sql = "SELECT DISTINCT DAYOFMONTH(date) as day FROM " . PREFIX . "_post WHERE date >= '{$year}-{$month}-01' AND date < '{$year}-{$month}-01' + INTERVAL 1 MONTH" . $approve . $where_date;
	
	
	$this_month = $month;
	$this_year = $year;

} else {
	
	$sql = "SELECT DISTINCT DAYOFMONTH(date) as day FROM " . PREFIX . "_post WHERE date >= '{$this_year}-{$this_month}-01' AND date < '{$this_year}-{$this_month}-01' + INTERVAL 1 MONTH AND approve=1" . $where_date;

}

	
$db->query( $sql );
	
while ( $row = $db->get_row() ) {
	$events[$row['day']] = strtotime( $this_year . "-" . $this_month . "-" . $row['day'] );
}
	
$db->free();
$db->close();

$buffer = cal( $this_month, $this_year, $events );

echo $buffer;

?>