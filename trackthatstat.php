<?php
/*
Plugin Name: Track That Stat
Plugin URI: http://trackthatstat.org
Description: View all of your traffic in real time. Analyze your visitors, search engine terms, referral traffic and more with our userfriendly interface. 
Version: 1.2.1
Author: Joe 
Author URI: http://trackthatstat.org
*/

error_reporting(E_ALL || ~E_NOTICE || ~E_WARNING);

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ).DIRECTORY_SEPARATOR.'wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH.'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins' );
if ( ! defined('TRACK_PLUGIN_NAME'))
	 define( 'TRACK_PLUGIN_NAME', 'track-that-stat' );

include("stats.php");
include("referrer_stats.php");
include("keyword_stats.php");
include("visitors.php");

// Creating database structure on plugin activation
register_activation_hook( __FILE__, array( 'TrafficstatsPlugin', '_assert_db_structure' ) );

add_action('wp_enqueue_scripts', 'load_trackthatstat_scripts');
add_action('admin_enqueue_scripts', 'load_trackthatstat_admin_scripts');
//add_action('wp_enqueue_style', 'load_trackthatstat_admin_style');

//Creating class for Traffic stats plugin

if (! class_exists('TrafficstatsPlugin')) {
	class TrafficstatsPlugin {
		function TrafficstatsPlugin() {
	

			if((isset($_GET['aweber_redirect']) & isset($_GET['val']) && !empty($_GET['aweber_redirect']) && !empty($_GET['val']) && isset($_COOKIE['trackstat_aweber']) && $_COOKIE['trackstat_aweber']==$_GET['val']) || ((isset($_GET['track_opt'])) && $_GET['track_opt']=='skipoptin')) {
				$option_name = 'track_that_stats_subscription' ;
				$newvalue = 'subscribed' ;
				$deprecated = ' ';
				$autoload = 'no';
				add_option( $option_name, $newvalue, $deprecated, $autoload );	
				setcookie('trackstat_aweber','trackstat_aweber',-3600,COOKIEPATH, COOKIE_DOMAIN, false);
				header( "Location: admin.php?page=".TRACK_PLUGIN_NAME );
				exit;
			}

			$this->set_cookie_data();
			add_action('admin_menu', array(&$this, 'add_trafficstats_admin'));

			if ( get_option( 'track_that_stats_subscription' ) ) {
				add_action('wp', array(&$this, 'update_traffic_stats'));
				add_filter('the_content', array(&$this, 'trafficstats_display_hook'));

				add_action('wp_dashboard_setup', array(&$this, 'trafficstats_dashboard_setup'));
			}
			
		}//end of constructor function
	


		//This function will create link track that stat on the admin side
		function add_trafficstats_admin() {
			$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			if ( get_option( 'track_that_stats_subscription' ) ) {
				add_menu_page('Track That Stat','Track That Stat','8',TRACK_PLUGIN_NAME, array(&$this, 'view_traffic_stats'),$path.'images/track.png');

				add_submenu_page(TRACK_PLUGIN_NAME,'Overview','Overview','8',TRACK_PLUGIN_NAME,array(&$this, 'view_traffic_stats'));
				add_submenu_page(TRACK_PLUGIN_NAME,'Settings','Settings','8','settings',array(&$this, 'settings'));

			}
			else {
				add_menu_page('Track That Stat','Track That Stat','8',TRACK_PLUGIN_NAME, array(&$this, 'trafficstats_display_aweber'));
			}

			
		}//end of function

		function trafficstats_display_aweber()
		{
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<link type='text/css' rel='stylesheet' href='".$path."css/style.css' media='screen'></LINK>";
?>
<style type="text/css">
<!--
.style1 {font-family: Arial, Helvetica, sans-serif}
-->
</style>


		<div class="main"><!-- main starts -->
<!----------------------------------------------- header ------------------------------------------------------>
	<div class="header">
		
    	<div class="header_left"><img src="<?php echo($path); ?>images/logo.png" alt="" border="none" /></div>
		</div>
		<div class="clear"></div>
<?php
					$cookie_val=md5(time()+"aweber"+rand(5,9999));

			echo('<div style="margin-top:50px;"><script type="text/javascript" src="http://forms.aweber.com/form/24/789150724.js"></script><div style="text-align:center;">
<a style="float:none;" class="link" href="admin.php?page='.TRACK_PLUGIN_NAME.'&track_opt=skipoptin">Click Here Now To Access The Plugin Without Opting In</a>
</div></div>');
			$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
			echo("<script type='text/javascript'>");

		?>
				function setCookie(name,value) {
					
					var date = new Date();
					date.setTime(date.getTime()+(1*.5*60*60*1000));
					var expires = "; expires="+date.toGMTString();
					
					document.cookie = name+"="+value+expires+"; path=<?php echo(COOKIEPATH);?>;domain=<?php echo(COOKIE_DOMAIN);?>";
				}

				function getCookie(name) {
					var nameEQ = name + "=";
					var ca = document.cookie.split(';');
					for(var i=0;i < ca.length;i++) {
						var c = ca[i];
						while (c.charAt(0)==' ') c = c.substring(1,c.length);
						if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
					}
					return false;
				}

				function deleteCookie(name) {
					setCookie(name,"",-1);
				}
			
			setCookie("trackstat_aweber","<?php echo($cookie_val);?>");

			jQuery(document).ready(function(){
				var redirect=jQuery(".af-form-wrapper input[name='redirect']").val();

				var hidden_obj=jQuery(".af-form-wrapper input[name='redirect']");
				jQuery(".af-form-wrapper input[name='redirect']").remove();
				jQuery(".af-form-wrapper").append('<input type="hidden" name="redirect66" id="'+jQuery(hidden_obj).attr('id')+'" value="'+redirect+"&aweber_redirect=true&val=<?php echo($cookie_val);?>"+'" />');
				
				jQuery(".af-form-wrapper").append('<input type="hidden" name="redirect" id="88'+jQuery(hidden_obj).attr('id')+'" value="'+redirect+"&aweber_redirect=true&val=<?php echo($cookie_val);?>"+'" />');
				jQuery(".af-form-wrapper").append('<input type="hidden" name="meta_redirect_onlist" value="'+redirect+"&aweber_redirect=true&val=<?php echo($cookie_val);?>"+'" />');

			});
		<?php
			echo "</script>";
?>

</div>
<div class="main-bottom"></div>

<?php
		}
		function view_traffic_stats(){

				$ReferrerStats = new RefferStats();
				$KeywordStats = new KeywordStats();
				$Stats = new Stats();
				$Visitors = new Visitors();
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<link type='text/css' rel='stylesheet' href='".$path."css/style.css' media='screen'></LINK>";

		?>
		<script type='text/javascript'>
			function track_form_validator()
			{
				if(document.getElementById('fromDate').value == '')
				{
					alert('Please enter From Date');
					document.getElementById('fromDate').focus();
					return false;
				}
				if(document.getElementById('toDate').value == '')
				{
					alert('Please enter To Date');
					document.getElementById('toDate').focus();
					return false;
				}
				var str1 = document.getElementById('fromDate').value;
			    var str2 = document.getElementById('toDate').value;
			    var yr1  = parseInt(str1.substring(0,4),10);
			    var mon1 = parseInt(str1.substring(5,7),10);
			    var dt1  = parseInt(str1.substring(8,10),10);
			    var yr2  = parseInt(str2.substring(0,4),10);
			    var mon2 = parseInt(str2.substring(5,7),10);
			    var dt2  = parseInt(str2.substring(8,10),10);
				mon1 = mon1-1;
				mon2 = mon2-1;
			    var date1 = new Date(yr1, mon1, dt1);
			    var date2 = new Date(yr2, mon2, dt2);

				if(date2 < date1)
				{
					alert('To date must be greater than from date');
					return false;
				}
			}
		</script>
			<div align="center">
			
<div style="width:1000px;height:160px;">
		<h3 align="center"><a href="http://trackthatstat.org" target="_blank">TTS Blog</a>  | <a href="http://wordpress.org/extend/plugins/track-that-stat/faq/" target="_blank">FAQ</a> | <a href="http://trackthatstat.org/contact-us/" target="_blank">Support</a> | <a href="http://trackthatstat.org/donate/" target="_blank">Donate</a> | <a href="http://trackthatstat.org/video/tts.html" target="_blank">Video Guide</a> </h3>
          
			
			  <p class="style1">Thank you for using our new plugin. If you have any questions please feel free to use the support button above. <br><br><a href="http://trackthatstat.org/go/promotion/" target="_blank"><img src="http://server.trackthatstat.org/images/promo.gif" width="728" height="90" border="0" /></a></p>
			  </div>
			</div>
	
		<div class="main"><!-- main starts -->
		
<!----------------------------------------------- header ------------------------------------------------------>
	
	<div class="header">
	
	
    	<div class="header_left"><img src="<?php echo($path); ?>images/logo.png" alt="" border="none" /></div>
		
        <div class="header_right">
        	<div class="menu_bg">
            	<ul class="menu">
                	<li><a <?php echo($_REQUEST['details'] == "referral" ? 'class="active"' : '');?> href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=referral">Referrals</a></li>
                    <li><a <?php echo($_REQUEST['details'] == "keyword" ? 'class="active"' : '');?> href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=keyword">Keywords</a></li>
                    <li><a <?php echo($_REQUEST['details'] == "page" ? 'class="active"' : '');?> href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=page">Content Viewed</a></li>
                    <li><a <?php echo($_REQUEST['details'] == "visitor" ? 'class="active"' : '');?> href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=visitor">Visitor Details</a></li>
                    <?php
						if(isset($_REQUEST['details'])) {
					?>
						<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>">Main</a></li>
					<?php
						}
					?>
                </ul>
            </div>
            <div class="searh_bg">
			<form method='get' onsubmit='return track_form_validator();'>
			<input type="hidden" name="page" value="<?php echo(TRACK_PLUGIN_NAME);?>" />
			<?php
				if(isset($_REQUEST['details']) && $_REQUEST['details']!='') {
			?>
			<input type="hidden" name="details" value="<?php echo($_REQUEST['details']);?>" />
			<?php
				}
			?>
            	<div class="search_title">Search</div>
                <div class="search_form">
                	<div class="search_form1">
                        <label class="search-text1">From Date :</label>
                        <input readonly name='fromDate' id='fromDate' value='<?php echo $_REQUEST['fromDate'] ?>' type="text" class="search-text" />
                        <img src="<?php echo($path); ?>images/caled.png" alt="" class="Calender" onclick="displayCalendar(document.getElementById('fromDate'),'yyyy/mm/dd',this)" />
                     </div>
                     <div class="search_form2">
                        <label class="search-text1">To Date :</label>
                        <input readonly name='toDate' id='toDate' value='<?php echo $_REQUEST['toDate'] ?>' type="text" class="search-text" />
                         <img src="<?php echo($path); ?>images/caled.png" alt="" class="Calender" onclick="displayCalendar(document.getElementById('toDate'),'yyyy/mm/dd',this)" />
                         
                     </div>
                     <div class="sub-btn"><input type="submit" value="Apply" class="sub" /></div>
                     <p align="right" class="date-next">
						<?php
						if(isset($_REQUEST['fromDate']) && $_REQUEST['fromDate']!='') {
					 ?>
						<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",strtotime($_REQUEST['fromDate'])));?>&nbsp;&nbsp;					 
					 <?php
						} 
						if(isset($_REQUEST['toDate']) && $_REQUEST['toDate']!='') {
					 ?>
						<strong>To Date :</strong> <?php echo(strftime("%b %d %Y",strtotime($_REQUEST['toDate'])));?>					 
					 <?php
						}
					 
						if(isset($_REQUEST['duration'])) {
							switch ($_REQUEST['duration']) {
								case "today":
						?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>					 


						<?php
									break;
								case "yesterday":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp')))));?>	
<?php
									break;
								case "7days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "14days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "30days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "60days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								default :
							}
						}

						if(isset($_REQUEST['interval'])) {

							switch ($_REQUEST['interval']) {
								case "today":
						?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>					 

						<?php
									break;
								case "yesterday":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-1, date('Y',current_time( 'timestamp')))));?>	
<?php
									break;
								case "7days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-6, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "14days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-13, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "30days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-29, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								case "60days":
?>
							<strong>From Date :</strong> <?php echo(strftime("%b %d %Y",mktime(0, 0, 0, date('m',current_time( 'timestamp')), date('d',current_time( 'timestamp'))-59, date('Y',current_time( 'timestamp')))));?>&nbsp;&nbsp;
						   <strong>To Date :</strong> <?php echo(strftime("%b %d %Y",current_time( 'timestamp')));?>	
<?php
									break;
								default :
							}
						}

						?>
						
					 </p>
                </div>
            </div>
			</form>
        </div>
    </div>
				<link rel="stylesheet" type="text/css" href="http://www.highcharts.com/highslide/highslide.css" />

<!----------------------------------------------- end header -------------------------------------------------->	
		<?php

			if($_REQUEST['details'] == "referral")
			{
				$duration	= $_REQUEST['duration'];
				$fromDate	= $_REQUEST['fromDate'];
				$toDate		= $_REQUEST['toDate'];
				//This function displays the referral views/visitors details with paging
				$ReferrerStats->view_referral_stats_details($duration,$fromDate,$toDate);
			}
			elseif($_REQUEST['details'] == "page")
			{			

				$duration	= $_REQUEST['duration'];
				$fromDate	= $_REQUEST['fromDate'];
				$toDate		= $_REQUEST['toDate'];
				//This function displays the page views/visitors details with paging
				$Stats->view_page_stats_details($duration,$fromDate,$toDate);
			}
			elseif($_REQUEST['details'] == "keyword")
			{
				$duration	= $_REQUEST['duration'];
				$fromDate	= $_REQUEST['fromDate'];
				$toDate		= $_REQUEST['toDate'];
				//This function displays the keyword views/visitors details with paging 
				$KeywordStats->view_keyword_stats_details($duration,$fromDate,$toDate);
			}
			elseif($_REQUEST['details'] == "visitor")
			{			

				$duration	= $_REQUEST['duration'];
				$fromDate	= $_REQUEST['fromDate'];
				$toDate		= $_REQUEST['toDate'];
				$Visitors->view_visitors_data($duration,$fromDate,$toDate);
			}
			else
			{
				$interval = $_REQUEST['interval'];				
				
				//This function displays page views , referral and keyword visitors graph.
				$Stats->display_page_views_graph_main($interval,$_REQUEST['fromDate'],$_REQUEST['toDate']);
						

			?>
<!----------------------------------------------- graph mid title ------------------------------------------------------>
    <div class="mid-graph">
    <p class="text">Percentage Changed  &nbsp;<?php echo($Stats->view_traffic_data($interval,$_REQUEST['fromDate'],$_REQUEST['toDate']));?></p>
    </div>
<!----------------------------------------------- graph mid title ------------------------------------------------------>

    <div class="cont" align="center">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
     <td height="60" width="50%" align="left"><img src="<?php echo($path); ?>images/ref.png" alt="" /></td>
     <td width="50%" align="left"><img src="<?php echo($path); ?>images/key.png" alt="" /></td>
    </tr>
<tr>
    <td align="left" valign="top">

			<?php
				$ReferrerStats->view_traffic_referral_stats($interval,$_REQUEST['fromDate'],$_REQUEST['toDate']);
			?>	
		 </td>
    <td align="right" valign="top">
	
	<?php
				//This function displays keywords vistors displayed and percentage viewed compared to others.
				$KeywordStats->view_keyword_stats($interval,$_REQUEST['fromDate'],$_REQUEST['toDate']);
	?>
	</td>
	  </tr>
  <tr>
    <td height="40" align="left"><img src="<?php echo($path); ?>images/cov.png" alt="" /></td>
    <td align="left"><img src="<?php echo($path); ?>images/vis.png" alt="" /></td>
  </tr>

  <tr>
    <td align="left" valign="top">
	<?php
				
				$data_interval = "week";
				if($_REQUEST['data_interval'])
				{
					$data_interval = $_REQUEST['data_interval'];
				}
				
				//This function displays page views/unique vistors displayed and percentage viewed compared to others.
				$Stats->view_page_stats($interval,$_REQUEST['fromDate'],$_REQUEST['toDate']);
	?>
	</td>
    <td align="right" valign="top">
	<?php
				$Visitors->visitors_online();
				
				
				//This function is used to display traffic increase/decrease % displayed in red/green for all the page views/visitors data collected
				$data_intvl = "week";
				if($_REQUEST['data_intvl'])
				{
					$data_intvl = $_REQUEST['data_intvl'];
				}
				
			}
		?>
</td>
  </tr>
		</table>
		</div>
        <div class="footer-menu">
        <ul>
			<li <?php echo($_REQUEST['details'] == "referral" ? 'class="current"' : '');?>><a  href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=referral">Referrals</a></li>
			<li <?php echo($_REQUEST['details'] == "keyword" ? 'class="current"' : '');?>><a  href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=keyword">Keywords</a></li>
			<li <?php echo($_REQUEST['details'] == "page" ? 'class="current"' : '');?> ><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=page">Content Viewed</a></li>
			<li <?php echo($_REQUEST['details'] == "visitor" ? 'class="current"' : '');?>><a  href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>&amp;details=visitor">Visitor Details</a></li>
			<?php
				if(isset($_REQUEST['details'])) {
			?>
				<li><a href="admin.php?page=<?php echo(TRACK_PLUGIN_NAME);?>">Main</a></li>
			<?php
				}
			?>

		</ul>
        
        </div>
		</div><!-- main ends -->
        
<div class="main-bottom"></div>

		<?php
		}

		function settings()
		{
			global $wpdb;
		$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
		echo "<link type='text/css' rel='stylesheet' href='".$path."css/style.css' media='screen'></LINK>";
		?>
			<div class="main"><!-- main starts -->
	<!----------------------------------------------- header ------------------------------------------------------>
	<div class="header">
    	<div class="header_left"><img src="<?php echo($path); ?>images/logo.png" alt="" border="none" /></div>
		</div>
		<div class="clear"></div>
		<div style="padding:40px 0 100px 100px;width:100%;">
		<?php
			if($_POST['formSubmitted'] == '1')
			{
				$list_ips = $_POST['list_ips'];
				
				
					$query = "Select list_ip_address from ".$wpdb->prefix."tts_settings";
					$Results		=	$wpdb->get_results($query, OBJECT);
					if(count($Results))
					{
						$wpdb->query("Update ".$wpdb->prefix."tts_settings set list_ip_address = '$list_ips' where list_ip_address='".$Results[0]->list_ip_address."'");
					}
					else
					{
						$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_settings SET list_ip_address = '$list_ips'");
					}

					echo "<b>IP Addresses updated successfully</b>";
					
				

			}
			$query = "Select list_ip_address from ".$wpdb->prefix."tts_settings";
			$Results		=	$wpdb->get_results($query, OBJECT);
			echo "<form name='excude_ips' action='admin.php?page=settings' method='post'><table width='100%' cellspacing='0' cellpadding='0'><tr><td style='padding-top:15px'>Enter the IP Addresses to exclude from the filter list</td></tr><tr><td><textarea cols='70' rows='5' name='list_ips' id='list_ips'>".$Results[0]->list_ip_address."</textarea></td></tr><tr><td>Comma separated value(ex: 127.0.0.1, 34.23.22.11)</td></tr><tr><td style='height:10px;'></td></tr><tr><td><input type='hidden' name='formSubmitted' value='1'><input type='submit' name='submit' value='Submit'></td></tr></table></form>";
?>
</div>

</div>
<div class="main-bottom"></div>
<?php
		}

		 
		/*
		 * Display the tracking track
		 */

		function trafficstats_display_hook($content="")
		{	
			static $loaded;
			if( empty($loaded) && (is_single() || is_home())) {
				$loaded='loaded';
				global $post;
				$post_id = $post->ID;
				if(is_home()) {
					$data='home: "home"';
				}
				else {
					$data='current_post_id: "'.$post_id.'"';
				}
				$path = WP_PLUGIN_URL."/".TRACK_PLUGIN_NAME."/";
				$content .=  "<script type='text/javascript'>";
				$content .= '
				jQuery(document).ready(function(){
					jQuery.post("'.$path.'registerPageSession.php",{ '.$data.' }); 
					jQuery.post("'.$path.'update.php",{ ajax: "ajax" });
					setInterval("updateOnlineStatus()", 50000); // Update every 50 seconds
				});
				function updateOnlineStatus() {
				  jQuery.post("'.$path.'update.php",{ ajax: "ajax" });
				}
				';
				$content .=  "</script>";
			}

			return ($content);
		}

		function update_traffic_stats() {
			global $post;
			global $wpdb;
			$ref_data = $_SERVER['HTTP_REFERER'];
		
			$referrer = str_replace(' ', '+', $ref_data);
			$referrer = preg_replace('/^http:\/\//i', '', $referrer);
			$referrer = preg_replace('/^www\./i', '', $referrer);

			$post_id = $post->ID;
			
			$browser_os_info=fetch_browser_os_info();
			
			$search_term = search_keyword($referrer);
			

			$referrer = preg_replace('/\/(.*)/', '', $referrer);

			//insert IP Address data into the database

			//$post_data = get_post($post_id);
		
			$page = get_permalink($post_id);
			$cookie_ip=base64_decode($_COOKIE['siteVisited']);
			if(!isset($_COOKIE['siteVisited']) || !isset($cookie_ip) || $cookie_ip=='' || empty($cookie_ip)) {
				$cookie_ip=get_visitor_id();
			}
			// get the current IP from cookies
		
			/*if(is_home() && isset($_COOKIE['home_visited']) && $_COOKIE['home_visited']!='')
			{
				return; // No need to proceed if its just a page refresh					
			}
			else
			*/
			if(is_home())
			{

				$post_id = -1;
				if(($referrer == $_SERVER['HTTP_HOST'] || $referrer == "") && $search_term == "")
				{	
					/* If the user returns from the same IP fetch data related to it
					 * else	
					 * If the user returns from a different IP fetch data related to it
					 */
					if($cookie_ip==get_visitor_id()) {
						//Run the code for page views/visitors
						$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and stat_visitor_id='".get_visitor_id()."'";
					}
					else {
						//Run the code for page views/visitors
						$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and (stat_visitor_id='".get_visitor_id()."' OR stat_visitor_id='".$cookie_ip."')";
					}
					//echo($query);
					$Results = $wpdb->get_results($query, OBJECT);
					if($Results[0]->num_entry == '0' && $_SESSION['siteVisitedCookieSet'])
					{

						write_trackstats($post_id);

						$page = "http://".$_SERVER['HTTP_HOST'];
						//adding ip address
					/* If the user returns from the same IP fetch data related to it
					 * else	
					 * If the user returns from a different IP fetch data related to it
					 */
					if($cookie_ip==get_visitor_id()) {
						$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and ip_address=\"".get_visitor_id()."\"";

					}
					else {
						$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and (ip_address=\"".get_visitor_id()."\" OR ip_address=\"".$cookie_ip."\")";						
					}
						$result = $wpdb->get_results($query, OBJECT);


						$show_city = "";	
						
						//$ip_address = get_visitor_id();		
						$ip_address = get_visitor_id();	

						
						$country_name = $this->getCountryName();

						/* If the user returns from the same IP Update/Insert data related to it
						 * else	
						 * If the user returns from a different IP Update data related to it
						 */

						if($cookie_ip==get_visitor_id()) {

							if($result[0]->total_visits > 0)
							{
								$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".get_visitor_id()."\" and page_url='$page'");
							}
							else
							{
								$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_visitors SET ip_address=\"".get_visitor_id()."\", country = \"".$country_name."\",page_url=\"".$page."\",page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\", time_visited=\"".current_time( 'timestamp')."\",time_last_visited=\"".current_time( 'timestamp')."\",num_visits='1'");
							}

						}
						else {
							$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\"browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".$cookie_ip."\" and page_url='$page'");
						}
					}
				}
				else
				{
					//run the code for referrals that are coming for external sites like google,yahoo
					if($referrer != "")
					{
						/* If the user returns from the same IP fetch data related to it
						 * else	
						 * If the user returns from a different IP fetch data related to it
						*/

						if($cookie_ip==get_visitor_id()) {
							$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and stat_visitor_id='".get_visitor_id()."'";
						}
						else {
							$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and (stat_visitor_id='".get_visitor_id()."' OR stat_visitor_id='".$cookie_ip."')";
						}
						$Results = $wpdb->get_results($query, OBJECT);
						if($Results[0]->num_entry == '0' && $_SESSION['siteVisitedCookieSet'])
						{
							if($cookie_ip==get_visitor_id()) {
								$ref_id = insert_referral_data($referrer,$post_id,get_visitor_id());
							}
							else {
								$ref_id = insert_referral_data($referrer,$post_id,$cookie_ip);
								
							}

						/* If the user returns from the same IP Insert data related to it
						 * else	
						 * If the user returns from a different IP Insert data related to it
						 */							
							//insert entry into traffic stats after a refrral is added
						if($cookie_ip==get_visitor_id()) {
							$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_trafficstats VALUES (\"".$post_id."\", now(), \"".get_visitor_id()."\",\"".get_visit_id()."\",$ref_id)");
						}
						else {
							$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_trafficstats VALUES (\"".$post_id."\", now(), \"".$cookie_ip."\",\"".get_visit_id()."\",$ref_id)");
						}
						
						//if keyword data exists then put keyword data into the database		
							if($search_term)
							{
								if($cookie_ip==get_visitor_id()) {
									insert_keyword_data($search_term,$post_id,get_visitor_id());
								}
								else {
									insert_keyword_data($search_term,$post_id,$cookie_ip);
									
								}
								
							}

							$page = "http://".$_SERVER['HTTP_HOST'];

							/* If the user returns from the same IP fetch data related to it
							 * else	
							 * If the user returns from a different IP fetch data related to it
							 */
							//adding ip address
							if($cookie_ip==get_visitor_id()) {
								$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and ip_address=\"".get_visitor_id()."\"";
							}
							else {
								$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and (ip_address=\"".get_visitor_id()."\" OR ip_address=\"".$cookie_ip."\" )";
							}
							
							$result = $wpdb->get_results($query, OBJECT);


							$show_city = "";	
							
							$ip_address = get_visitor_id();		

							$country_name = $this->getCountryName();

							/* If the user returns from the same IP Update/Insert data related to it
							 * else	
							 * If the user returns from a different IP Update data related to it
							 */

							if($cookie_ip==get_visitor_id()) {

								if($result[0]->total_visits > 0)
								{
									$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".get_visitor_id()."\" and page_url='$page'");
								}
								else
								{
									$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_visitors SET ip_address=\"".get_visitor_id()."\", country = \"".$country_name."\",page_url=\"".$page."\",page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\", time_visited=\"".current_time( 'timestamp')."\",time_last_visited=\"".current_time( 'timestamp')."\",num_visits='1'");
								}

							}
							else {
							
									$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".$cookie_ip."\" and page_url='$page'");
							}


						}
					}
				}


				

			}
			else
			{
				if($post_id==0 || empty($post_id) || $post_id==null || $post_id=='') {
					//return;
				}
				if(isset($_COOKIE['current_post_id']) && $_COOKIE['current_post_id']==$post_id) {
					return; // No need to proceed if its just a page refresh		
				}

								
				if(($referrer == $_SERVER['HTTP_HOST'] || $referrer == "") && $search_term == "")
				{
					/* If the user returns from the same IP fetch data related to it
					 * else	
					 * If the user returns from a different IP fetch data related to it
					 */					
					//Run the code for page views/visitors
					if($cookie_ip==get_visitor_id()) {
						$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and stat_visitor_id='".get_visitor_id()."'";
					}
					else {
						$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and (stat_visitor_id='".get_visitor_id()."' OR stat_visitor_id='".$cookie_ip."' )";
					}

					$Results = $wpdb->get_results($query, OBJECT);

					if($Results[0]->num_entry == '0' && $_SESSION['siteVisitedCookieSet'])
					{
						write_trackstats($post_id);

						//ip address
						/* If the user returns from the same IP fetch data related to it
						 * else	
						 * If the user returns from a different IP fetch data related to it
						 */
						//adding ip address
						if($cookie_ip==get_visitor_id()) {
							$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and ip_address=\"".get_visitor_id()."\"";
						}
						else {
							$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and (ip_address=\"".get_visitor_id()."\" OR ip_address=\"".$cookie_ip."\" )";
						}
						$result = $wpdb->get_results($query, OBJECT);
						
						$show_city = "";	
						
						$ip_address = get_visitor_id();		
						$country_name = $this->getCountryName();

						/* If the user returns from the same IP Update/Insert data related to it
						 * else	
						 * If the user returns from a different IP Update data related to it
						 */
					
						if($cookie_ip==get_visitor_id()) {
						
							if($result[0]->total_visits > 0)
							{
								$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".get_visitor_id()."\" and page_url='$page'");
							}
							else
							{
								$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_visitors SET ip_address=\"".get_visitor_id()."\", country = \"".$country_name."\",page_url=\"".$page."\",page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\", time_visited=\"".current_time( 'timestamp')."\",time_last_visited=\"".current_time( 'timestamp')."\",num_visits='1'");
							}

						}
						else {
								$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\" ,browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".$cookie_ip."\" and page_url='$page'");
						}
					}
				}
				else
				{
					//run the code for referrals that are coming for external sites like google,yahoo
					if($referrer != "")
					{

						/* If the user returns from the same IP fetch data related to it
						 * else	
						 * If the user returns from a different IP fetch data related to it
						 */
						if($cookie_ip==get_visitor_id()) {
							$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and stat_visitor_id='".get_visitor_id()."'";
						}
						else {
							$query = "select count(*) as num_entry from ".$wpdb->prefix."tts_trafficstats where stat_date=now() and (stat_visitor_id='".get_visitor_id()."' OR stat_visitor_id='".$cookie_ip."')";
						}
						
						$Results = $wpdb->get_results($query, OBJECT);
						if($Results[0]->num_entry == '0' && $_SESSION['siteVisitedCookieSet'])
						{
							if($cookie_ip==get_visitor_id()) {
								$ref_id = insert_referral_data($referrer,$post_id,get_visitor_id());
							}
							else {
								$ref_id = insert_referral_data($referrer,$post_id,$cookie_ip);
								
							}
							
							//insert entry into traffic stats after a refrral is added
							/* If the user returns from the same IP Update/Insert data related to it
							 * else	
							 * If the user returns from a different IP Update data related to it
							 */
							if($cookie_ip==get_visitor_id()) {
								$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_trafficstats VALUES (\"".$post_id."\", now(), \"".get_visitor_id()."\", \"".get_visit_id()."\",$ref_id)");
							}
							else {
								$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_trafficstats VALUES (\"".$post_id."\", now(), \"".$cookie_ip."\", \"".get_visit_id()."\",$ref_id)");
							}
							//if keyword data exists then put keyword data into the database		
							if($search_term)
							{
								if($cookie_ip==get_visitor_id()) {
									insert_keyword_data($search_term,$post_id,get_visitor_id());
								}
								else {
									insert_keyword_data($search_term,$post_id,$cookie_ip);
									
								}							}

							/* If the user returns from the same IP fetch data related to it
							 * else	
							 * If the user returns from a different IP fetch data related to it
							 */
							//ip address
							//adding ip address
							if($cookie_ip==get_visitor_id()) {
								$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and ip_address=\"".get_visitor_id()."\"";
							}
							else {
								$query = "select count(page_url) as total_visits from ".$wpdb->prefix."tts_visitors where page_url='$page' and ip_address=\"".$cookie_ip."\"";
							}

							$result = $wpdb->get_results($query, OBJECT);
							
							$show_city = "";	
							
							$ip_address = get_visitor_id();		

							$country_name = $this->getCountryName();

							/* If the user returns from the same IP Update/Insert data related to it
							 * else	
							 * If the user returns from a different IP Update data related to it
							 */
							if($cookie_ip==get_visitor_id()) {
							
								if($result[0]->total_visits > 0)
								{
									$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".get_visitor_id()."\" and page_url='$page'");
								}
								else
								{
									$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_visitors SET ip_address=\"".get_visitor_id()."\", country = \"".$country_name."\",page_url=\"".$page."\",page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\", time_visited=\"".current_time( 'timestamp')."\",time_last_visited=\"".current_time( 'timestamp')."\",num_visits='1'");
								}

							}
							else {
								$wpdb->query("UPDATE ".$wpdb->prefix."tts_visitors SET time_last_visited=\"".current_time( 'timestamp')."\",num_visits=num_visits+1,page_id=\"".$post_id."\",browser=\"".$browser_os_info['browser']."\",platform=\"".$browser_os_info['platform']."\" where ip_address=\"".$cookie_ip."\" and page_url='$page'");
							}


						}
					}
				}

			}
		
			return $content;
		}

		/*
		 * Creating the Database structure for this plugin (if not already existing)
		 */
		function _assert_db_structure() {
			global $wpdb;

			//creating table for traffic within the site
			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_trafficstats (`stat_post_id` int NOT NULL default '0', `stat_date` datetime NOT NULL default '0000-00-00 00:00:00' , `stat_visitor_id` VARCHAR( 32 ) NULL , `visitor_id` VARCHAR( 32 ) NULL , referrer_id int(11) NULL default '0') ENGINE = MyISAM");		

			//creating table for referrals
			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_referrer_stats (`id` bigint(20) NOT NULL AUTO_INCREMENT, `referrer` varchar(60) NOT NULL, `create_time` datetime NOT NULL,`post_id` int NOT NULL default '0',`stat_visitor_id` varchar(32) NULL, `visitor_id` VARCHAR( 32 ) NULL, PRIMARY KEY (`id`), KEY `referrer` (`referrer`)) ENGINE=MyISAM");
			 
			//creating table for keywords
			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_keyword_stats (`id` bigint(20) NOT NULL AUTO_INCREMENT, `keyword` varchar(60) NOT NULL, `create_time` datetime NOT NULL,`post_id` int NOT NULL default '0',`stat_visitor_id` varchar(32) NULL, `visitor_id` VARCHAR( 32 ) NULL, PRIMARY KEY (`id`), KEY `keyword` (`keyword`)) ENGINE=MyISAM");

			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_visitors(`id` bigint(20) NOT NULL AUTO_INCREMENT,`ip_address` varchar(50) not null default '',`country` varchar(50) default null,`browser` varchar(255) default null, `platform` varchar(255) default null, `page_url` text not null,`page_id` int(11) not null default '0',`time_visited` int(10) unsigned not null default '0',`time_last_visited` int(10) unsigned not null default '0',num_visits int(10) unsigned not null default '0',PRIMARY KEY (`id`)) ENGINE=MyISAM");


			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_online_status(`id` bigint(20) NOT NULL AUTO_INCREMENT, `ip_address` varchar(50) NOT NULL DEFAULT '',`last_active_time` int(10) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`id`),KEY `ip_address` (`ip_address`)) ENGINE=MyISAM");

			$wpdb->query("ALTER TABLE ".$wpdb->prefix."tts_visitors CHANGE `ip_address` `ip_address` VARCHAR(50) NOT NULL DEFAULT ''");

			$wpdb->query("ALTER TABLE ".$wpdb->prefix."tts_online_status CHANGE `ip_address` `ip_address` VARCHAR(50) NOT NULL DEFAULT ''");
			
			
			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."tts_settings(`list_ip_address` text) ENGINE=MyISAM");
			
			$wpdb->query("CREATE INDEX stat_visitor_id on ".$wpdb->prefix."tts_trafficstats (stat_visitor_id);");
			$wpdb->query("CREATE INDEX stat_visitor_id on ".$wpdb->prefix."tts_referrer_stats (stat_visitor_id);");
			$wpdb->query("CREATE INDEX stat_visitor_id on ".$wpdb->prefix."tts_keyword_stats (stat_visitor_id);");	
			$wpdb->query("CREATE INDEX ip_address on ".$wpdb->prefix."tts_visitors (ip_address);");
		}


		/*
		 * set visitor id
		 */
		function set_visitor_id() {
			if(empty($_COOKIE['visitor_id'])) {
				$visitor_id = substr(md5(sha1(crc32(md5(base64_decode(microtime())).microtime()))), 0, 32);
				setcookie('visitor_id', $visitor_id, current_time( 'timestamp')+3600*24*30);
			}
		}

		/*
		 * trackstats dashboard widget
		 */
		function trafficstats_dashboard_setup() {
			wp_add_dashboard_widget( 'trafficstats_widget', __( 'Track That Stat' ), 'trafficstats_widget');
		}

		/*
		 * Returns the country name of the visitor
		 * Checks if the country information exists in the cookies
		 * otherwise fetches the country info from the ipinfodb API
		*/
		function getCountryName()
		{
			$ip=get_visitor_id();


	

			if(!isset($_COOKIE['countryDetails'])) {
				$this->set_cookie_data(true);
			}

			$country_data=unserialize(base64_decode($_COOKIE['countryDetails']));
			$country_name=$country_data->countryName;

			if(empty($country_name) || $country_name=='' || !isset($_COOKIE['countryDetails']) || $country_name=='-' || strlen($country_name)<2) {

				$country_data=getUserGeoInfo($ip);
				$country_name=$country_data->countryName;
			}


			return ($country_name);
		}

		function set_cookie_data($country_details=false)
		{

			switch ($_REQUEST['details']) {
				case 'page':
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
				   setPageLimitCookie($limit,'paging_records_stats');		
				break;
				case 'referral':
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
				   setPageLimitCookie($limit,'paging_records_referrer');		
				break;
				case 'keyword':
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
				   setPageLimitCookie($limit,'paging_records_keywords');		
				break;
				case 'visitor':
					if($_REQUEST['paging_records'])
					{
						$limit = $_REQUEST['paging_records'];
					}
					else
					{
						if(isset($_COOKIE ['paging_records_visits'])) {
							
							$limit = $_COOKIE['paging_records_visits'];
						}
						else {
							$limit = 10;
						}
						
					}
				   setPageLimitCookie($limit,'paging_records_visits');		
				break;
				default :
		}
			$ip=get_visitor_id();
			if(!isset($_COOKIE['countryDetails'])) {
	
				$country_data=getUserGeoInfo($ip);



				// setting cookie of 12hours
		        setcookie('countryDetails', base64_encode(serialize($country_data)), current_time( 'timestamp')+3600*24*.5, COOKIEPATH, COOKIE_DOMAIN, false);		
			}
			$_SESSION['siteVisitedCookieSet']=true;

			if(!isset($_COOKIE['siteVisited'])) {
		        setcookie('siteVisited', base64_encode($ip), current_time( 'timestamp')+3600*24*1, COOKIEPATH, COOKIE_DOMAIN, false);	
			}
			else {
				if(current_time( 'timestamp')>base64_decode($_COOKIE['siteVisited'])) {
					setcookie('siteVisited', base64_encode($ip), current_time( 'timestamp')+3600*24*1, COOKIEPATH, COOKIE_DOMAIN, false);	
				}				
			}

		}
		

	}//end of class
}//end of if class not exists

