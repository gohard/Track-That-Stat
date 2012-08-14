<?php
error_reporting(E_ALL || ~E_NOTICE || ~E_WARNING);

include("../../../wp-config.php");
setcookie('home_visited', '', -3600, COOKIEPATH, COOKIE_DOMAIN, false);	
setcookie('current_post_id', '', -3600, COOKIEPATH, COOKIE_DOMAIN, false);	

if(isset($_POST['home'])) {	
	setcookie('home_visited', base64_encode("home"), current_time( 'timestamp')+3600*24*.5, COOKIEPATH, COOKIE_DOMAIN, false);	

}

if(isset($_POST['current_post_id'])) {
	setcookie('current_post_id', $_POST['current_post_id'], current_time( 'timestamp')+3600*24*.5, COOKIEPATH, COOKIE_DOMAIN, false);	
}
?>