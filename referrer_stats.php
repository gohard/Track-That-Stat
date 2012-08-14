<?php
class RefferStats{
	
	//This function is used to display refrral data on dashboard
	function display_refrral_data($duration) {
		

		echo "<table width='100%'><tr>
		<td width='50%' valign='top'>
			<table  width='100%'>
			<tr>
			<td colspan='2' width='100%' style='line-height:30px;color:#333333'><strong>Top Referrals</strong></td>
			</tr>";
		
		
		
		//get total visitors
		$Results = $this->get_total_referrer_visitors($duration,$fromDate="",$toDate="");
		$total_visitors = $Results[0]->total_visitors;

		$Results = $this->get_referrer_visitors_data();
		$i = 1;
		foreach($Results as $result)
		{
			$referrer = $result->referrer;

			$percent_viewed = ($result->visitors)*100/$total_visitors;

			echo "<tr><td width='7%' style='line-height:15px'>".$i.".</td><td title='".$referrer."'><a href='http://".$referrer."' target='_blank'>";if(strlen($referrer)>25) echo substr_replace($referrer,'...',25); else
				echo $referrer;
			echo "</a></td></tr>";
			$i++;

		}

		echo "</table></td></tr></table>";

	}

	function view_traffic_referral_stats($duration,$fromDate,$toDate)
	{
		global $wpdb;
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		
		echo "<SCRIPT type='text/javascript' src='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118'></script>";
		echo "<link type='text/css' rel='stylesheet' href='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112' media='screen'></LINK>";


		//setting path of more details
		if($duration != "") {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&duration=".$duration;
		}
		elseif($fromDate || $toDate) {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&fromDate=".$fromDate."&toDate=".$toDate;
		}
		else
		{
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=referral";
		}
	?>
  <table width="98%" border="0" cellspacing="0" cellpadding="0">

  <tr>
    <td width="16" class="t-top-left">&nbsp;</td>
    <td width="174" class="t-top-left-next align_left">Referrer</td>
    <td width="104" class="t-top-left-next">Views/Visitors</td>
    <td width="92" class="t-top-left-next">Unique Visitors</td>
    <td width="76" class="t-top-left-next">% Viewed</td>
    <td width="18" class="t-top-right">&nbsp;</td>
  </tr>
	<?php



		//get total visitors
		//echo "sandeep".$fromDate;
		$Results = $this->get_total_referrer_visitors($duration,$fromDate,$toDate);
		$total_visitors = $Results[0]->total_visitors;
		
		//get referrer visitor records
		$Results = $this->get_referrer_visitors_records($duration,$fromDate,$toDate);
		$css_class="";
		$count=0;
		//$Results=array();

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
			$count++;
			$referrer = $result->referrer;
			$percent_viewed = ($result->visitors)*100/$total_visitors;

			//two tokens
			$res = array();
			$unique_visitors = 0;



if($duration == "today")
		{
			$query1 = "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$result->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "yesterday")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$result->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "7days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$result->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "14days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where    referrer='$result->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "30days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where  referrer='$result->referrer' AND   DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "60days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$result->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where  referrer='$result->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query1 = "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where referrer='$result->referrer' AND stat_visitor_id not in(".$list_ips.") ";
		}
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);

