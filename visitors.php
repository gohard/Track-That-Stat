<?php
class Visitors{
	function visitors_online()
	{
		global $wpdb;
		?>
		<table width="98%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="16" class="t-top-left">&nbsp;</td>
			<td width="179" class="t-top-left-next align_left">Time</td>
			<td width="99" class="t-top-left-next">IP Address</td>
			<td width="168" class="t-top-left-next" colspan="2">Country</td>
			<td width="18" class="t-top-right">&nbsp;</td>
		  </tr>
		<?php

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

		$query = "select * from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and ip_address not in(".$list_ips.") order by time_last_visited desc limit 5";
		$result = $wpdb->get_results($query, OBJECT);
		$css_class="";
		$result_count=count($result);
		$count=0;
		foreach($result as $res)
		{
			$count++;
				$time_visited = $res->time_last_visited;

			if($res->country=='-' || empty($res->country)) {
				
				$country_info=getUserGeoInfo($res->ip_address);

				$res->country=$country_info->countryName;


				$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET country=\"".$res->country."\" where id=\"".$res->id."\"");
			}
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
	
		$online_time = 15;
		$mintes_ago = (current_time( 'timestamp') - absint(($online_time * 60)));
		

		$online_time = 10;
		$seconds_ago = (current_time( 'timestamp') - absint((10)));
		$query = "select count(*) as num_online_users from ".$wpdb->prefix."tts_online_status where last_active_time > '$seconds_ago'";
		$result = $wpdb->get_results($query, OBJECT);
		
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
				echo "<script>";
				?>
		setInterval("getList()", 50000) // Get users-online every 50 seconds

		function getList() {
		  jQuery.post("<?php echo $path; ?>getList.php", function(list) {
		  document.getElementById('listBox').innerHTML = list;
		  });
		}
		<?php
		echo "</script>";

		if($result_count<5) {

			if($result_count==0) {
				$css_class="";
			}
			for ($i=1; $i <=5-$result_count ; $i++) {
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
        <td class="t-bot-left-next<?php echo($css_class);?>" colspan="4"><a href="#" class="visitor" id="listBox">Visitors Online <?php echo $result[0]->num_online_users; ?></a></td>
        <td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" height="30">&nbsp;</td>
        <td colspan="3" align="right"><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor" class="more-d" title='More Details' target='_blank'>More Details</a></td>
      </tr>
   </table>

<?php


	}

	function view_visitors_data($duration,$fromDate,$toDate)
	{
		global $wpdb;
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<SCRIPT type='text/javascript' src='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118'></script>";
		echo "<link type='text/css' rel='stylesheet' href='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112' media='screen'></LINK>";
				echo "<script>";
				?>

		function getListDetailed() {
		var date = new Date();
		  jQuery.post("<?php echo $path; ?>getListDetailed.php?time="+date.getTime(), function(list) {
			jQuery("#visitor_container").html(list);
		  });
		}

		jQuery(document).ready(function(){
			getListDetailed();
			setInterval("getListDetailed()", 50000) // Get users-online every 50 seconds
		
		});
		<?php
		echo "</script>";

		$pagingPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/pager.php";
		include($pagingPath);

		
		
		$to_date = date("Y M d",current_time( 'timestamp'));


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


		if($fromDate)
		{
			$frDate = date("Y-m-d",strtotime($fromDate));
			$tDate = date("Y-m-d",strtotime($toDate));
			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between '$frDate' and '$tDate' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between '$frDate' and '$tDate' and ip_address not in(".$list_ips.") order by time_last_visited desc";
		}
		else
		{
			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and ip_address not in(".$list_ips.")";
			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and ip_address not in(".$list_ips.") order by time_last_visited desc";
		}


		

		if($duration == "today")
		{
			$from_date = date("Y M d",current_time( 'timestamp'));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  = '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  = '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.") order by time_last_visited desc";
		}
		

		if($duration == "yesterday")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
			$to_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) and ip_address not in(".$list_ips.") order by time_last_visited desc";			
		}
		

		if($duration == "7days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp'))));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.") order by time_last_visited desc";			
		}
		

