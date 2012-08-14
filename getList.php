<?php
error_reporting(E_ALL || ~E_NOTICE || ~E_WARNING);
include("../../../wp-config.php");
global $wpdb;
 /* Gets all users with lastActiveTime within the last 100 seconds */
		$query_ip = "select list_ip_address from ".$wpdb->prefix."tts_settings";
		$Results_ip = $wpdb->get_results($query_ip, OBJECT);

		$list_ip_addresses = $Results_ip[0]->list_ip_address;

		$list_ip_addresses = explode(",",$list_ip_addresses);
		$list_ips=array();
		foreach($list_ip_addresses as $ip)
		{
			$list_ips[] = "'".$ip."'";
		}
		$list_ips = implode(",",$list_ips);

$online_time = 10;
$seconds_ago = (current_time( 'timestamp') - absint((100)));
$query = "select DISTINCT(ip_address) from ".$wpdb->prefix."tts_online_status where last_active_time > '$seconds_ago' AND ip_address not in(".$list_ips.") ";
$result = $wpdb->get_results($query, OBJECT);

$output .= "Visitors Online&nbsp;&nbsp;&nbsp;".count($result);

print $output;
?>