			//two tokens end
			if($count!=count($Results)) {
		
			?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>" title="<?php echo($referrer);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span>
					<a class="link" href='http://<?php echo($referrer);?>' target='_blank'><?php if(strlen($referrer)>20) echo substr_replace($referrer,'...',20); else
					echo $referrer; ?> </a>
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
			<?php
			}
			else {
			if(count($Results)==6){

				if($css_class=="") {
					$css_class="1";
				}
				else {
					$css_class="";
				}
			?>
			  <tr>
				<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>" title="<?php echo($referrer);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span>
					<a class="link" href='http://<?php echo($referrer);?>' target='_blank'><?php if(strlen($referrer)>20) echo substr_replace($referrer,'...',20); else
					echo $referrer; ?> </a>
				</td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
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
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>" title="<?php echo($referrer);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span>
					<a class="link" href='http://<?php echo($referrer);?>' target='_blank'><?php if(strlen($referrer)>20) echo substr_replace($referrer,'...',20); else
					echo $referrer; ?> </a>
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors); ?></td>
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
		if(count($Results)<6) {
			
			if(count($Results)==0) {
				$css_class="";
			}
			for ($i=1; $i <=6-count($Results) ; $i++) {
				if($i==6-count($Results)) {
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


	function display_referral_graph($duration,$fromDate,$toDate)
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

		if($duration == "today") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($duration == "yesterday") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($duration == "7days") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 7 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($duration == "14days") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 14 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($duration == "30days") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 30 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($duration == "60days") {
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 60 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		elseif($fromDate) {
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));

			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";			
		}
		else
		{
			$query = "select referrer,create_time,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats  where stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(create_time,'%Y-%m-%d')";
		}
		//echo($query.'<br />');

		$Results = $wpdb->get_results($query, OBJECT);
		$data = "# Graph\n";
		$data .= "Day\tAll Visits\tUnique Visitors\n";
		$i = 1;
		foreach($Results as $result)
		{
			//two tokens
			$res = array();
			$unique_visitors = 0;
			$query1 = "select referrer,stat_visitor_id,visitor_id
					 from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".date('Y-m-d',strtotime($result->create_time))."' and stat_visitor_id not in(".$list_ips.")";

	//	echo($query1.'<br />');

			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);

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
		$f = fopen($absPath .'analytics_referrer.tsv' , 'wb');
		fwrite($f , $data );
		fclose($f);
		$jsPath = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			?>
			
			
			
			<!-- 1. Add these JavaScript inclusions in the head of your page -->
			
			
		
			
			
			
			
			<!-- 2. Add the JavaScript to initialize the chart on document ready -->
			<script type="text/javascript">
			
				var chart1;
				jQuery(document).ready(function() {
					
					// define the options
					var options = {
				
						chart: {
							renderTo: 'container1'
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
							showFirstLabel: false
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
					jQuery.get('<?php echo $jsPath; ?>analytics_referrer.tsv', null, function(tsv, state, xhr) {
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
						
						chart1 = new Highcharts.Chart(options);
					});
					
				});
					
			</script>
			
			<!-- Additional files for the Highslide popup effect -->

		
			
			<!-- 3. Add the container -->
			
					
		
			<?php
		
	}//end of graph function

	//This function is used to display traffic increase/decrease % displayed in red/green for all the referral data collected
	function view_traffic_referral_data($data_interval = "week")
	{
		global $wpdb;
		$flag = 0;  //flag = 0 for decrease
		
		echo "<table  width='99%' cellspacing='0' cellpadding='0' border='1' align='left'><tr><td style='font-size:16px;padding-top:30px;'><strong>Traffic Data</strong></td></tr>";

		echo "<tr><td style='height:15px'></td></tr><tr><td align='right'><table cellpsacing='0' cellpadding='0'><tr>";
		
		if($data_interval == "day")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=day' class='button-secondary' title='day' style='color:#E66F21'>Day</a></td>";

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') = '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' group by referrer";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) group by referrer";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d")." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-1, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m"), date("d")-1, date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=day' class='button-secondary' title='day'>Day</a></td>";
		}

		if($data_interval == "week")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=week' class='button-secondary' title='week' style='color:#E66F21'>Week</a></td>";

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' group by referrer";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 7 Day) group by referrer";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-6, date("y")))." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-13, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m"), date("d")-7, date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=week' class='button-secondary' title='week'>Week</a></td>";
		}

		if($data_interval == "month")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=month' class='button-secondary' title='month' style='color:#E66F21'>Month</a></td>";

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and DATE_FORMAT(create_time,'%Y-%m-%d') != DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) group by referrer";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 2 Month) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) and DATE_FORMAT(create_time,'%Y-%m-%d') != DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 2 Month) group by referrer";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d",mktime(0, 0, 0, date("m")-1, date("d")+1, date("y")))." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m")-2, date("d")+1, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m")-1, date("d"), date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&data_interval=month' class='button-secondary' title='month'>Month</a></td>";
		}

		echo "</tr></table></td></tr>";