		if($duration == "14days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp'))));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.") order by time_last_visited desc";			
		}
		

		if($duration == "30days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp'))));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.") order by time_last_visited desc";			
		}
		

		if($duration == "60days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp'))));

			$query1 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d')  between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.")";

			$query2 = "select DATE_FORMAT( FROM_UNIXTIME( time_last_visited ) , '%Y-%m-%d' ) AS visitor_date, wp_tts_visitors.* from ".$wpdb->prefix."tts_visitors where page_id!=0 and page_id!='' and  DATE_FORMAT(FROM_UNIXTIME(time_last_visited),'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and ip_address not in(".$list_ips.") order by time_last_visited desc";			
		}
		


		

		if($duration != "")
		{
		}
		elseif($fromDate || $toDate) {
			if($fromDate && $toDate) {
			}
			elseif($fromDate) {
			}
			else {
			}
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=visitor&fromDate=".$fromDate."&toDate=".$toDate;
		}


?>
    <div class="graph-con">
    	<div class="graph-left">
        	<div class="graph-left1"></div>
            <div class="graph-left2" align="center">
            <div class="left-title">View Visitor Details</div>
            	<ul class="memu-ul">
                	<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=today">Today</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=yesterday">Yesterday</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=7days">Last 7 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=14days">Last 14 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=30days">Last 30 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=visitor&duration=60days">Last 60 Days</a></li>
                </ul>
            </div>
            <div class="graph-left3"></div>
        </div>
      <div class="graph-right">
        	<div class="graph-right1"></div>
            <div class="graph-right2" align="center" id="visitor_container"></div>
            <div class="graph-right3"></div>
        </div> 


    </div>
<div class="cont" align="center">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td colspan="8" height="50" valign="top">
    <div class="select"><form name='pageForm' method='get' action='admin.php'>Show <select name='paging_records'  class="Select_bg" onchange='document.pageForm.submit()'>
	<?php

		$position = strpos($_SERVER['REQUEST_URI'],"?");
		$str = substr($_SERVER['REQUEST_URI'],$position+1);
		$str_a = explode("&",$str);
		
		if($_REQUEST['paging_records'])
		{
			$limit = $_REQUEST['paging_records'];
		}
		else
		{
		
			if(isset($_COOKIE['paging_records_visits'])) {
				
				$limit = $_COOKIE['paging_records_visits'];
			}
			else {
				
				$limit = 10;

			}
			
		}

		

		$paging_array = get_paging_array();

		foreach($paging_array as $pages)
		{
			if($limit==$pages)
			{
				echo "<option value='".$pages."' selected>".$pages."</option>";
			}
			else
			{
				echo "<option value='".$pages."'>".$pages."</option>";
			}
		}		
	?>
        </select> Records Per Page
	<?php

		foreach($str_a as $key=>$value)
		{
			$hidden_values = explode('=',$value);
			if(($hidden_values[0] == "paging_records") || ($hidden_values[0] == "pg") || ($hidden_values[0] == "submit"))
			{
				continue;
			}
			else
			{
				echo "<input type='hidden' name='".$hidden_values[0]."' value='".urldecode($hidden_values[1])."'>";
			}
		}

		

		$p = new Pager;

		/* Show many results per page */



		
		

		

		/* Find the start depending on $_GET['page'] (declared if it's null) */
		$start = $p->findStart($limit);
		 
		/* Find the number of rows returned from a query; Note: Do Not use a limit clause */
		$count = mysql_num_rows(mysql_query($query1));

		/* Find the number of pages based on $count and $limit */
		$pages = $p->findPages($count, $limit);
		 
		/* Now we use the LIMIT clause to grab a range of rows */
		
		$query2 = $query2." LIMIT ".$start.", ".$limit;
		$result = $wpdb->get_results($query2, OBJECT);
		 
		/* Now get the page list and echo it */
		$pagelist = $p->pageList($_GET['pg'], $pages);

		
		
		
		

	?>	</form>
		</div>
    <div class="select-r"> <img src="<?php echo($path); ?>images/vis.png" alt="" /></div>
            <div class="clear">
                <div class="paginesion">
				<?php
				if($pages > 1){
					echo $pagelist; 
				}
				?>

           </div>
            </div>

    </td>
  </tr>
          <tr>
            <td width="16" class="t-top-left">&nbsp;</td>
            <td width="190" class="t-top-left-next align_left"><span class="align"> Date </span></td>
            <td width="100" class="t-top-left-next align_left">Time</td>
            <td width="139" class="t-top-left-next align_left">IP Address</td>
            <td width="140" class="t-top-left-next align_left">Country</td>
            <td width="173" class="t-top-left-next align_left">Page</td>
            <td width="125" class="t-top-left-next align_left">Browser</td>
            <td width="78" class="t-top-left-next align_left">Platform</td>
            <td width="19" class="t-top-right">&nbsp;</td>
          </tr>

  <?php
		$count=0;
		$css_class="";

		foreach($result as $res)
		{

			$count++;
			$time_visited = $res->time_last_visited;

			$post_data = get_post($res->page_id);
			$post_url = get_permalink($res->page_id);
						
			if(strlen($post_data->post_title)>15) $post_title = substr_replace($post_data->post_title,'...',15); else $post_title = $post_data->post_title;

			if(strlen($res->browser)>15) $browser = substr_replace($res->browser,'...',15); else $browser = $res->browser;
			if(strlen($res->platform)>8) $platform = substr_replace($res->platform,'...',8); else $platform = $res->platform;

			if($res->country=='-' || empty($res->country)) {
				
				$country_info=getUserGeoInfo($res->ip_address);

				$res->country=$country_info->countryName;


				$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET country=\"".$res->country."\" where id=\"".$res->id."\"");
			}
			if (strstr($res->country,"Private")!==false) {
				$res->country='-';
			}

			if (strstr($res->country,"Unknown")!==false) {
				$res->country='-';
			}

			if(strlen($res->country)>17) $country = substr_replace($res->country,'... ',17); else $country = $res->country;


			if($post_title=='') {
				$post_title='/';
				$post_url=site_url();			
			}
			if($post_data->post_title=='') {
				$post_data->post_title='/';
			}
			if($count!=count($result)) {


?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>

				<td class="t-mid-left-next<?php echo($css_class);?> align_left"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php echo(date("F d, Y",strtotime($res->visitor_date)));?></span></td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?> align_left">
					<?php echo(date("H:i:s",$time_visited)); ?> 
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?> align_left"><?php echo($res->ip_address); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->country);?>"><?php echo($country); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?> align_left">
					<?php 
						if($res->page_id==0 || $res->page_id=='') {
					?>
						&nbsp;
					<?php
						}
						else {
					?>
					<a href="<?php echo($post_url);?>" class="link" title="<?php echo($post_data->post_title);?>"><?php echo($post_title);?></a>
					<?php
							
						}
					?>
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->browser);?>">
				
					<?php echo($browser);?>
					
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->platform);?>">
				
					<?php echo($platform);?>
					
				</td>

				 <td class="t-mid-right<?php echo($css_class);?> align_left">&nbsp;</td>

			  </tr>	
