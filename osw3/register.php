<?php

if (!class_exists('OSW3_Register'))
{
    class OSW3_Register extends OSW3
    {
        private $config;
        // public $state;
        // public $register;
        
        public function __construct( $params )
        {
            $this->config = $params[0];
            
            OSW3::include_class(
                $this->config->Path.'osw3/register/posts.php', 
                'OSW3_RegisterPosts',
                // [$this]
                $params
            );

            OSW3::include_class(
                $this->config->Path.'osw3/register/settings.php', 
                'OSW3_RegisterSettings',
                $params
            );
        }
    }
}