		//$current_data = mktime(0, 0, 0, date("m")-$month, date("d")-$interval, date("y"));

		echo "<tr><td style='padding-right:5px;padding-top:10px;line-height:25px;color:#555555;font-size:12px;text-align:right'>".$current_time."</td></tr>";
		echo "<tr><td style='padding-right:5px;padding-bottom:10px;line-height:25px;color:#555555;font-size:12px;text-align:right'>Comparing to ".$previous_time."</td></tr>";


		foreach($previous_results as $result)
		{
			$prev_results[$result->referrer] = $result->visitors;
		}
		


		echo "<tr><td style='padding-right:5px;'><table class='widefat'><thead><tr><th>Referrer</th><th>Visits</th><th style='width:100px;margin-right:10px'>Percent Change</th></tr></thead>";

		
		if($current_results)
		{
			foreach($current_results as $result)
			{
				$referrer = $result->referrer;
				echo "<tr><td title='".$referrer."' style='width:500px'><a href='http://".$referrer."' target='_blank'>";if(strlen($referrer)>60) echo substr_replace($referrer,'...',60); else
				echo $referrer;
				echo "</a></td><td>".$result->visitors."</td>";
				if(!$result->visitors)
				{
					$result->visitors = "0";
				}
				//calculate percent change
				if($result->visitors > $prev_results[$result->referrer])
				{
					if($prev_results[$result->referrer])
					{
						$percent_change = (($result->visitors-$prev_results[$result->referrer])*100)/$prev_results[$result->referrer];
					}
					else
					{
						$percent_change = (($result->visitors-$prev_results[$result->referrer])*100);
					}
					
					echo "<td style='color:#62B073;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/greenuparrow.png' border='0'> ".round($percent_change,2)."%"."</td>";
				}
				else
				{
					if($prev_results[$result->referrer])
					{
						$percent_change = (($result->visitors-$prev_results[$result->referrer])*100)/$prev_results[$result->referrer];
						echo "<td style='color:#FF0000;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/reddownarrow.png' border='0'>".round($percent_change,2)."%"."</td>";
					}
					
				}
				echo "</tr>";
			}
		}
		else {
					$result->visitors = 0;
					if(count($prev_results)>0){
						foreach($prev_results as $referrer=>$visitors){
							
							echo "<tr><td title='".$referrer."' style='width:500px;'><a href='http://".$referrer."' target='_blank'>";if(strlen($referrer)>60) echo substr_replace($referrer,'...',60); else
							echo $referrer;
							echo "</a></td><td>".$result->visitors."</td>";
							
							$percent_change = (($result->visitors-$visitors)*100)/$visitors;
							echo "<td style='color:#FF0000;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/reddownarrow.png' border='0'>".round($percent_change,2)."%"."</td>";
							echo "</tr>";
						}
					}

		}

		echo "</table></td></tr>";

		echo "</table>";
	}

	function view_referral_stats_details($duration,$fromDate,$toDate)
	{
		global $wpdb;
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<SCRIPT type='text/javascript' src='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118'></script>";
		echo "<link type='text/css' rel='stylesheet' href='".$path."calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112' media='screen'></LINK>";
		$pagingPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/pager.php";
		include($pagingPath);

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
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=referral&fromDate=".$fromDate."&toDate=".$toDate;
		}

?>

