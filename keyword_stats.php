<?php
class KeywordStats{
	//This function is used to display keywords data on dashboard
	function display_keyword_data() {
		
		echo "<table  width='100%'><tr><td colspan='2' style='line-height:30px;color:#333333' align='center'><strong>Top Search Terms</strong></td></tr>";
	

		//get total visitors
		$Results =$this-> get_total_keyword_visitors();

		$total_visitors = $Results[0]->total_visitors;

		$keywords_data = $this->get_keyword_visitors_data();
		$i=1;
		$first_three = array();
		$last_three = array();
		foreach($keywords_data as $keywordData)
		{
			$keyword = $keywordData->keyword;
			
			$percent_viewed = ($keywordData->visitors*100)/$total_visitors;

			$post_id = $keywordData->post_id;

			$post_data = get_post($post_id);
			
			$post_url = get_permalink($post_id);

			
			if($i++ <= "3")
			{
				$first_three[] = $keyword;
			}
			else
			{
				$last_three[] = $keyword;
			}
		}
		echo "<tr><td width='50%' valign='top'><table width='100%'>";
		$j = 1;
		foreach($first_three as $keyword)
		{
			echo "<tr><td width='9%' style='line-height:15px' valign='top'>".$j.".&nbsp;</td><td title='".$keyword."''>";if(strlen($keyword)>28) echo substr_replace($keyword,'...',28); else
				echo $keyword;
			echo "</td></tr>";
			$j++;
		}
		echo "</table></td><td valign='top'><table width='100%'>";
		foreach($last_three as $keyword)
		{
			echo "<tr><td width='9%' style='line-height:15px' valign='top'>".$j.".</td><td title='".$keyword."''>";if(strlen($keyword)>28) echo substr_replace($keyword,'...',28); else
				echo $keyword;
			echo "</td></tr>";
			$j++;
		}
		echo "</table></td></tr>";
		echo "</table>";
	}
	//This function displays keywords vistors displayed and percentage viewed compared to others.
	function view_keyword_stats($keyword_interval,$beginDate,$lastDate) {
		global $wpdb;
	
		

		//displaying dates From Date to To Date
		if($keyword_interval != "") {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=keyword&duration=".$keyword_interval;
		}
		elseif($beginDate || $lastDate) {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=keyword&fromDate=".$beginDate."&toDate=".$lastDate;
		}
		else
		{
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=keyword";
		}

		$Results = $this->get_total_visitor_keywords($keyword_interval,$beginDate,$lastDate);
		
		$total_visitors = $Results[0]->total_visitors;

		$keywords_data		=	$this->get_keyword_visitor_records($keyword_interval,$beginDate,$lastDate);

		?>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="16" class="t-top-left">&nbsp;</td>
        <td width="183" class="t-top-left-next align_left">Keyword</td>
        <td width="102" class="t-top-left-next">Views/Visitors</td>
        <td width="91" class="t-top-left-next">Unique Visitors</td>
        <td width="70" class="t-top-left-next">% Viewed</td>
        <td width="18" class="t-top-right">&nbsp;</td>
      </tr>
		<?php
		$css_class="";
		$count=0;

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

		foreach($keywords_data as $keywordData)
		{
			$count++;
			$keyword = $keywordData->keyword;
			
			$percent_viewed = ($keywordData->visitors*100)/$total_visitors;

			$post_id = $keywordData->post_id;

			$post_data = get_post($post_id);
			
			$post_url = get_permalink($post_id);


			//two tokens
			$res = array();
			$unique_visitors = 0;

$fromDate=$beginDate;
$toDate=$lastDate;
if($keyword_interval == "today")
		{
			$query1 = "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$keywordData->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "yesterday")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$keywordData->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "7days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$keywordData->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "14days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where    keyword='$keywordData->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "30days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where  keyword='$keywordData->keyword' AND   DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "60days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$keywordData->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where  keyword='$keywordData->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query1 = "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where keyword='$keywordData->keyword' AND stat_visitor_id not in(".$list_ips.") ";
		}
//echo($query1);
		//	$query1 = "select keyword,stat_visitor_id,visitor_id
		//			 from ".$wpdb->prefix."tts_keyword_stats where keyword='$keywordData->keyword'";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);

			//two tokens end
			if($count!=count($keywords_data)) {
			?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td align="left" valign="middle" class="t-mid-left-next<?php echo($css_class);?>"  title="<?php echo($keyword);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php if(strlen($keyword)>25) echo substr_replace($keyword,'...',25); else
				echo $keyword; ?></span></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($keywordData->visitors);?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>			
			<?php
			}
			else {
			if(count($keywords_data)==6)
				{
				if($css_class=="") {
					$css_class="1";
				}
				else {
					$css_class="";
				}
			?>
			  <tr>
				<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td align="left" valign="middle" class="t-bot-left-next<?php echo($css_class);?>"  title="<?php echo($keyword);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php if(strlen($keyword)>25) echo substr_replace($keyword,'...',25); else
				echo $keyword; ?></span></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($keywordData->visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>			
			<?php
			}
			else {
?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td align="left" valign="middle" class="t-mid-left-next<?php echo($css_class);?>"  title="<?php echo($keyword);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php if(strlen($keyword)>25) echo substr_replace($keyword,'...',25); else
				echo $keyword; ?></span></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($keywordData->visitors);?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
<?php
			}
			}
			?>

			<?php
			if($css_class=="") {
				$css_class="1";
			}
			else {
				$css_class="";
			}
		}

		if(count($keywords_data)<6) {

			if(count($keywords_data)==0) {
				$css_class="";
			}

			for ($i=1; $i <=6-count($keywords_data) ; $i++) {
				if($i==6-count($keywords_data)) {
				$css_class="";
?>
			   <tr>
				<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td align="left" valign="middle" class="t-bot-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-bot-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-bot-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-bot-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
<?php
				}
				else {

?>
			   <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td align="left" valign="middle" class="t-mid-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-mid-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-mid-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-mid-left-next<?php echo($css_class);?>">&nbsp;</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
<?php
				}
		?>
		
		<?php
				if($css_class=="") {
					$css_class="1";
				}
				else {
					$css_class="";
				}
			}	
		}
		
		?>
      <tr>
        <td colspan="3" height="30">&nbsp;</td>
        <td colspan="3" align="right"><a href="<?php echo($detailsPath);?>" class="more-d" title='More Details' target='_blank'>More Details</a></td>
      </tr>
    </table>
		<?php
	}


	//This function is used to get keywords data from the database
	function get_keyword_visitors_data()
	{
		global $wpdb;
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
		$query = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,
					count(stat_visitor_id) as visitors,post_id from ".$wpdb->prefix."tts_keyword_stats where stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";

		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}

	//This function is used to get total keywords visitors for dashboard
	function get_total_keyword_visitors()
	{
		global $wpdb;
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
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where stat_visitor_id not in(".$list_ips.") ";
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}

	//This function is used to get total keyword visitors for main page
	function get_total_visitor_keywords($keyword_interval,$beginDate,$lastDate)
	{
		global $wpdb;
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

		if($keyword_interval == "today") {
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "yesterday") {
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where 	                      DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "7days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where 	                      DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "14days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where 	                      DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "30days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where 	                      DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "60days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where 	                      DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($beginDate)
		{
			$beginDate = date("Y-m-d",strtotime($beginDate));
			$lastDate = date("Y-m-d",strtotime($lastDate));
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between '$beginDate' and '$lastDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_keyword_stats where stat_visitor_id not in(".$list_ips.") ";
		}

//echo($query);
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}
	
	//This function is used to get keywords visitors and their records
	function get_keyword_visitor_records($keyword_interval,$beginDate,$lastDate){
		global $wpdb;

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

		if($keyword_interval == "today") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($keyword_interval == "yesterday") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY)  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($keyword_interval == "7days") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($keyword_interval == "14days") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($keyword_interval == "30days") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($keyword_interval == "60days") {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		elseif($beginDate)
		{
			$beginDate = date("Y-m-d",strtotime($beginDate));
			$lastDate = date("Y-m-d",strtotime($lastDate));

			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$beginDate' and '$lastDate'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
		else {
			$query = "select keyword,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats  where stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc limit 6";
		}
//echo($query);

		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}


	function view_keyword_stat_graph($keyword_interval,$beginDate,$lastDate)
	{
		global $wpdb;
		

		$Results = $this->get_keyword_stat_graph_records($keyword_interval,$beginDate,$lastDate);
		
		$data = "# Graph\n";
		$data .= "Day\tAll Visits\tUnique Visitors\n";
		$i = 1;
		
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
		
		foreach($Results as $result)
		{
			//two tokens
			$res1 = array();
			$unique_visitors = 0;
			$query1 = "select keyword,stat_visitor_id,visitor_id
					 from ".$wpdb->prefix."tts_keyword_stats where  DATE_FORMAT(create_time,'%Y-%m-%d')='".date('Y-m-d',strtotime($result->create_time))."' AND stat_visitor_id not in(".$list_ips.") ";
			$Results1 = $wpdb->get_results($query1, OBJECT);
		//echo($query1.'<br />');
			foreach($Results1 as $result1)
			{
				$res1[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res1 = array_flip($res1);

			$unique_visitors = count($res1);

			//two tokens end
			if($i++ == 1)
			{
				$data .= date("l, F d, Y",strtotime($result->create_time))."\t".$result->visitors."\t".$unique_visitors."\n";
				$data .= date("l, F d, Y",strtotime($result->create_time))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
			else
			{
				$data .= date("l, F d, Y",strtotime($result->create_time))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
		}
		$absPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/";
		$f = fopen($absPath .'analytics_keyword.tsv' , 'wb');
		fwrite($f , $data );
		fclose($f);
		$jsPath = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		?>

			<!-- 1. Add these JavaScript inclusions in the head of your page -->

			<!-- 2. Add the JavaScript to initialize the chart on document ready -->
			<script type="text/javascript">
			
				var chart3;
				jQuery(document).ready(function() {
					
					// define the options
					var options = {
				
						chart: {
							renderTo: 'container3'
						},
						
						title: {
							text: '   '
						},
						
						subtitle: {
							text: ''
						},
						
						xAxis: {
							type: 'datetime',
							tickInterval: 7 * 24 * 3600 * 1000, // one week
							tickWidth: 0,
							gridLineWidth: 1,
							labels: {
								align: 'left',
								x: 3,
								y: -3 
							}
						},
						
						yAxis: [{ // left y axis
							title: {
								text: null
							},
							labels: {
								align: 'left',
								x: 3,
								y: 16,
								formatter: function() {
									return Highcharts.numberFormat(this.value, 0);
								}
							},
							showFirstLabel : false
						}, { // right y axis
							linkedTo: 0,
							gridLineWidth: 0,
							opposite: true,
							title: {
								text: null
							},
							
							showFirstLabel: false
						}],
						
						legend: {
							align: 'left',
							verticalAlign: 'top',
							y: 20,
							floating: true,
							borderWidth: 0
						},
						
						tooltip: {
							shared: true,
							crosshairs: true
						},
						
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function() {
											hs.htmlExpand(null, {
												pageOrigin: {
													x: this.pageX, 
													y: this.pageY
												},
												headingText: this.series.name,
												maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+ 
													this.y +' visits',
												width: 200
											});
										}
									}
								},
								marker: {
									lineWidth: 1
								}
							}
						},
						
						series: [{
							name: 'All visitors',
							lineWidth: 4,
							marker: {
								radius: 4
							}
						}, {
							name: 'Unique visitors'
						}]
					};
					
					
					// Load data asynchronously using jQuery. On success, add the data
					// to the options and initiate the chart.
					// This data is obtained by exporting a GA custom report to TSV.
					// http://api.jquery.com/jQuery.get/
					jQuery.get('<?php echo $jsPath; ?>analytics_keyword.tsv', null, function(tsv, state, xhr) {
						var lines = [],
							listen = false,
							date,
							
							// set up the two data series
							allVisits = [],
							newVisitors = [];
							
						// inconsistency
						if (typeof tsv !== 'string') {
							tsv = xhr.responseText;
						}
						
						// split the data return into lines and parse them
						tsv = tsv.split(/\n/g);
						jQuery.each(tsv, function(i, line) {
				
							// listen for data lines between the Graph and Table headers
							if (tsv[i - 3] == '# Graph') {
								listen = true;
							} else if (line == '' || line.charAt(0) == '#') {
								listen = false;
							}
							
							// all data lines start with a double quote
							if (listen) {
								line = line.split(/\t/);
								date = Date.parse(line[0] +' UTC');
								
								allVisits.push([
									date, 
									parseInt(line[1].replace(',', ''), 10)
								]);
								newVisitors.push([
									date, 
									parseInt(line[2].replace(',', ''), 10)
								]);
							}
						});
						
						options.series[0].data = allVisits;
						options.series[1].data = newVisitors;
						
						chart3 = new Highcharts.Chart(options);
					});
					
				});
					
			</script>
			
			<!-- Additional files for the Highslide popup effect -->

			
			
	
			
			<!-- 3. Add the container -->
			
			<?php
	}

	function get_keyword_stat_graph_records($duration,$startDate,$endDate) {
		//This function returns records coressponding to page views/visitors
		global $wpdb;

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

		if($duration == "today") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.")  group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($duration == "yesterday") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)   AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($duration == "7days") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'   AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($duration == "14days") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.")  group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($duration == "30days") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'   AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($duration == "60days") {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'   AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		elseif($startDate)
		{
			$startDate = date("Y-m-d",strtotime($startDate));
			$endDate = date("Y-m-d",strtotime($endDate));

			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$startDate' and '$endDate'   AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		else {
			$query = "select keyword,create_time,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats   where stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}
	
	//This function displays the keyword views/visitors details with paging 
	function view_keyword_stats_details($duration,$fromDate,$toDate){
		global $wpdb;
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<SCRIPT type='text/javascript' src='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118'></script>";
		echo "<link type='text/css' rel='stylesheet' href='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112' media='screen'></LINK>";

		$pagingPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/pager.php";
		include_once($pagingPath);


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
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=keyword&fromDate=".$fromDate."&toDate=".$toDate;
		}

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
		
		$to_date = date("Y M d",current_time( 'timestamp'));
		if($fromDate)
		{
			$frDate = date("Y-m-d",strtotime($fromDate));
			$tDate = date("Y-m-d",strtotime($toDate));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$frDate' and '$tDate'   AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$frDate' and '$tDate'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		else
		{
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where stat_visitor_id not in(".$list_ips.")  group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats   where stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}

		if($duration == "today")
		{
			$from_date = date("Y M d",current_time( 'timestamp'));
			
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		

		if($duration == "yesterday"){
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats  where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		

		if($duration == "7days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp'))));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		
		
		if($duration == "14days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp'))));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		

		if($duration == "30days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp'))));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats  where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}
		
		if($duration == "60days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp'))));
			$query1 = "select keyword from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword";
			$query2 = "select keyword,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_keyword_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by keyword order by visitors desc";
		}

		//echo($query1.'<br />');
		//echo($query2.'<br />');

?>
    <div class="graph-con">
    	<div class="graph-left">
        	<div class="graph-left1"></div>
            <div class="graph-left2" align="center">
            <div class="left-title">View Graph</div>
            	<ul class="memu-ul">
                	<li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=today">Today</a></li>
                    <li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=yesterday">Yesterday</a></li>
                    <li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=7days">Last 7 Days</a></li>
                    <li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=14days">Last 14 Days</a></li>
                    <li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=30days">Last 30 Days</a></li>
                    <li><a href="admin.php?page=<?php echo TRACK_PLUGIN_NAME; ?>&details=keyword&duration=60days">Last 60 Days</a></li>
                </ul>
            </div>
            <div class="graph-left3"></div>
        </div>
        <div class="graph-right">
        	<div class="graph-right1"></div>
            <div class="graph-right2" align="center" id="container3"><?php 		$this->view_keyword_stat_graph($duration,$fromDate,$toDate); ?></div>
            <div class="graph-right3"></div>
        </div>
    </div>
    <div class="cont" align="center">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td colspan="6" height="50" valign="top">
    <div class="select"><form name='pageForm' method='get' action='admin.php'>Show <select name='paging_records'  class="Select_bg" onchange='document.pageForm.submit()'>
	<?php

		$position = strpos($_SERVER['REQUEST_URI'],"?");
		$str = substr($_SERVER['REQUEST_URI'],$position+1);
		$str_a = explode("&",$str);
		
		/* Show many results per page? */
		if($_REQUEST['paging_records'])
		{
			$limit = $_REQUEST['paging_records'];
		}
		else
		{
			
			if(isset($_COOKIE ['paging_records_keywords'])) {
				
				$limit = $_COOKIE['paging_records_keywords'];
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

		//get total visitors
		$Results = $this->get_total_visitor_keywords($duration,$fromDate,$toDate);
		$total_visitors = $Results[0]->total_visitors;
		
		//get referrer visitor records
		//$Results = get_referrer_visitors_records($duration);

		

		$p = new Pager;
 

		 
		/* Find the start depending on $_GET['page'] (declared if it's null) */
		$start = $p->findStart($limit);
		 
		/* Find the number of rows returned from a query; Note: Do NOT use a LIMIT clause in this query */
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
    <div class="select-r"> <img src="<?php echo($path); ?>images/key.png" alt="" /></div>
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
    <td width="442" align="left" class="t-top-left-next"><span class="align"> Keyword </span></td>
    <td width="150" align="center" class="t-top-left-next">Views/Visitors</td>
    <td width="186" align="center" class="t-top-left-next">Unique Visitors</td>
    <td width="168" class="t-top-left-next">% Viewed</td>
    <td width="18" class="t-top-right">&nbsp;</td>
  </tr>
<?php
		//$this->view_keyword_stat_graph($duration,$fromDate,$toDate);
		$count=0;
		$css_class="";
		foreach($result as $res)
		{
			$count++;
			$keyword = $res->keyword;

			$percent_viewed = ($res->visitors)*100/$total_visitors;



			//two tokens
			$res1 = array();
			$unique_visitors = 0;
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

$keyword_interval = $duration;
if($keyword_interval == "today")
		{
			$query1 = "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$res->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "yesterday")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$res->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "7days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$res->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "14days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where    keyword='$res->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "30days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where  keyword='$res->keyword' AND   DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "60days")
		{
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where   keyword='$res->keyword' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where  keyword='$res->keyword' AND DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query1 = "select keyword,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_keyword_stats where keyword='$res->keyword' AND stat_visitor_id not in(".$list_ips.") ";
		}

		//echo($query1.'<br />');
			//$query1 = "select keyword,stat_visitor_id,visitor_id
			//		 from ".$wpdb->prefix."tts_keyword_stats where keyword='$res->keyword'";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res1[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res1 = array_flip($res1);

			$unique_visitors = count($res1);

			//two tokens end
			if($count!=count($result)) {
		?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>" title="<?php echo($keyword);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php if(strlen($keyword)>50) echo substr_replace($keyword,'...',50); else
					echo $keyword; ?> </span>
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($res->visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
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
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>"  title="<?php echo($keyword);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><span class="link"><?php if(strlen($keyword)>50) echo substr_replace($keyword,'...',50); else
					echo $keyword; ?> </span>
				</td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($res->visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>			
		<?php
			}
?>

<?php
			if($css_class=="") {
				$css_class="1";
			}
			else {
				$css_class="";
			}	
		
		}
?><tr><td colspan="6">
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


	

} //end of class
?>