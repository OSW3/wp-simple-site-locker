<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
    exit;
}


/**
 * Util
 * --
 * Session Start
 */
if (!function_exists('WPPPM_SessionStart'))
{
    function WPPPM_SessionStart()
    {
        if (empty(session_id())) session_start();
    }
} 


/**
 * Util
 * --
 * Options Getter
 */
if (!function_exists('SimpleSiteLocker_getOption'))
{
    function SimpleSiteLocker_getOption( $param )
    {
        $options = get_option( "simple_site_locker" );
        return $options[ $param ];
    }
} 