// Load the plugin
$trafficstats_plugin = new TrafficstatsPlugin();


function trafficstats_widget() {
	$stats = new Stats();
	$referrerStats = new RefferStats();
	$keywordStats = new KeywordStats();
	//global $trafficstats_plugin;
	global $wpdb;
	
	//to display page views and unique visitors graph
	$stats->display_page_views_graph_dashboard();

	//traffic increase/decrease data compared with previous month data
	$stats->traffic_change_data_dashboard();

	echo "<table width='100%'><tr><td style='border-bottom:4px solid #C4C4C4;padding-top:10px;' colspan='6'></td></tr><tr><td width='46%' valign='top'><table  width='100%'><tr><td colspan='2' width='100%' style='line-height:30px;color:#333333'><strong>Top Content Viewed</strong></td></tr>";
	
	$Results = get_total_visitors();


	$total_visitors = $Results[0]->total_visitors;


	$Results		=	get_page_visitors_data();
	$i=1;
	foreach($Results as $result)
	{
		$post_data = get_post($result->stat_post_id);
		
		$post_url = get_permalink($result->stat_post_id);

		$percent_viewed = ($result->visitors * 100)/$total_visitors;


		if(strlen($post_data->post_title)>28) $post_title = substr_replace($post_data->post_title,'...',27); else $post_title = $post_data->post_title;
		
		if($result->stat_post_id != "-1")
		{
			echo "<tr><td width='7%' style='line-height:15px'>".$i.".</td><td><a href='$post_url' target='_blank' title='".$post_data->post_title."'>".$post_title."</a></td></tr>";
		}
		else
		{
			echo "<tr><td width='7%' style='line-height:15px'>".$i.".</td><td><a href='".site_url()."' target='_blank' title='/'>/</a></td></tr>";
		}
		$i++;
	}

	echo "</table></td>";

	echo "<td width='2%'></td><td style='border-right:1px solid #c4c4c4;padding-right:4px'></td><td width='2%'></td><td valign='top'>";
	
	//display referral data on dashboard
	$duration = "";
	$referrerStats->display_refrral_data($duration);

	echo "</td></tr><tr><td style='border-bottom:1px solid #c4c4c4;padding-top:10px;' colspan='6'></td></tr><tr><td colspan='6'>";

	//display keyword data on dashboard
	$keywordStats->display_keyword_data();

	echo "</td></tr></table>";

	echo "<table width='100%'><tr><td align='right' style='padding-top:7px'><a href='admin.php?page=".TRACK_PLUGIN_NAME."' class='button-secondary' target='_blank'>All Stats</td></tr></table>";
	
}