<?php
		$to_date = date("Y M d",current_time( 'timestamp'));

		if($fromDate)
		{
			$frDate = date("Y-m-d",strtotime($fromDate));
			$tDate = date("Y-m-d",strtotime($toDate));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$frDate' and '$tDate'  AND stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$frDate' and '$tDate'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}
		else
		{
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats  where stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats  where stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}

		if($duration == "today")
		{
			$from_date = date("Y M d",current_time( 'timestamp'));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer ";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}
		
		if($duration == "yesterday")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats  where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";


		}
		
		if($duration == "7days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp'))));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}
		
		if($duration == "14days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp'))));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer ";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}
		
		if($duration == "30days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp'))));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats  where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
		}
		
		if($duration == "60days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp'))));
			$query1 = "select referrer from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer";
			$query2 = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc";
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
                	<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=today">Today</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=yesterday">Yesterday</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=7days">Last 7 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=14days">Last 14 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=30days">Last 30 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=referral&duration=60days">Last 60 Days</a></li>
                </ul>
            </div>
            <div class="graph-left3"></div>
        </div>
        <div class="graph-right">
        	<div class="graph-right1"></div>
            <div class="graph-right2" align="center" id="container1"><?php $this->display_referral_graph($duration,$fromDate,$toDate); ?></div>
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
			
			if(isset($_COOKIE ['paging_records_referrer'])) {
				
				$limit = $_COOKIE['paging_records_referrer'];
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
		$Results = $this->get_total_referrer_visitors($duration,$fromDate,$toDate);
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
    <div class="select-r"> <img src="<?php echo($path); ?>images/ref.png" alt="" /></div>
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
    <td width="342" align="left" class="t-top-left-next"><span class="align"> Referrer </span></td>
    <td width="250" align="center" class="t-top-left-next">Views/Visitors</td>
    <td width="186" align="center" class="t-top-left-next">Unique Visitors</td>
    <td width="168" class="t-top-left-next">% Viewed</td>
    <td width="18" class="t-top-right">&nbsp;</td>
  </tr>
  
  <?php


		$count=0;
		foreach($result as $res)
		{

			$count++;
			$referrer = $res->referrer;

			$percent_viewed = ($res->visitors)*100/$total_visitors;


			//two tokens
			$res1 = array();
			$unique_visitors = 0;

if($duration == "today")
		{
			$query1 = "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$res->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($duration == "yesterday")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$res->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($duration == "7days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$res->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($duration == "14days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where    referrer='$res->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($duration == "30days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where  referrer='$res->referrer' AND   DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($duration == "60days")
		{
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where   referrer='$res->referrer' AND  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.")";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where  referrer='$res->referrer' AND DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate' and stat_visitor_id not in(".$list_ips.")";
		}
		else
		{
			$query1 = "select referrer,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_referrer_stats where referrer='$res->referrer' and stat_visitor_id not in(".$list_ips.")";
		}

		//echo($query1.'<br />');

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
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span>
					<a class="link" href='http://<?php echo($referrer);?>' target='_blank'><?php if(strlen($referrer)>25) echo substr_replace($referrer,'...',25); else
					echo $referrer; ?> </a>
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
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span>
					<a class="link" href='http://<?php echo($referrer);?>' target='_blank'><?php if(strlen($referrer)>25) echo substr_replace($referrer,'...',25); else
					echo $referrer; ?> </a>
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


	//This function is used to get total referrer visitors
	function get_total_referrer_visitors($duration,$fromDate,$toDate)
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

		if($duration == "today")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "yesterday")
		{
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "7days")
		{
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "14days")
		{
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "30days")
		{
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "60days")
		{
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query= "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where  DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_referrer_stats where stat_visitor_id not in(".$list_ips.") ";
		}
		
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}

	//This function is used to get referrer data from database
	function get_referrer_visitors_data()
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

		$query = "select referrer,count(distinct(stat_visitor_id)) as unique_visitors,
					count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats  where stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";

		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}

	//This function is used to get referrer data from database
function get_referrer_visitors_records($duration,$fromDate,$toDate)
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

	if($duration == "today")	{
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc  limit 6";
	}
	elseif($duration == "yesterday") {
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}
	elseif($duration == "7days")
	{
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}
	elseif($duration == "14days")
	{
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}
	elseif($duration == "30days")
	{
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}
	elseif($duration == "60days")
	{
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	} 
	elseif($fromDate)
	{
		$fromDate = date("Y-m-d",strtotime($fromDate));
		$toDate = date("Y-m-d",strtotime($toDate));
	
		$query = "select create_time,referrer, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats where DATE_FORMAT(create_time,'%Y-%m-%d') between '$fromDate' and '$toDate'  and stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}
	else {
		$query = "select referrer,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_referrer_stats  where stat_visitor_id not in(".$list_ips.") group by referrer order by visitors desc limit 6";
	}

	$Results = $wpdb->get_results($query, OBJECT);
	
	return $Results;
}

} //end of class
?>