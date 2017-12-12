<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
    exit;
}


/**
 * Action
 * --
 * 1) Ckeck the validity of the unlock form submission
 * 2) Show the lock screen
 */
if (!function_exists('SimpleSiteLocker'))
{
    function SimpleSiteLocker()
    {
        if ('on' === strtolower(SimpleSiteLocker_getOption('is_locked')))
        {
            // Unlocker form treatment
            if ('POST' == $_SERVER['REQUEST_METHOD'])
            {
                if (isset($_POST['sitelocker-token']) && wp_verify_nonce($_POST['sitelocker-token'], 'sitelocker'))
                {
                    $expires = SimpleSiteLocker_getOption('expires');
                    $hash    = SimpleSiteLocker_getOption('password');
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

            if ('GET' == $_SERVER['REQUEST_METHOD'])
            {
                if (isset($_GET['sitelocker']) && "lock" === $_GET['sitelocker'])
                {
                    if (isset($_COOKIE['sitelocker']))
                    {
                        setcookie('sitelocker', null, time() - 1, "/");
                        if ( wp_redirect( home_url() ) ) {
                            exit;
                        }
                    }
                    // var_dump( $_COOKIE['sitelocker'] );

                }
            }

            // Show the unlocker form
            if (!isset($_COOKIE['sitelocker']) && !is_admin())
            {
                $theme_root = trailingslashit(get_template_directory());
                $lock_screen = SimpleSiteLocker_getOption('lock_screen');

                // If the lock-screen file exists for the current theme
                if (file_exists($theme_root.$lock_screen))
                {
                    include_once $theme_root.$lock_screen;
                }
                
                // If the lock-screen file does not exists for the current theme
                else
                {
                    do_shortcode('[SimpleSiteLocker_UnlockForm]');
                }

                exit;
            }
            else 
            {
                include_once plugin_dir_path( __DIR__ )."views/unlocked.php";
            }
        }
    }
} 