//This function is used to get total visitors
function get_total_visitors()
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

	$query	=	"select count(stat_visitor_id) as total_visitors
				 from ".$wpdb->prefix."tts_trafficstats where stat_visitor_id not in(".$list_ips.") ";

	$Results =	$wpdb->get_results($query, OBJECT);
	return $Results;
}

//This function is used to get each post/page data with limit 6
function get_page_visitors_data()
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

	$query	=	"select stat_post_id,count(stat_visitor_id) as unique_visitors,
				count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 and stat_visitor_id not in(".$list_ips.") group by stat_post_id order by visitors desc limit 6";

	$Results		=	$wpdb->get_results($query, OBJECT);
	return $Results;
}

//This function is used to get each post/page data
function get_page_visitors_records()
{
	global $wpdb;
	$query	=	"select stat_post_id,count(stat_visitor_id) as unique_visitors,
				count(stat_visitor_id) as visitors from ".$wpdb->prefix."tts_trafficstats where stat_post_id!=0 group by stat_post_id order by visitors desc";

	$Results		=	$wpdb->get_results($query, OBJECT);
	return $Results;
}

	/*
 * Write stats to database
 */
function write_trackstats($post_id) {
	global $wpdb;
	$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_trafficstats VALUES (\"".$post_id."\", now(), \"".get_visitor_id()."\", \"".get_visit_id()."\",0)");
}

