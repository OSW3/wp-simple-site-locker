<?php

if (!class_exists('OSW3_V1_Register'))
{
    class OSW3_V1_Register extends OSW3_V1
    {
        public $state;
        public $register;
        
        public function __construct( $plugin )
        {
            $config = $plugin->getConfig(); 
            
            OSW3_V1::include_class(
                $config->Path.'osw3/register/posts.php', 
                'OSW3_V1_RegisterPosts',
                [$this]
            );
            OSW3_V1::include_class(
                $config->Path.'osw3/register/settings.php', 
                'OSW3_V1_RegisterSettings',
                // [$plugin]
                [$config]
            );

            // $posts_class = $config->Path.'osw3/register/posts.php'; 
            // $settings_class = $config->Path.'osw3/register/settings.php';
            


            // var_dump( $config->Path );
            // var_dump($plugin->getRegister());
            // exit;
            // $this->state = $state;
            // $this->register = $this->state->getRegister();

            // $path_posts = $this->state->getPath().'osw3/register/posts.php'; 
            // $path_settings = $this->state->getPath().'osw3/register/settings.php';

            // if (file_exists($path_posts))
            // {
            //     require_once($path_posts);
            // }
            // if (class_exists('OSW3_V1_RegisterPosts'))
            // {
            //     new OSW3_V1_RegisterPosts( $this );
            // }

            // if (file_exists($path_settings))
            // {
            //     require_once($path_settings);
            // }
            // if (class_exists('OSW3_V1_RegisterSettings'))
            // {
            //     new OSW3_V1_RegisterSettings( $this->state );
            // }
        }
    }
}