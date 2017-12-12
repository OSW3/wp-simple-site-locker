<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
    exit;
}



/**
 * Shortcode
 * --
 * Include the Unlcoker form
 * usage: do_shortcode('[SimpleSiteLocker_UnlockForm]');
 */
if (!function_exists('SimpleSiteLocker_UnlockForm'))
{
    function SimpleSiteLocker_UnlockForm()
    {
        include_once plugin_dir_path( __DIR__ )."views/form.php";
    }
} 