//This function returns total page Views/Visitors
function get_total_page_visitors($duration) {
	global $wpdb;
	if($duration == "today") {
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')='".strftime('%Y-%m-%d',current_time( 'timestamp'))."'";
	}
	if($duration == "yesterday") {
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where DATE_FORMAT(stat_date,'%Y-%m-%d')=DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 1 DAY)";
	}
	if($duration == "7days")
	{
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 6 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'";
	}
	if($duration == "14days")
	{
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 13 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'";
	}
	if($duration == "30days")
	{
		$query = "select count(stat_visitor_id) as total_visitors from ".$wpdb->prefix."tts_trafficstats where  DATE_FORMAT(stat_date,'%Y-%m-%d') between DATE_SUB('".strftime('%Y-%m-%d',current_time( 'timestamp'))."',INTERVAL 29 DAY) and '".strftime('%Y-%m-%d',current_time( 'timestamp'))."'";
	}
	else
	{

	}
	$Results = $wpdb->get_results($query, OBJECT);
	return $Results;
}


/*
 * Get visitor id from cookie or generate new and write to cookie
 */
function get_visitor_id() {
	$visitor_id = $_SERVER['REMOTE_ADDR'];
	return $visitor_id;
}
function get_visit_id() {
	
			if(empty($_COOKIE['visitor_id'])) {
				$visitor_id = substr(md5(sha1(crc32(md5(base64_decode(microtime())).microtime()))), 0, 32);
			} else {
				$visitor_id = $_COOKIE['visitor_id'];
			}
			
			return $visitor_id;
		}