<?php
			}
			else {
			if($css_class=="") {
				$css_class="1";
			}
			else {
				$css_class="";
			}
?>
			  <tr>
			   <td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-bot-left-next<?php echo($css_class);?> align_left"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php echo(date("F d, Y",strtotime($res->visitor_date)));?></span></td>
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?> align_left">
					<?php echo(date("H:i:s",$time_visited)); ?> 
				</td>
				<td class="t-bot-left-next<?php echo($css_class);?> align_left"><?php echo($res->ip_address); ?></td>
				<td class="t-bot-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->country);?>"><?php echo($country); ?></td>
				<td class="t-bot-left-next<?php echo($css_class);?> align_left">
				<?php 
					if($res->page_id==0 || $res->page_id=='') {
				?>
					&nbsp;
				<?php
					}
					else {
				?>
				<a href="<?php echo($post_url);?>" class="link" title="<?php echo($post_data->post_title);?>"><?php echo($post_title);?></a>
				<?php
					}
				?>
				</td>
		 		<td class="t-bot-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->browser);?>">					<?php echo($browser);?>
</td>
		 		<td class="t-bot-left-next<?php echo($css_class);?> align_left" title="<?php echo($res->platform);?>">					<?php echo($platform);?>
</td>

               <td class="t-bot-right<?php echo($css_class);?> align_left">&nbsp;</td>

			  </tr>	
<?php
			}
			
			if($css_class=="") {
				$css_class="1";
			}
			else {
				$css_class="";
			}
		}
?><tr><td colspan="8">
            <div class="clear">
                <div class="paginesion">
				<?php
				if($pages > 1){
					echo $pagelist; 
				}
				?>

           </div>
            </div>
</td>
</tr>
</table>
<?php

	}
}
?>