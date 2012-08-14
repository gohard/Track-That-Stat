<?php
error_reporting(E_ALL || ~E_NOTICE || ~E_WARNING);

include("../../../wp-config.php");
global $wpdb;
 /* Gets all users with lastActiveTime within the last 100 seconds */
$query = "select list_ip_address from ".$wpdb->prefix."tts_settings";
$Results = $wpdb->get_results($query, OBJECT);

$list_ip_addresses = $Results[0]->list_ip_address;

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
//echo('<span style="display:none;">'.$query.'</span>');
$result_online = $wpdb->get_results($query, OBJECT);


$query = "select * from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  ip_address not in(".$list_ips.") order by time_last_visited desc limit 7";
$result = $wpdb->get_results($query, OBJECT);


?>

<table width="98%" border="0" cellspacing="0" cellpadding="0" style="padding-top:2px;">
  <tr>
	<td width="16" class="t-top-left">&nbsp;</td>
	<td width="179" class="t-top-left-next align_left">Time</td>
	<td width="99" class="t-top-left-next">IP Address</td>
	<td width="168" class="t-top-left-next" colspan="2">Country</td>
	<td width="18" class="t-top-right">&nbsp;</td>
  </tr>

<?php
$css_class="";
//$result=array();
$result_count=count($result);
$count0;
foreach($result as $res)
{
		$count++;
		$time_visited = $res->time_last_visited;
			if (strstr($res->country,"Private")!==false) {
				$res->country='-';
			}


			if (strstr($res->country,"Unknown")!==false) {
				$res->country='-';
			}
?>
	  <tr>
		<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
		<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php echo(date("H:i:s",$time_visited));?></span></td>
		<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($res->ip_address);?></td>
		<td class="t-mid-left-next<?php echo($css_class);?>" colspan="2"><?php echo($res->country);?></td>
		<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
	  </tr>
<?php
	if($css_class=="") {
		$css_class="1";
	}
	else {
		$css_class="";
	}
}
		if($result_count<7) {

			if($result_count==0) {
				$css_class="";
			}
			for ($i=1; $i <=7-$result_count ; $i++) {
		?>		
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="link">&nbsp;</span></td>
				<td class="t-mid-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-mid-left-next<?php echo($css_class);?>" colspan="2">&nbsp;</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
		<?php
				if($css_class=="") {
					$css_class="1";
				}
				else {
					$css_class="";
				}
			}	
		}


if($css_class=="") {
	$css_class="1";
}
else {
	$css_class="";
}
?>

  <tr>
	<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
	<td class="t-bot-left-next<?php echo($css_class);?>" colspan="4"><a href="#" class="visitor" id="listBox">  Visitors Online&nbsp;&nbsp;&nbsp;<?php echo count($result_online); ?></a></td>
	<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
  </tr>
</table>