//This function searches the keyword which the user searches and after search 
	//search engine like google and yahoo redirects on our site 
	function search_keyword($referrer) {

		$key = 0;
	  	$search_term = '';

		
		if(strpos($_SERVER['HTTP_REFERER'],site_url())!== false) {
			$search_term = urldecode($search_term);
			return $search_term;			
		}

		// used by dogpile, excite, webcrawler, metacrawler
		if (strpos($referrer, '/search/web/') !== false) $key = strpos($referrer, '/search/web/') + 12;

		// used by chubba
		if (strpos($referrer, 'arg=') !== false) $key = strpos($referrer, 'arg=') + 4;

		// used by dmoz
		if (strpos($referrer, 'search=') !== false) $key = strpos($referrer, 'query=') + 7;

		// used by looksmart
		if (strpos($referrer, 'qt=') !== false) $key = strpos($referrer, 'qt=') + 3;

		// used by scrub the web
		if (strpos($referrer, 'keyword=') !== false) $key = strpos($referrer, 'keyword=') + 8;

		// used by overture, hogsearch
		if (strpos($referrer, 'keywords=') !== false) $key = strpos($referrer, 'keywords=') + 9;

		// used by mamma, lycos, kanoodle, snap, whatuseek
		if (strpos($referrer, 'query=') !== false) $key = strpos($referrer, 'query=') + 6;

		// don't allow encrypted key words by aol
		if (strpos($referrer, 'encquery=') !== false) $key = 0;

		// used by ixquick
		if (strpos($referrer, '&query=') !== false) $key = strpos($referrer, '&query=') + 7;

		// used by aol
		if (strpos($referrer, 'qry=') !== false) $key = strpos($referrer, 'qry=') + 4;

		// used by yahoo, hotbot
		if (strpos($referrer, 'p=') !== false) $key = strpos($referrer, 'p=') + 2;

		// used by google, msn, alta vista, ask jeeves, all the web, teoma, wisenut, search.com
		if (strpos($referrer, 'q=') !==  false) $key = strpos($referrer, 'q=') + 2;
		if ($key > 0)
		{
			if (strpos($referrer, '&', $key) !== false)
			{
				$search_term = substr($referrer, $key, (strpos($referrer, '&', $key) - $key));
			}
			elseif (strpos($referrer, '/search/web/') !== false)
			{
				if (strpos($referrer, '/', $key) !== false)
				{
					$search_term = urldecode(substr($referrer, $key, (strpos($referrer, '/', $key) - $key)));
		        }
				else
		        {
		        	$search_term = urldecode(substr($referrer, $key));
		        }
		    }
		    else
		    {
		    	$search_term = substr($referrer, $key);
		    }
		} //end of if
		if($search_term=='') {
			if(strpos($referrer, 'sa=') !== false && strpos($_SERVER['HTTP_REFERER'], 'google') !== false) {
				$search_term="No Search Term (SSL)";
			}
		}
		$search_term = urldecode($search_term);
		return $search_term;

	}//end of function 
//} //end of http_referrer if


//This function is used to insert referrals that are coming for external sites like google,yahoo
//into the database
function insert_referral_data($referrer,$post_id,$ip)
{
	global $wpdb;
	$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_referrer_stats SET referrer=\"".$referrer."\", create_time = now(),`post_id`=".$post_id.", stat_visitor_id='".$ip."', visitor_id='".get_visit_id()."'");
	return $wpdb->insert_id;
}

