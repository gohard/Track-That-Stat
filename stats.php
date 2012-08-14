<?php
class Stats{

	//This function is used to display traffic increase decrease percent with respect to day, week and month
	function traffic_change_data1($days){
		global $wpdb;
		$flag = 0;  //flag = 0 for decrease
		switch($days)
		{
			case "day":
				$month = 0;
				$day = "1";
				$unit = "day";
				$duration = "1";
				break;
			case "week":
				$month = 0;
				$day = "7";
				$unit = "day";
				$duration = "7";
				break;
			case "month":
				$month = 1;
			    $day = "0";
				$unit = "Month";
				$duration = 1;
				break;
			default:
				$month = 0;
				$day = "7";
				$unit = "day";
				$duration = "7";
		}
		
		//get current week data
		echo $query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL $duration $unit) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' group by stat_post_id";
		$current_results = $wpdb->get_results($query, OBJECT);
		
		$previous_interval = $duration*2;
		//get previous week data
		echo "<br>".$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL $previous_interval $unit) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL $duration $unit) group by stat_post_id";
		$previous_results = $wpdb->get_results($query, OBJECT);
			 
		
		foreach($previous_results as $result)
		{
			$prev_results[$result->stat_post_id] = $result->visitors;
		}
		
		
		echo "<table class='widefat fixed' width='100%'><tr><td colspan='3'><h3>Traffc data</h3></td></tr><tr><td colspan='3' align='right'>";
		$current_data = mktime(0, 0, 0, date("m")-$month, date("d")-$day+1, date("y"));
		echo date("Y M d")." - ".date("Y M d",$current_data);
		echo "<br><br>compared to ". date("Y M d",mktime(0, 0, 0, date("m")-(2*$month), date("d")-(2*$day)+1, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m")-$month, date("d")-$duration, date("y")));
		echo "</td></tr>
		<tr><td colspan='3' align='right'><a href='index.php?day=day'><input type='button' name='day' value='Day'></a><a href='index.php?day=week'><input type='button' name='week' value='Week'></a><a href='index.php?day=month'><input type='button' name='month' name='month' value='Month'></a></td></tr>
			<tr><th><h4>Post/Page</h4></th><th><h4>Visits</h4></th><th><h4>% Change</h4></th></tr>";
		foreach($current_results as $result)
		{
			$post_data = get_post($result->stat_post_id);
		
			$post_url = get_permalink($result->stat_post_id);

			echo "<tr><td><a href='$post_url' target='_blank'>".$post_data->post_title."</a></td><td>".$result->visitors."</td>";
			if(!$result->visitors)
			{
				$result->visitors = "0";
			}
			//calculate percent change
			if($result->visitors > $prev_results[$result->stat_post_id])
			{
				echo "ddd".$result->stat_post_id;
				echo "<pre>";
				print_r($prev_results);
				if($prev_results[$result->stat_post_id])
				{
					$percent_change = (($result->visitors-$prev_results[$result->stat_post_id])*100)/$prev_results[$result->stat_post_id];
				}
				elseif($prev_results[$result->visitors])
				{
					$percent_change = "100";
				}

				echo "<td style='color:#62B073;width:10px;margin-right:0px'><img src='images/uparrow.png' border='0'>&nbsp;<span style='vertical-align:middle'>".round($percent_change,2)."%"."</span></td>";
			}
			else
			{
				if($prev_results[$result->stat_post_id])
				{
					$percent_change = (($result->visitors-$prev_results[$result->stat_post_id])*100)/$prev_results[$result->stat_post_id];
				}
				
				echo "<td style='color:#FF0000'>".round($percent_change,2)."%"."</td>";
			}
		
			echo "</tr>";
		}

		echo "</table>";
		
	}

	function traffic_change_data_dashboard(){
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

		//get current week data
		$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		$current_results = $wpdb->get_results($query, OBJECT);

		$current_visitors = $current_results[0]->visitors;
		
		//get previous week data
		$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 30 Day) AND stat_visitor_id not in(".$list_ips.") ";
		$previous_results = $wpdb->get_results($query, OBJECT);

		$previous_visitors = $previous_results[0]->visitors;
		echo "<table width='82%'  cellpadding='0' border='0' align='center'><tr><td style='text-align:center;padding-left:95px;'><table width='100%'><!--tr><td colspan='3'><h3>Traffc data</h3></td></tr-->";
		
		
		if($previous_visitors>0)
		{
			if($current_visitors > $previous_visitors)
			{
				$percent_change = (($current_visitors-$previous_visitors)*100)/$previous_visitors;
				echo "<tr><td style='width:20px;text-align:right;' valign='top'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/greenuparrow.png'></td><td style='color:#62B073;text-align:left'>".round($percent_change,2)."%</td><td>This data is based on the past 30 days</td></tr>";

			}
			else
			{
				$percent_change = (($current_visitors-$previous_visitors)*100)/$previous_visitors;
				echo "<tr><td style='width:10px;text-align:right;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/reddownarrow.png'></td><td  style='color:#ff0000;'>".round($percent_change,2)."%</td><td>This data is based on the past 30 days</td></tr>";
				
			}
		}
		else
		{
			if($current_visitors > 0)
			{
				echo "<tr><td style='width:20px;text-align:right;' valign='top'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/greenuparrow.png'></td><td style='color:#62B073;text-align:left'>100%</td><td>This data is based on the past 30 days</td></tr>";
			}
		}
		
		echo "<tr><td style='height:5px;'></td></tr></table></td></tr></table>";
	}



	function display_page_views_graph_dashboard()
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

		$query = "select stat_date,
					count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		$Results = $wpdb->get_results($query, OBJECT);
		$data = "# Graph\n";
		$data .= "Day\tAll Visits\tUnique Visitors\n";
		$i = 1;
		foreach($Results as $result)
		{
			$res = array();
			$unique_visitors = 0;
			$query1 = "select stat_date,stat_visitor_id,visitor_id
					 from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')='".date('Y-m-d',strtotime($result->stat_date))."' AND stat_visitor_id not in(".$list_ips.") ";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);

			if($i++ == 1)
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
			else
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
		}
		$absPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/";
		$f = fopen($absPath .'analytics.tsv' , 'wb');
		fwrite($f , $data );
		fclose($f);
		$jsPath = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			?>
			<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
			
			
			<!-- 1. Add these JavaScript inclusions in the head of your page -->

			
			
			
			<!-- 2. Add the JavaScript to initialize the chart on document ready -->
			<script type="text/javascript">
			
				var chart;
				jQuery(document).ready(function() {
					
					// define the options
					var options = {
				
						chart: {
							renderTo: 'container'
						},
						
						title: {
							text: '  '
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
					jQuery.get('<?php echo $jsPath; ?>analytics.tsv', null, function(tsv, state, xhr) {
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
						
						chart = new Highcharts.Chart(options);
					});
					
				});
					
			</script>
			
			<!-- Additional files for the Highslide popup effect -->
	
		</head>
		<body>
			
			<!-- 3. Add the container -->
			<div id="container" style="width: 500px; height: 280px; margin: 0 auto"></div>
			
					
		</body>
	</html>
			<?php
	}//end of graph function


	function display_page_views_graph_main($duration,$fromDate,$toDate)
	{
		global $wpdb;


		
		$to_date = date("Y M d");
		if($duration == "today")
		{
			$from_date = date("Y M d");
		}
		else
		{
		}
		if($duration == "yesterday")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
			$to_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));;
		}
		else
		{
		}
		if($duration == "7days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp'))));
		}
		else
		{
		}

		if($duration == "14days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp'))));
		}
		else
		{
		}

		if($duration == "30days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp'))));
		}
		else
		{
		}
		if($duration == "60days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp'))));
		}
		else
		{
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

		if($duration == "today") {
			$query = "select stat_date,
					count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "yesterday") {
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "7days") {
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 7 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "14days") {
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 14 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "30days") {
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 30 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")  group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "60days") {
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 60 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")  group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query = "select stat_date,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between '$fromDate' and '$toDate'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		else {
			$query = "select stat_date,
					count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats  where stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}


		$Results = $wpdb->get_results($query, OBJECT);
		$data = "# Graph\n";
		$data .= "Day\tAll Visits\tUnique Visitors\n";
		$i = 1;
		
		foreach($Results as $result)
		{
			$res = array();
			$unique_visitors = 0;
			$query1 = "select stat_date,stat_visitor_id,visitor_id
					 from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')='".date('Y-m-d',strtotime($result->stat_date))."' AND stat_visitor_id not in(".$list_ips.") ";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);
			
			if($i++ == 1)
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
			else
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
		}
		$absPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/";

		$f = fopen($absPath.'analytics_views.tsv' , 'wb');
		fwrite($f , $data );
		fclose($f);
		$jsPath = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			?>
			
			
			
			
			<!-- 1. Add these JavaScript inclusions in the head of your page -->

			
			
			<!-- 2. Add the JavaScript to initialize the chart on document ready -->
			<script type="text/javascript">
			
				var chart;
				jQuery(document).ready(function() {
					
					// define the options
					var options = {
				
						chart: {
							renderTo: 'container'
						},
						
						title: {
							text: ' '
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
					jQuery.get('<?php echo $jsPath; ?>analytics_views.tsv', null, function(tsv, state, xhr) {
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
						
						chart = new Highcharts.Chart(options);
					});
					
				});
					
			</script>
			
			<!-- Additional files for the Highslide popup effect -->
			
		<!----------------------------------------------- graph ------------------------------------------------------>
    <div class="graph-con">
    	<div class="graph-left">
        	<div class="graph-left1"></div>
            <div class="graph-left2" align="center">
            <div class="left-title">View Graph</div>
            	<ul class="memu-ul">
                	<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=today">Today</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=yesterday">Yesterday</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=7days">Last 7 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=14days">Last 14 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=30days">Last 30 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&interval=60days">Last 60 Days</a></li>
                </ul>
            </div>
            <div class="graph-left3"></div>
        </div>
        <div class="graph-right">
        	<div class="graph-right1"></div>
            <div class="graph-right2" align="center" id="container"></div>
            <div class="graph-right3"></div>
        </div>
    </div>
<!----------------------------------------------- end graph ------------------------------------------------------>
			
           

			<?php
	}//end of graph function
	
	//This function displays page views/unique vistors displayed and percentage viewed compared to others.

	
	function view_page_stats($intvl,$startDate,$endDate){
		global $wpdb;
		
		
		//displaying dates From Date to To Date
		if($intvl != "") {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=page&duration=".$intvl;
		}
		elseif($startDate || $endDate) {
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=page&fromDate=".$startDate."&toDate=".$endDate;
		}
		else
		{
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=page";
		}

		$Results = $this->get_total_page_visitors($intvl,$startDate,$endDate);
		$total_visitors = $Results[0]->total_visitors;

		$Results		=	$this->get_page_visitor_records($intvl,$startDate,$endDate);
		

?>
	<table width="98%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="16" class="t-top-left">&nbsp;</td>
        <td width="193" class="t-top-left-next align_left">Post/Page</td>
        <td width="85" class="t-top-left-next">Views/Visitors</td>
        <td width="92" class="t-top-left-next">Unique Visitors</td>
        <td width="76" class="t-top-left-next">% Viewed</td>
        <td width="18" class="t-top-right">&nbsp;</td>
      </tr>
<?php
		$css_class="";
		$count=0;
		foreach($Results as $result)
		{
			$count++;
			$post_data = get_post($result->stat_post_id);
			$post_url = get_permalink($result->stat_post_id);

			$percent_viewed = ($result->visitors * 100)/$total_visitors;

			//two tokens
			$res = array();
			$unique_visitors = 0;

		$fromDate=$startDate;
		$toDate=$endDate;
		$keyword_interval = $intvl;

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

if($keyword_interval == "today")
		{
			$query1 = "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$result->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "yesterday")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$result->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "7days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$result->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "14days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where    stat_post_id='$result->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "30days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where  stat_post_id='$result->stat_post_id' AND   DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($keyword_interval == "60days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$result->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";


		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where  stat_post_id='$result->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query1 = "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id='$result->stat_post_id' AND stat_visitor_id not in(".$list_ips.") ";
		}
//echo($query1);
			//$query1 = "select stat_date,stat_visitor_id,visitor_id
			//		 from ".$wpdb->prefix."tts_trafficstats where stat_post_id='$result->stat_post_id'";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);

			//two tokens end

			if(strlen($post_data->post_title)>20) $post_title = substr_replace($post_data->post_title,'...',20); else $post_title = $post_data->post_title;
			
			if($count!=count($Results)) {
				if($result->stat_post_id != "-1")
				{
				?>
				  <tr>
					<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
					<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo($post_url);?>" class="link" title="<?php echo($post_data->post_title);?>"><?php echo($post_title);?></a></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
					<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
				  </tr>
				<?php

				}
				else {
				?>
				  <tr>
					<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
					<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo(site_url());?>" class="link" title="/">/</a></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
					<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
				  </tr>		
				<?php
				}				
			}
			else {
				if(count($Results)==6)
				{
				if($css_class=="") {
					$css_class="1";
				}
				else {
					$css_class="";
				}
					if($result->stat_post_id != "-1")
					{
					?>
					  <tr>
						<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
						<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo($post_url);?>" class="link" title="<?php echo($post_data->post_title);?>"><?php echo($post_title);?></a></td>
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
						<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
						<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo(site_url());?>" class="link" title="/">/</a></td>
						<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
						<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
						<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
						<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
					  </tr>		
					<?php
					}
			}
			else {
				if($result->stat_post_id != "-1")
				{
				?>
				  <tr>
					<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
					<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo($post_url);?>" class="link" title="<?php echo($post_data->post_title);?>"><?php echo($post_title);?></a></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
					<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
				  </tr>
				<?php

				}
				else {
				?>
				  <tr>
					<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
					<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>"><span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href="<?php echo(site_url());?>" class="link" title="/">/</a></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($result->visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
					<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
					<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
				  </tr>		
				<?php
				}
			}
			}

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
	}//end of function
	
	
	//This function returns total page Views/Visitors
	function get_total_page_visitors($duration,$startDate,$endDate) {
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
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "yesterday") {
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where 	                      DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "7days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "14days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "30days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($duration == "60days")
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";
		}
		elseif($startDate)
		{
			$startDate = date("Y-m-d",strtotime($startDate));
			$endDate = date("Y-m-d",strtotime($endDate));
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between '$startDate' and '$endDate' AND stat_visitor_id not in(".$list_ips.") ";
		}
		else
		{
			$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where stat_visitor_id not in(".$list_ips.") ";
		}

		//echo($query);
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}
	/*This function returns  page Views/Visitors corresponding to display options
	like today, yesterday, 7days, 14days etc.*/
	function get_page_visitor_records($duration,$startDate,$endDate)
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
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats  where DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' and stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($duration == "yesterday") {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) and stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($duration == "7days") {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($duration == "14days") {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($duration == "30days") {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($duration == "60days") {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		elseif($startDate)
		{
			$startDate = date("Y-m-d",strtotime($startDate));
			$endDate = date("Y-m-d",strtotime($endDate));

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between '$startDate' and '$endDate'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}
		else {
			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";
		}

		//echo($query);
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}
	
	//This function displays graph corresponding to page views/visitors
	function view_page_stat_graph($intvl,$startDate,$endDate){
		global $wpdb;
		
		$data = "# Graph\n";
		$data .= "Day\tAll Visits\tUnique Visitors\n";
		
		$Results = $this->get_page_stat_graph_records($intvl,$startDate,$endDate);
		//echo "<pre>";
		//print_r($Results);
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

			$res = array();
			$unique_visitors = 0;
			$query1 = "select stat_date,stat_visitor_id,visitor_id
					 from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d')='".date('Y-m-d',strtotime($result->stat_date))."' AND stat_visitor_id not in(".$list_ips.") ";
			$Results1 = $wpdb->get_results($query1, OBJECT);
		//echo($query1.'<br />');
			foreach($Results1 as $result1)
			{
				$res[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res = array_flip($res);

			$unique_visitors = count($res);


			if($i++ == 1)
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
			else
			{
				$data .= date("l, F d, Y",strtotime($result->stat_date))."\t".$result->visitors."\t".$unique_visitors."\n";
			}
		}
		$absPath = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/";
		
		$f = fopen($absPath.'analytics_page.tsv' , 'wb');
		fwrite($f , $data );
		fclose($f);
		$jsPath = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			?>
			
			
			
			<!-- 1. Add these JavaScript inclusions in the head of your page -->
			
			<!-- 2. Add the JavaScript to initialize the chart on document ready -->
			<script type="text/javascript">
			
				var chart2;
				jQuery(document).ready(function() {
					
					// define the options
					var options = {
				
						chart: {
							renderTo: 'container2'
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
					jQuery.get('<?php echo $jsPath; ?>analytics_page.tsv', null, function(tsv, state, xhr) {
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
						
						chart2 = new Highcharts.Chart(options);
					});
					
				});
					
			</script>
			
			<!-- Additional files for the Highslide popup effect -->


			
			<!-- 3. Add the container -->
			
			
			<?php
		
	}
	
	//This function returns records coressponding to page views/visitors
	function get_page_stat_graph_records($duration,$startDate,$endDate)
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
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "yesterday") {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d')= DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day)  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "7days") {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "14days") {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "30days") {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($duration == "60days") {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")  group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		elseif($startDate)
		{
			$startDate = date("Y-m-d",strtotime($startDate));
			$endDate = date("Y-m-d",strtotime($endDate));

			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between '$startDate' and '$endDate'  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		else {
			$query = "select stat_date,count(distinct(stat_visitor_id)) as unique_visitors, count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by DATE_FORMAT(stat_date,'%Y-%m-%d')";
		}
		
		$Results = $wpdb->get_results($query, OBJECT);
		return $Results;
	}


	function view_page_stats_details($duration,$fromDate,$toDate){
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
		
		$to_date = date("Y M d");
		if($fromDate)
		{
			$frDate = date("Y-m-d",strtotime($fromDate));
			$tDate = date("Y-m-d",strtotime($toDate));
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between '$frDate' and '$tDate'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between '$frDate' and '$tDate'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}
		else
		{
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}

		if($duration == "today")
		{
			$from_date = date("Y M d");
			
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id ";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}
		
		if($duration == "yesterday")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
			$to_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp'))));
						
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY) AND stat_visitor_id not in(".$list_ips.")  group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY)  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
			
		}
		
		if($duration == "7days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp'))));
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}
		
		if($duration == "14days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp'))));
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}
		

		if($duration == "30days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp'))));
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}
		
		if($duration == "60days")
		{
			$from_date = date("Y M d",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp'))));
			$query1 = "select stat_post_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id";
			$query2 = "select stat_post_id,count(distinct(stat_visitor_id)) as unique_visitors,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  AND stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc";
		}

		//echo($query1.'<br />');
		//echo($query2.'<br />');

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
			$detailsPath = "admin.php?page=".TRACK_PLUGIN_NAME."&details=page&fromDate=".$fromDate."&toDate=".$toDate;
		}


	?>	
    <div class="graph-con">
    	<div class="graph-left">
        	<div class="graph-left1"></div>
            <div class="graph-left2" align="center">
            <div class="left-title">View Graph</div>
            	<ul class="memu-ul">
                	<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=today">Today</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=yesterday">Yesterday</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=7days">Last 7 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=14days">Last 14 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=30days">Last 30 Days</a></li>
                    <li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&details=page&duration=60days">Last 60 Days</a></li>
                </ul>
            </div>
            <div class="graph-left3"></div>
        </div>
        <div class="graph-right">
        	<div class="graph-right1"></div>
            <div class="graph-right2" align="center" id="container2"><?php 				$this->view_page_stat_graph($duration,$fromDate,$toDate); ?></div>
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
			
			if(isset($_COOKIE ['paging_records_stats'])) {
				
				$limit = $_COOKIE['paging_records_stats'];
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
		$Results = $this->get_total_page_visitors($duration,$fromDate,$toDate);
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
    <div class="select-r"> <img src="<?php echo($path); ?>images/cov.png" alt="" /></div>
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
    <td width="442" align="left" class="t-top-left-next"><span class="align"> Page </span></td>
    <td width="150" align="center" class="t-top-left-next">Views/Visitors</td>
    <td width="186" align="center" class="t-top-left-next">Unique Visitors</td>
    <td width="168" class="t-top-left-next">% Viewed</td>
    <td width="18" class="t-top-right">&nbsp;</td>
  </tr>

  <?php
	  $count=0;
	 $css_class="";
		foreach($result as $res)
		{
			 $count++;	
			$post_data = get_post($res->stat_post_id);
			$post_url = get_permalink($res->stat_post_id);

			$percent_viewed = ($res->visitors * 100)/$total_visitors;

			//two tokens
			$res1 = array();
			$unique_visitors = 0;


$keyword_interval = $duration;
if($keyword_interval == "today")
		{
			$query1 = "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$res->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($keyword_interval == "yesterday")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$res->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($keyword_interval == "7days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$res->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($keyword_interval == "14days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where    stat_post_id='$res->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($keyword_interval == "30days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where  stat_post_id='$res->stat_post_id' AND   DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($keyword_interval == "60days")
		{
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where   stat_post_id='$res->stat_post_id' AND  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.")";
		}
		elseif($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));
			$query1= "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where  stat_post_id='$res->stat_post_id' AND DATE_FORMAT(stat_date,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.")";
		}
		else
		{
			$query1 = "select stat_date,stat_visitor_id,visitor_id from ".$wpdb->prefix."tts_trafficstats where stat_post_id='$res->stat_post_id' AND stat_visitor_id not in(".$list_ips.")";
		}
		
		//	$query1 = "select stat_date,stat_visitor_id,visitor_id
		//			 from ".$wpdb->prefix."tts_trafficstats where stat_post_id='$res->stat_post_id'";
			$Results1 = $wpdb->get_results($query1, OBJECT);

			foreach($Results1 as $result1)
			{
				$res1[$result1->stat_visitor_id] = $result1->visitor_id;
			}
			
			$res1 = array_flip($res1);
			$unique_visitors = count($res1);

			if(strlen($post_data->post_title)>55) $post_title = substr_replace($post_data->post_title,'...',55); else $post_title = $post_data->post_title;

			if($count!=count($result)) {

				if($res->stat_post_id != "-1")
				{
			?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href='<?php echo $post_url; ?>' target='_blank' title='<?php echo $post_data->post_title; ?>' class="link"><?php echo($post_title); ?> </a>
				</td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($res->visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>
			<?php
					
				}
				else
				{
			?>
			  <tr>
				<td class="t-mid-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-mid-left-next<?php echo($css_class);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href='<?php echo site_url(); ?>' target='_blank' title='/' class="link">/</a>

				</td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($res->visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo($unique_visitors); ?></td>
				<td class="t-mid-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-mid-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>
			<?php
				}						
			}
			else {
			
			if($css_class=="") {
				$css_class="1";
			}
			else {
				$css_class="";
			}
?>

<?php
				if($res->stat_post_id != "-1")
				{
	?>
			  <tr>
				<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href='<?php echo $post_url; ?>' target='_blank' title='<?php echo $post_data->post_title; ?>' class="link"><?php echo($post_title); ?> </a>

				</td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($res->visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
	<?php

				}
				else
				{
?>
			  <tr>
				<td class="t-bot-left<?php echo($css_class);?>">&nbsp;</td>
				<td valign="middle" class="t-bot-left-next<?php echo($css_class);?>">
					<span class="sno_text"><?php echo($count);?>.&nbsp;&nbsp;</span><a href='<?php echo site_url(); ?>' target='_blank' title='/' class="link">/</a>


				</td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($res->visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo($unique_visitors);?></td>
				<td class="t-bot-left-next<?php echo($css_class);?>"><?php echo(round($percent_viewed,2));?>%</td>
				<td class="t-bot-right<?php echo($css_class);?>">&nbsp;</td>
			  </tr>	
<?php
				}				
			}
			$unique_visitors = count($res1);

			//two tokens end
			
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

	function view_traffic_data($duration,$fromDate,$toDate) {
		global $wpdb;
		$flag = 0;  //flag = 0 for decrease
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";

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

		if(!$duration && !$fromDate)
		{
			$duration = "30days";
		}
		if($duration == "today")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}

		if($duration == "yesterday")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 2 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}
		
		if($duration == "7days")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 7 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}

		if($duration == "14days")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 27 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 14 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}

		if($duration == "30days")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 30 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}
		if($duration == "60days")
		{
			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 59 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 119 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 60 Day) AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}

		if($fromDate)
		{
			$fromDate = date("Y-m-d",strtotime($fromDate));
			$toDate = date("Y-m-d",strtotime($toDate));

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";

			$current_results = $wpdb->get_results($query, OBJECT);

			$start_ts = strtotime($fromDate);
			$end_ts = strtotime($toDate);
			$diff = $end_ts - $start_ts;
			$day_diff = round($diff / 86400);

			$day_diff = $day_diff+1;


			$newToDate = strtotime ( '-1 day' , strtotime($fromDate)) ;
			$toDate = date ( 'Y-m-d' , $newToDate );

			$newFromDate = strtotime("-$day_diff day",strtotime($fromDate));
			$fromDate = date ( 'Y-m-d' , $newFromDate );

			$query = "select count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') between '$fromDate' and '$toDate' AND stat_visitor_id not in(".$list_ips.") ";

			$previous_results = $wpdb->get_results($query, OBJECT);
		}

		$current_visitors = $current_results[0]->visitors;
		$previous_visitors = $previous_results[0]->visitors;
		
		
		
		if($previous_visitors>0)
		{
			if($current_visitors > $previous_visitors)
			{
				$percent_change = (($current_visitors-$previous_visitors)*100)/$previous_visitors;
		?>
			<a href="#"><img src="<?php echo($path); ?>images/green-aero.png" alt="" border="none" /></a> 		
		<?php
		?>
		<span class="green-text"><?php echo(round($percent_change,2)).'%'; ?></span>
		<?php
				
				

			}
			else
			{
		?>
			<a href="#"><img src="<?php echo($path); ?>images/red-aero.png" alt="" border="none" /></a> 		
		<?php
				$percent_change = (($current_visitors-$previous_visitors)*100)/$previous_visitors;
		?>
		<span class="red-text"><?php echo(round($percent_change,2)).'%'; ?></span>
		<?php
				
			}
		}
		else
		{
			if($current_visitors > 0)
			{
		?>
			<a href="#"><img src="<?php echo($path); ?>images/green-aero.png" alt="" border="none" /></a> 		
		
		<span class="green-text"><?php echo "100%"; ?></span>
		<?php
			}
		}
		
		
	}

	//This function is used to display traffic increase/decrease % displayed in red/green for all the page views/visitors data collected
	function view_traffic_stats_data($data_intvl="week"){
		global $wpdb;
		$flag = 0;  //flag = 0 for decrease
		
		echo "<table  width='99%' cellspacing='0' cellpadding='0' border='1' align='left'><tr><td style='font-size:16px;padding-top:30px;'><strong>Traffic Data</strong></td></tr>";

		echo "<tr><td style='height:9px'></td></tr><tr><td align='right'><table cellpsacing='0' cellpadding='0'><tr>";
		if($data_intvl == "day")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=day' class='button-secondary' title='day' style='color:#E66F21'>Day</a></td>";

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' group by stat_post_id";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') = DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Day) group by stat_post_id";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d")." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-1, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m"), date("d")-1, date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=day' class='button-secondary' title='day'>Day</a></td>";
		}

		if($data_intvl == "week")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=week' class='button-secondary' title='week' style='color:#E66F21'>Week</a></td>";

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 Day) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."' group by stat_post_id";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 Day) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 7 Day) group by stat_post_id";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-6, date("y")))." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m"), date("d")-13, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m"), date("d")-7, date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=week' class='button-secondary' title='week'>Week</a></td>";
		}


		if($data_intvl == "month")
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=month' class='button-secondary' title='month' style='color:#E66F21'>Month</a></td>";

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) AND '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'  and DATE_FORMAT(stat_date,'%Y-%m-%d') != DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) group by stat_post_id";

			$current_results = $wpdb->get_results($query, OBJECT);

			$query = "select stat_post_id,count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d') BETWEEN DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 2 Month) AND DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 Month) and DATE_FORMAT(stat_date,'%Y-%m-%d') != DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 2 Month) group by stat_post_id";

			$previous_results = $wpdb->get_results($query, OBJECT);

			$current_time = date("Y M d",mktime(0, 0, 0, date("m")-1, date("d")+1, date("y")))." - ".date("Y M d");
			$previous_time = date("Y M d",mktime(0, 0, 0, date("m")-2, date("d")+1, date("y")))." - ".date("Y M d",mktime(0, 0, 0, date("m")-1, date("d"), date("y")));
		}
		else
		{
			echo "<td><a href='admin.php?page=".TRACK_PLUGIN_NAME."&details=page&data_intvl=month' class='button-secondary' title='month'>Month</a></td>";
		}

		echo "</tr></table></td></tr>";

		echo "<tr><td style='padding-right:5px;padding-top:10px;line-height:25px;color:#555555;font-size:12px;text-align:right'>".$current_time."</td></tr>";
		echo "<tr><td style='padding-right:5px;line-height:25px;padding-bottom:10px;color:#555555;font-size:12px;text-align:right'>Comparing to ".$previous_time."</td></tr>";

		foreach($previous_results as $result)
		{
			$prev_results[$result->stat_post_id] = $result->visitors;
		}

		echo "<tr><td><table class='widefat'><thead><tr><th>Post/Page</th><th>Visits</th><th style='width:100px;margin-right:10px'>Percent Change</th></tr></thead>";

		if($current_results)
		{
			foreach($current_results as $result)
			{
				$post_data = get_post($result->stat_post_id);
			
				$post_url = get_permalink($result->stat_post_id);

				echo "<tr><td style='width:500px'><a href='$post_url' target='_blank'>".$post_data->post_title."</a></td><td>".$result->visitors."</td>";
				if(!$result->visitors)
				{
					$result->visitors = "0";
				}
				//calculate percent change
				if($result->visitors > $prev_results[$result->stat_post_id])
				{
					if($prev_results[$result->stat_post_id])
					{
						$percent_change = (($result->visitors-$prev_results[$result->stat_post_id])*100)/$prev_results[$result->stat_post_id];
					}
					else
					{
						$percent_change = (($result->visitors-$prev_results[$result->stat_post_id])*100);
					}
					
					echo "<td style='color:#62B073;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/greenuparrow.png' border='0'> ".round($percent_change,2)."%"."</td>";
				}
				else
				{
					if($prev_results[$result->stat_post_id])
					{
						$percent_change = (($result->visitors-$prev_results[$result->stat_post_id])*100)/$prev_results[$result->stat_post_id];
						echo "<td style='color:#FF0000;'><img src='".WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/images/reddownarrow.png' border='0'>".round($percent_change,2)."%"."</td>";
					}
					
				}
				echo "</tr>";
			}
		}
		else
		{
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


}//end of class
?>