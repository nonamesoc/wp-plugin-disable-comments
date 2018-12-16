<?php
/*
Plugin Name: Disable comments by Noname
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Disable and delete comments on the site
Version: 1.0.0
Author: Noname
Author URI: http://URI_Of_The_Plugin_Author
License: Proprietary
*/

define( 'DIS_COM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DIS_COM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIS_COM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once ( DIS_COM_PLUGIN_DIR . "disable-comments-settings.php");
require_once ( DIS_COM_PLUGIN_DIR . "disable-comments-function.php");
require_once ( DIS_COM_PLUGIN_DIR . "delete-comments-function.php");
?>