//This function is used to insert keywords that the user uses to search on search engines
function insert_keyword_data($keyword,$post_id,$ip)
{
	global $wpdb;
	$wpdb->query("INSERT INTO ".$wpdb->prefix."tts_keyword_stats SET keyword=\"".$keyword."\", create_time = now(),`post_id`=".$post_id.", stat_visitor_id='".$ip."',visitor_id='".get_visit_id()."'");
}

// This function is used to fetch browser and operating system information
function fetch_browser_os_info()
{
	include("uadetector.class.php");

	$info=array('browser'=>'','platform'=>'');
	$browser="";
	$ua = new UADetector();
	if ($ua->agenttype == "B") {
		$browser = $ua->name;
		if (!empty($ua->version)) { 
			$browser .= " ".browserVersion($ua->version);
			if (strstr($ua->version,"Mobile")!==false) {
				$browser .= " Mobile";
			}
		}

	} else {
		$spider = $ua->name;
		if ($ua->agenttype == "F") {
			if (!empty($ua->subscribers)) {
				$browser = $ua->subscribers;
			} else {
				$browser = $spider;
			}
		} elseif ($ua->agenttype == "H" ) {	//it's a script injection bot|spammer
			$browser = "Hacker";
		}
		 elseif ($ua->agenttype == "S" ) {	//it's a script injection bot|spammer
			$browser = "Spammer/email harvester";
		}
		 elseif ($ua->agenttype == "M" ) {	//it's a script injection bot|spammer
			$browser = "siteMap generator";
		}
	} //end else agenttype
		

	$agent = (!empty($browser)?$browser:$spider);
	$info['browser'] = $agent;  
	$info['platform'] = $ua->os;
	
	$path = WP_PLUGIN_DIR."/".TRACK_PLUGIN_NAME."/";
		/*$f = fopen($path.'bots_new.txt' , 'a+');
		fwrite($f , serialize(trackBot()).'\n' );
		fclose($f);
R=robot, B=Browser/downloader, F=feedreader, H=hacker, L=Link checker, M=siteMap generator, S=Spammer/email harvester, V=CSS/Html validator		
		*/

	if($info['browser']=='' || empty($info['browser']) || $ua->agenttype == "R") {
		$bot_data=trackBot();
		if(isset($bot_data[0]) && $bot_data[0]!='') {
			$info['browser']=$bot_data[0];
			if(isset($bot_data[1]) && $bot_data[1]!='') {
				switch ($bot_data[1]) {
					case 'R':
						$info['browser'] = $info['browser'].'(Robot)';
						break;
					case 'B':
						$info['browser'] = $info['browser'].'(Browser/downloader)';
						break;
					case 'F':
						$info['browser'] = $info['browser'].'(feedreader)';
						break;
					case 'H':
						$info['browser'] = $info['browser'].'(hacker)';
						break;
					case 'L':
						$info['browser'] = $info['browser'].'(Link checker)';
						break;
					case 'M':
						$info['browser'] = $info['browser'].'(siteMap generator)';
						break;
					case 'S':
						$info['browser'] = $info['browser'].'(Spammer/email harvester)';
						break;
					case 'V':
						$info['browser'] = $info['browser'].'(CSS/Html validator)';
						break;
					
				}
			}


		}
	}

	if($info['browser']=='') {
		$info['browser']=$_SERVER['HTTP_USER_AGENT'];

	}



	if($info['platform']=='' || empty($info['platform']) || $info['platform']=='unknown') {
		$info['platform']=$ua->platform;

		if($info['platform']=='' || empty($info['platform']) || $info['platform']=='unknown') {

			$info['platform']=$_SERVER['HTTP_USER_AGENT'];

			if (strlen($info['platform']) > 255) {
				$info['platform']=substr(str_replace(array('  ','%20%20','++'),array(' ','%20','+'),$info['platform']),0,255);
			}
		}
	}

	if($ua->agenttype == "R" || (isset($bot_data) && isset($bot_data[0]) && $bot_data[0]!='')) {
		
		if($ua->platform!='') {
			$info['platform']=$ua->platform;
		}
	}

	return ($info);
}

