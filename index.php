<?php
/**
 * Plugin Name: Simple Site Locker
 * Plugin URI: http://osw3.net/wordpress/plugins/simple-site-locker
 * Description: A simple website locker
 * Version: 0.1
 * Author: OSW3
 * Author URI: http://osw3.net/
 * License: OSW3
 * Text Domain: simple_site_locker
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
    exit;
}

require_once(__DIR__.'/ppm/index.php');