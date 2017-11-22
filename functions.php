<?php

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) exit;


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

/**
 * Action
 * --
 * 1) Ckeck the validity of the unlock form submission
 * 2) Show the lock screen
 */
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

                    if (wp_get_referer())
                        wp_safe_redirect( wp_get_referer() );
                    else 
                        wp_redirect( home_url() );

                    exit;
                }
            }

            // Show the unlocker form
            if (empty($_COOKIE['sitelocker']) && !is_admin())
            {
                $theme_root = trailingslashit(get_template_directory());
                $lock_screen = OSW3_SiteLocker_getOption('lock_screen');

                // If the lock-screen file exists for the current theme
                if (file_exists($theme_root.$lock_screen))
                    include_once $theme_root.$lock_screen;
                
                // If the lock-screen file does not exists for the current theme
                else
                    do_shortcode('[OSW3_SiteLocker_UnlockForm]');

                exit;
            }
        }
    }
} 
if (!function_exists('OSW3_SiteLocker_getOption'))
{
    function OSW3_SiteLocker_getOption( $name )
    {
        $options = get_option("simple_site_locker");
        return $options[$name];
    }
} 


/**
 * Shortcode
 * --
 * Include the Unlcoker form
 * usage: do_shortcode('[OSW3_SiteLocker_UnlockForm]');
 */
if (!function_exists('OSW3_SiteLocker_UnlockForm'))
{
    function OSW3_SiteLocker_UnlockForm()
    {
        include_once plugin_dir_path( __FILE__ )."views/form.php";
    }
} 