function trackBot($agent="",$hostname="", $browser="")
{

	$hostname=@gethostbyaddr(get_visitor_id());
    $agent = (isset($_SERVER['HTTP_USER_AGENT']) ? rtrim($_SERVER['HTTP_USER_AGENT']) : '');
	if (strlen($agent) > 255) {
		$agent=substr(str_replace(array('  ','%20%20','++'),array(' ','%20','+'),$agent),0,255);
	}

	if (empty($agent)) { $agent = $_SERVER['HTTP_USER_AGENT']; }
	$ua = rtrim($agent);
	if (empty($ua)) {	//nothing to do...
		return false;
	} 

	$spiderdata=false;
	$crawler = "";
	$feed = "";
	$os = "";
	//## Identify obvious script injection bots 
	if (stristr($ua,'location.href')!==FALSE) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/(<|&lt;|&#60;)a(\s|%20|&#32;|\+)href/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/(<|&lt;|&#60;)script/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";
	} elseif (preg_match('/select.*(\s|%20|\+|%#32;)from(\s|%20|\+|%#32;)wp_/i',$ua)>0) {
		$crawlertype = "H";
		$crawler = "Script Injection bot";

	//## check for crawlers that identify themselves clearly in their
	//#  user agent string with words like bot, spider, and crawler
	} elseif (!empty($ua) && preg_match("#(\w+[ \-_]?(bot|spider|crawler|reader|seeker))[0-9/ -:_.;\)]#",$ua,$matches) > 0) {
		$crawler = $matches[1];
		$crawlertype="R";

	} elseif (!empty($hostname)) {
		//## check for crawlers that mis-identify themselves as a 
		//#  browser but come from a known crawler domain - the 
		//#  most common of these are MSN (ie6,win2k3), and Yahoo!
		if (substr($hostname,-16) == ".crawl.yahoo.net" || (substr($hostname,-10)==".yahoo.com" && substr($hostname,0,3)=="ycar")) {
			if (stristr($ua,"Slurp")) {
				$crawler = "Yahoo! Slurp";
				$crawlertype="R";
			} elseif (stristr($ua,"mobile")) {
				$crawler = "Yahoo! Mobile";
				$crawlertype="R";
			} else {
				$crawler = "Yahoo!";
				$crawlertype="R";
			}
		} elseif (substr($_SERVER["REMOTE_ADDR"],0,6) == "65.55." || substr($_SERVER["REMOTE_ADDR"],0,7) == "207.46.") {
			$crawler = "MSNBot";
			$crawlertype="R";
		} elseif (substr($hostname,-8) == ".msn.com" && strpos($hostname,"msnbot")!== FALSE) {
			$crawler = "MSNBot";
			$crawlertype="R";

		//googlebot mobile can show as browser, sometimes
		} elseif (substr($hostname,-14) == ".googlebot.com") {
			if (stristr($ua,"mobile")) {
				$crawler = "Googlebot-Mobile";
				$crawlertype="R";
			} else {
				$crawler = "Googlebot";
				$crawlertype="R";
			}

		//} elseif (!empty($browser) && preg_match("#([a-z0-9_]*(bot|crawl|reader|seeker|spider\.|feed|indexer|parser))]#",$ua,$matches) > 0) {
		//## TODO: check for crawlers that claim to be browsers but
		//#  their hostname says otherwise
		}
	}

	if (empty($crawler) && ini_get("browscap") != "" ) {
		//## check browscap data for crawler info., when available
		$browsercap = get_browser($ua,true);
		//if no platform(os), assume crawler...
		if (!empty($browsercap['platform'])) {
			if ( $browsercap['platform'] != "unknown") {
				$os = $browsercap['platform'];
			}
		}
		if (!empty($browsercap['crawler']) || !empty($browsercap['stripper']) || $os == "") {
			if (!empty($browsercap['browser'])) {
				$crawler = $browsercap['browser'];
			} elseif (!empty($browsercap['parent'])) {
				$crawler = $browsercap['parent'];
			}
			if (!empty($crawler) && !empty($browsercap['version'])) {
				$crawler = $crawler." ".$browsercap['version'];
			}
		}
		//reject unknown browscap crawlers (ex: default)
		if (preg_match('/^(default|unknown|robot)/i',$crawler) > 0) {
			$crawler = "";
		}
	}

	//get crawler info. from a known list of bots and feedreaders that
	// don't list their names first in UA string.
	//Note: spaces are removed from UA string for the bot comparison
	$crawler = trim($crawler);
	if (empty($crawler)) {
		$uagent=str_replace(" ","",$ua);
		$key = null;
		// array format: "Spider Name|UserAgent keywords (no spaces)| Spider type (R=robot, B=Browser/downloader, F=feedreader, H=hacker, L=Link checker, M=siteMap generator, S=Spammer/email harvester, V=CSS/Html validator)
		$lines = array("Googlebot|Googlebot/|R|", 
			"Yahoo!|Yahoo! Slurp|R|",
			"FeedBurner|FeedBurner|F|",
			"AboutUsBot|AboutUsBot/|R|", 
			"80bot|80legs.com|R|", 
			"Aggrevator|Aggrevator/|F|", 
			"AlestiFeedBot|AlestiFeedBot||", 
			"Alexa|ia_archiver|R|", "AltaVista|Scooter-|R|", 
			"AltaVista|Scooter/|R|", "AltaVista|Scooter_|R|", 
			"AMZNKAssocBot|AMZNKAssocBot/|R|",
			"AppleSyndication|AppleSyndication/|F|",
			"Apple-PubSub|Apple-PubSub/|F|",
			"Ask.com/Teoma|AskJeeves/Teoma)|R|",
			"Ask Jeeves/Teoma|ask.com|R|",
			"AskJeeves|AskJeeves|R|", 
			"Baiduspider|www.baidu.com/search/spider|R|",
			"BlogBot|BlogBot/|F|", "Bloglines|Bloglines/|F|",
			"Blogslive|Blogslive|F|",
			"BlogsNowBot|BlogsNowBot|F|",
			"BlogPulseLive|BlogPulseLive|F|",
			"IceRocket BlogSearch|icerocket.com|F|",
			"Charlotte|Charlotte/|R|", 
			"Xyleme|cosmos/0.|R|", "cURL|curl/|R|",
			"Daumoa|Daumoa-feedfetcher|F|",
			"Daumoa|DAUMOA|R|",
			"Daumoa|.daum.net|R|",
			"Die|die-kraehe.de|R|", 
			"Diggit!|Digger/|R|", 
			"disco/Nutch|disco/Nutch|R|",
			"DotBot|DotBot/|R|",
			"Emacs-w3|Emacs-w3/v||", 
			"ananzi|EMC||", 
			"EnaBot|EnaBot||", 
			"esculapio|esculapio/||", "Esther|esther||", 
			"everyfeed-spider|everyfeed-spider|F|", 
			"Evliya|Evliya||", "nzexplorer|explorersearch||", 
			"eZ publish Validator|eZpublishLinkValidator||",
			"FacebookExternalHit|facebook.com/externalhit|R|",
			"FastCrawler|FastCrawler|R|", 
			"FDSE|FDSErobot|R|", 
			"Feed::Find|Feed::Find||",
			"FeedDemon|FeedDemon/|F|",
			"FeedHub FeedFetcher|FeedHub|F|", 
			"Feedreader|Feedreader|F|", 
			"Feedshow|Feedshow|F|", 
			"Feedster|Feedster|F|",
			"FeedTools|feedtools|F|",
			"Feedfetcher-Google|Feedfetcher-google|F|", 
			"Felix|FelixIDE/||", 
			"FetchRover|ESIRover||", 
			"fido|fido/||", 
			"Fish|Fish-Search-Robot||", "Fouineur|Fouineur||", 
			"Freecrawl|Freecrawl|R|", 
			"FriendFeedBot|FriendFeedBot/|F|",
			"FunnelWeb|FunnelWeb-||", 
			"gammaSpider|gammaSpider||", "gazz|gazz/||", 
			"GCreep|gcreep/||", 
			"GetRight|GetRight|R|", 
			"GetURL|GetURL.re||", "Golem|Golem/||", 
			"Google Images|Googlebot-Image|R|",
			"Google AdSense|Mediapartners-Google|R|", 
			"Google Desktop|GoogleDesktop|F|", 
			"Google Web Preview|GoogleWebPreview|R|",
			"GreatNews|GreatNews|F|", 
			"Gregarius|Gregarius/|F|",
			"Gromit|Gromit/||", 
			"gsinfobot|gsinfobot||", 
			"Gulliver|Gulliver/||", "Gulper|Gulper||", 
			"GurujiBot|GurujiBot||", 
			"havIndex|havIndex/||",
			"heritrix|heritrix/||", "HI|AITCSRobot/||",
			"HKU|HKU||", "Hometown|Hometown||", 
			"HostTracker|host-tracker.com/|R|",
			"ht://Dig|htdig/|R|", "HTMLgobble|HTMLgobble||", 
			"Hyper-Decontextualizer|Hyper||", 
			"iajaBot|iajaBot/||", 
			"IBM_Planetwide|IBM_Planetwide,||", 
			"ichiro|ichiro||", 
			"Popular|gestaltIconoclast/||", 
			"Ingrid|INGRID/||", "Imagelock|Imagelock||", 
			"IncyWincy|IncyWincy/||", 
			"Informant|Informant||", 
			"InfoSeek|InfoSeek||", 
			"InfoSpiders|InfoSpiders/||", 
			"Inspector|inspectorwww/||", 
			"IntelliAgent|IAGENT/||", 
			"ISC Systems iRc Search|ISCSystemsiRcSearch||", 
			"Israeli-search|IsraeliSearch/||", 
			"IRLIRLbot/|IRLIRLbot||",
			"Italian Blog Rankings|blogbabel|F|", 
			"Jakarta|Jakarta||", "Java|Java/||", 
			"JBot|JBot||", 
			"JCrawler|JCrawler/||", 
			"JoBo|JoBo||", "Jobot|Jobot/||", 
			"JoeBot|JoeBot/||",
			"JumpStation|jumpstation||", 
			"image.kapsi.net|image.kapsi.net/|R|", 
			"kalooga/kalooga|kalooga/kalooga||", 
			"Katipo|Katipo/||", 
			"KDD-Explorer|KDD-Explorer/||", 
			"KIT-Fireball|KIT-Fireball/||", 
			"KindOpener|KindOpener||", "kinjabot|kinjabot||", 
			"KO_Yappo_Robot|yappo.com/info/robot.html||", 
			"Krugle|Krugle||", 
			"LabelGrabber|LabelGrab/||",
			"Larbin|larbin_||",
			"libwww-perl|libwww-perl||", 
			"lilina|Lilina||", 
			"Link|Linkidator/||", "LinkWalker|LinkWalker|L|", 
			"LiteFinder|LiteFinder||", 
			"logo.gif|logo.gif||", "LookSmart|grub-client||",
			"Lsearch/sondeur|Lsearch/sondeur||", 
			"Lycos|Lycos/||", 
			"Magpie|Magpie/||", "MagpieRSS|MagpieRSS|F|", 
			"Mail.ru|Mail.ru||", 
			"marvin/infoseek|marvin/infoseek||", 
			"Mattie|M/3.||", "MediaFox|MediaFox/||", 
			"Megite2.0|Megite.com||", 
			"NEC-MeshExplorer|NEC-MeshExplorer||", 
			"MindCrawler|MindCrawler||", 
			"Missigua Locator|Missigua Locator||", 
			"MJ12bot|MJ12bot|R|", "mnoGoSearch|UdmSearch||", 
			"MOMspider|MOMspider/||", "Monster|Monster/v||", 
			"Moreover|Moreoverbot||", "Motor|Motor/||", 
			"MSNBot|MSNBOT/|R|", "MSN|msnbot.|R|",
			"MSRBOT|MSRBOT|R|", "Muninn|Muninn/||", 
			"Muscat|MuscatFerret/||", 
			"Mwd.Search|MwdSearch/||", 
			"MyBlogLog|Yahoo!MyBlogLogAPIClient|F|",
			"Naver|NaverBot||",
			"Naver|Cowbot||",
			"NDSpider|NDSpider/||", 
			"Nederland.zoek|Nederland.zoek||", 
			"NetCarta|NetCarta||", 
			"NetMechanic|NetMechanic||", 
			"NetScoop|NetScoop/||", 
			"NetNewsWire|NetNewsWire||", 
			"NewsAlloy|NewsAlloy||",
			"newscan-online|newscan-online/||", 
			"NewsGatorOnline|NewsGatorOnline||", 
			"Exalead NG|NG/|R|", 
			"NHSE|NHSEWalker/||", "Nomad|Nomad-V||", 
			"Nutch/Nutch|Nutch/Nutch||", 
			"ObjectsSearch|ObjectsSearch/||", 
			"Occam|Occam/||", 
			"Openfind|Openfind||", 
			"OpiDig|OpiDig||", 
			"Orb|Orbsearch/||", 
			"OSSE Scanner|OSSEScanner||", 
			"OWPBot|OWPBot||", 
			"Pack|PackRat/||", "ParaSite|ParaSite/||", 
			"Patric|Patric/||", 
			"PECL::HTTP|PECL::HTTP||", 
			"PerlCrawler|PerlCrawler/||", 
			"Phantom|Duppies||", "PhpDig|phpdig/||", 
			"PiltdownMan|PiltdownMan/||", 
			"Pimptrain.com's|Pimptrain||", 
			"Pioneer|Pioneer||", 
			"Portal|PortalJuice.com/||", "PGP|PGP-KA/||", 
			"PlumtreeWebAccessor|PlumtreeWebAccessor/||", 
			"Poppi|Poppi/||", "PortalB|PortalBSpider/||", 
			"psbot|psbot/|R|", 
			"R6_CommentReade|R6_CommentReade||", 
			"R6_FeedFetcher|R6_FeedFetcher|F|", 
			"radianrss|RadianRSS||", 
			"Raven|Raven-v||", 
			"relevantNOISE|relevantnoise.com||",
			"Resume|Resume||", "RoadHouse|RHCS/||", 
			"RixBot|RixBot||",
			"Robbie|Robbie/||", "RoboCrawl|RoboCrawl||", 
			"RoboFox|Robofox||",
			"Robozilla|Robozilla/||", 
			"Rojo|rojo1|F|", 
			"Roverbot|Roverbot||", 
			"RssBandit|RssBandit||", 
			"RSSMicro|RSSMicro.com|F|",
			"Ruby|Rfeedfinder||", 
			"RuLeS|RuLeS/||", 
			"Runnk RSS aggregator|Runnk||", 
			"SafetyNet|SafetyNet||", 
			"Sage|(Sage)|F|",
			"SBIder|sitesell.com|R|", 
			"Scooter|Scooter/||", 
			"ScoutJet|ScoutJet||",
			"SearchProcess|searchprocess/||", 
			"Seekbot|seekbot.net|R|", 
			"SimplePie|SimplePie/|F|", 
			"Sitemap Generator|SitemapGenerator||", 
			"Senrigan|Senrigan/||", 
			"SeznamBot|SeznamBot/|R|",
			"SeznamScreenshotator|SeznamScreenshotator/|R|",
			"SG-Scout|SG-Scout||", "Shai'Hulud|Shai'Hulud||", 
			"Simmany|SimBot/||", 
			"SiteTech-Rover|SiteTech-Rover||", 
			"shelob|shelob||", 
			"Sleek|Sleek||", 
			"Slurp|.inktomi.com/slurp.html|R|",
			"Snapbot|.snap.com|R|", 
			"SnapPreviewBot|SnapPreviewBot|R|",
			"Smart|ESISmartSpider/||", 
			"Snooper|Snooper/b97_01||", "Solbot|Solbot/||", 
			"Sphere Scout|SphereScout|R|",
			"Sphere|sphere.com|R|",
			"spider_monkey|mouse.house/||",
			"SpiderBot|SpiderBot/||", 
			"Spiderline|spiderline/||",
			"SpiderView(tm)|SpiderView||", 
			"SragentRssCrawler|SragentRssCrawler|F|",
			"Site|ssearcher100||",
			"StackRambler|StackRambler||", 
			"Strategic Board Bot|StrategicBoardBot||", 
			"Suke|suke/||", 
			"SummizeFeedReader|SummizeFeedReader|F|", 
			"suntek|suntek/||", 
			"SurveyBot|SurveyBot||", 
			"Sygol|.sygol.com||", 
			"Syndic8|Syndic8|F|", 
			"TACH|TACH||", "Tarantula|Tarantula/||",
			"tarspider|tarspider||", "Tcl|dlw3robot/||", 
			"TechBOT|TechBOT||", "Technorati|Technoratibot||",
			"Teemer|Teemer||", "Templeton|Templeton/||",
			"TitIn|TitIn/||", "TITAN|TITAN/||", 
			"Twiceler|.cuil.com/twiceler/|R|",
			"Twiceler|.cuill.com/twiceler/|R|",
			"Twingly|twingly.com|R|",
			"UCSD|UCSD-Crawler||", "UdmSearch|UdmSearch/||",
			"UniversalFeedParser|UniversalFeedParser|F|", 
			"UptimeBot|uptimebot||", 
			"URL_Spider|URL_Spider_Pro/|R|", 
			"VadixBot|VadixBot||", "Valkyrie|Valkyrie/||", 
			"Verticrawl|Verticrawlbot||", 
			"Victoria|Victoria/||", 
			"vision-search|vision-search/||", 
			"void-bot|void-bot/||", "Voila|VoilaBot||",
			"Voyager|.kosmix.com/html/crawler|R|",
			"VWbot|VWbot_K/||", 
			"W3C_Validator|W3C_Validator/|V|",
			"w3m|w3m/|B|", "W3M2|W3M2/||", "w3mir|w3mir/||", 
			"w@pSpider|w@pSpider/||", 
			"WallPaper|CrawlPaper/||",
			"WebCatcher|WebCatcher/||", 
			"webCollage|webcollage/|R|", 
			"webCollage|collage.cgi/|R|", 
			"WebCopier|WebCopierv|R|",
			"WebFetch|WebFetch|R|", "WebFetch|webfetch/|R|", 
			"WebMirror|webmirror/||", 
			"webLyzard|webLyzard||", "Weblog|wlm-||", 
			"WebReaper|webreaper.net|R|", 
			"WebVac|webvac/||", "webwalk|webwalk||", 
			"WebWalker|WebWalker/||", 
			"WebWatch|WebWatch||", 
			"WebStolperer|WOLP/||", 
			"WebThumb|WebThumb/|R|", 
			"Wells Search II|WellsSearchII||", 
			"Wget|Wget/||",
			"whatUseek|whatUseek_winona/||", 
			"whiteiexpres/Nutch|whiteiexpres/Nutch||",
			"wikioblogs|wikioblogs||", 
			"WikioFeedBot|WikioFeedBot||", 
			"WikioPxyFeedBo|WikioPxyFeedBo||",
			"Wild|Hazel's||", 
			"Wired|wired-digital-newsbot/||", 
			"Wordpress Pingback/Trackback|Wordpress||", 
			"WWWC|WWWC/||", 
			"XGET|XGET/||", 
			"yacybot|yacybot||",
			"Yahoo FeedSeeker|YahooFeedSeeker|F|",
			"Yahoo MMAudVid|Yahoo-MMAudVid/|R|",
			"Yahoo MMCrawler|Yahoo-MMCrawler/|R|",
			"Yahoo!SearchMonkey|Yahoo!SearchMonkey|R|",
			"YahooSeeker|YahooSeeker/|R|",
			"YoudaoBot|YoudaoBot|R|", 
			"Tailrank|spinn3r.com/robot|R|",
			"Tailrank|tailrank.com/robot|R|",
			"Yandex|Yandex|R|",
			"Yesup|yesup||",
			"Internet|User-Agent:||",
			"Robot|Robot||", "Spider|spider||");
		foreach($lines as $line_num => $spider) {
			list($nome,$key,$crawlertype)=explode("|",$spider);
			if ($key != "") {
				if(strstr($uagent,$key)===FALSE) { 
					continue; 
				} else { 
					$crawler = trim($nome);
					if (!empty($crawlertype) && $crawlertype == "F") {
						$feed = $crawler;
					}
					break 1;
				}
			}
		}
	} // end if crawler

	//If crawler not on list, use first word in useragent for crawler name
	if (empty($crawler)) { 
		//Assume first word in useragent is crawler name
		if (preg_match("/^(\w+)[\/ \-\:_\.;]/",$ua,$matches) > 0) {
			if (strlen($matches[1])>1 && $matches[1]!="Mozilla") { 
				$crawler = $matches[1];
			}
		}
		/* //Use browser name for crawler as last resort
		if (empty($crawler) && !empty($browser)) { 
			$crawler = $browser;
		} */
	}
	//#do a feed check and get feed subcribers, if available
	if (preg_match("/([0-9]{1,10})\s?subscriber/i",$ua,$subscriber) > 0) {
		// It's a feedreader with some subscribers
		$feed = $subscriber[1];
		if (empty($crawler) && empty($browser)) {
			$crawler = "Feed Reader";
			$crawlertype="F";
		}
	} elseif (empty($feed) && (is_feed() || preg_match("/(feed|rss)/i",$ua)>0)) {
		if (!empty($crawler)) { 
			$feed = $crawler;
		} elseif (empty($browser)) {
			$crawler = "Feed Reader";
			$feed = "feed reader";
		}
		$crawlertype="F";
	} //end else preg_match subscriber

	//check for spoofers of Google/Yahoo crawlers...
	if ($hostname!="") {
		if (preg_match('/^(googlebot|yahoo\!\ slurp)/i',$crawler)>0 && preg_match('/\.(googlebot|yahoo)\./i',$hostname)==0){
			$crawler = "Spoofer bot";
			$crawlertype = "H";
		}
	} //end if hostname
	$spiderdata=array($crawler,$crawlertype,trim($feed));

	return $spiderdata;


}

