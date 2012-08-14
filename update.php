<?php
error_reporting(E_ALL || ~E_NOTICE || ~E_WARNING);

include("../../../wp-config.php");
global $wpdb;
$cookie_ip=base64_decode($_COOKIE['siteVisited']);
$current_ip=$_SERVER['REMOTE_ADDR'];

if($cookie_ip==$current_ip) {
	$query = "select count(*) as num_ip from ".$wpdb->prefix."tts_online_status where ip_address='".$_SERVER['REMOTE_ADDR']."'";

}
else {
	$query = "select count(*) as num_ip from ".$wpdb->prefix."tts_online_status where ip_address='".$_SERVER['REMOTE_ADDR']."' OR ip_address='".$current_ip."'";
}

$Results		=	$wpdb->get_results($query, OBJECT);
$Results[0]->num_ip;
if($cookie_ip==$current_ip) {

	if($Results[0]->num_ip > 0)
	{
		$wpdb->query("UPDATE ".$wpdb->prefix."tts_online_status SET last_active_time = '".current_time( 'timestamp')."' where ip_address='".$_SERVER['REMOTE_ADDR']."'");
		//echo("UPDATE ".$wpdb->prefix."tts_online_status SET last_active_time = '".current_time( 'timestamp')."' where ip_address='".$_SERVER['REMOTE_ADDR']."'");
	}
	else
	{
		$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_online_status SET last_active_time = '".current_time( 'timestamp')."',ip_address='".$_SERVER['REMOTE_ADDR']."'");
	}

}
else {
	$wpdb->query("UPDATE ".$wpdb->prefix."tts_online_status SET last_active_time = '".current_time( 'timestamp')."' where ip_address='".$cookie_ip."'");
}
?>