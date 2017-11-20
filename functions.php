<?php

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) exit;

// Actions
// ------



if (!function_exists('OSW3_SessionStart'))
{
    function OSW3_SessionStart()
    {
        if (empty(session_id()))
        {
            session_start();
        }
    }
} 

if (!function_exists('OSW3_SiteLocker'))
{
    function OSW3_SiteLocker()
    {
        if ('on' === strtolower(OSW3_SiteLocker_getOption('is_locked')))
        {
            // Unlocker form treatment
            if ('POST' == $_SERVER['REQUEST_METHOD'])
            {
                if (isset($_POST['sitelocker-token']) && wp_verify_nonce($_POST['sitelocker-token'], 'sitelocker'))
                {
                    $expires = OSW3_SiteLocker_getOption('expires');
                    $hash    = OSW3_SiteLocker_getOption('password');
                    $plain_password = $_POST['sitelocker-password'];
                    
                    if (password_verify($plain_password, $hash))
                    {
                        setcookie('sitelocker', 'off', time() + $expires, "/");
                    }

                    wp_safe_redirect( wp_get_referer() );
                    exit;
                }
            }

            // Show the unlocker form
            if (empty($_COOKIE['sitelocker']) && !is_admin())
            {
                include_once plugin_dir_path( __FILE__ )."views/form.php";
                exit;
            }
        }
    }
} 

if (!function_exists('OSW3_SiteLocker_getOption'))
{
    function OSW3_SiteLocker_getOption( $name )
    {
        $options = get_option("settings_sitelocker");
        return $options[$name];
    }
} 