function browserVersion($versionstring) {
	$version=0;
	if (!empty($versionstring)) {
		$n = strpos($versionstring,'.');
		if ($n >0) {
			$version= (int) substr($versionstring,0,$n);
		}
		if ($n == 0 || $version == 0) {
			$p = strpos($versionstring,'.',$n+1);
			if ($p) $version= substr($versionstring,0,$p);
		}
	}
	if ($version > 0) {
		return $version;
	} else {
		return $versionstring;
	}
}

function load_trackthatstat_scripts() {

    //wp_deregister_script( 'jquery' );
	//wp_register_script( 'jquery', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/jquery.min.js');
    wp_enqueue_script( 'jquery', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/jquery.min.js');

   // wp_register_script( 'highcharts', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/highcharts.js');
    wp_enqueue_script( 'highcharts' , WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/highcharts.js');

	//wp_register_script( 'exporting', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/modules/exporting.js');
    wp_enqueue_script( 'exporting',WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/modules/exporting.js' );

}

function load_trackthatstat_admin_scripts() {
	
	//wp_deregister_script( 'jquery' );
    //wp_register_script( 'jquery', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/jquery.min.js');
    wp_enqueue_script( 'jquery', WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/jquery.min.js');

    wp_enqueue_script( 'highcharts' , WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/highcharts.js');
    wp_enqueue_script( 'exporting',WP_PLUGIN_URL.'/'.TRACK_PLUGIN_NAME.'/js/modules/exporting.js' );
	wp_enqueue_script( 'highslide','http://www.highcharts.com/highslide/highslide-full.min.js' );
	wp_enqueue_script( 'highslide_config','http://www.highcharts.com/highslide/highslide.config.js' );
}

function setPageLimitCookie($limit,$name)
{
	 setcookie($name, $limit, current_time( 'timestamp')+3600*24*.5, COOKIEPATH, COOKIE_DOMAIN, false);		
	
}

function get_paging_array()
{
	return (array(10, 25, 50, 100, 250));
}

function getUserGeoInfo($ip)
{
	
	$user_geo_info = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=938c415de230116a21b1dacf1fbf181d2a7c330e1fa69cb6c3b48b751c77860a&ip=$ip&format=json");
	$country_data =  json_decode($user_geo_info);

	if(count($country_data)==0 || empty($country_data->countryName) || empty($country_data) || $country_data->countryName=='-') {
		$user_geo_info = file_get_contents("http://api.hostip.info/get_json.php?ip=$ip");
		$country_hostip_data =  json_decode($user_geo_info);
		$country_data=(object)array('countryName'=>$country_hostip_data->country_name);
	}

	if (strstr($country_data->countryName,"Unknown")!==false) {
		$country_data->countryName='-';
	}

	if (strstr($country_data->countryName,"Private")!==false) {
		$country_data->countryName='-';
	}

	return ($country_data);
}
?>