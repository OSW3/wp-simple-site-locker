<?php

if (!class_exists('OSW3_Installer'))
{
    class OSW3_Installer extends OSW3
    {
        private $config;
        
        public function __construct( $params )
        {
            $this->config = $params[0];
        }
        


        /**
         * The plugin activation process
         */
        public function activate()
        {
            $this->add_options();
            $this->add_database();
            $this->add_textdomain();
        }
        
        /**
         * The plugin deactivation process
         */
        public function deactivate()
        {
            $this->delete_options();
            $this->delete_database();
            $this->delete_textdomain();
        }
        


        /**
         * Add Options
         * add the plugin options into the wp_options table
         */
        private function add_options ()
        {
            add_option(
                $this->config->Namespace, 
                $this->config->Options
            );
        }

        /**
         * Delete Options
         * delete the plugin options from the wp_options table
         */
        private function delete_options ()
        {
            delete_option( $this->config->Namespace );
        }



        /**
         * Add Database
         * Process to install customs tables for the plugin
         */
        private function add_database () 
        {
            require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
            foreach ( $this->tables() as $table )
            {
                dbDelta( $table );
            }
        }

        /**
         * Remove Database
         * Process to remove customs tables
         */
        private function delete_database ()
        {
            global $wpdb;
            foreach( $this->tables() as $table)
            {
                preg_match("/CREATE TABLE\s(.*)\s\(/iu", $table, $matches);

                if (isset($matches[1])) 
                {
                    $wpdb->query( "DROP TABLE IF EXISTS " . $matches[1] );
                }
            }
        }

        /**
         * Parse the sql file & return an array with list of tables
         */
        protected function tables ()
        {
            $output = [];
            $file = $this->config->Path.'install/database-structure.sql';
            $prefix = $this->config->PrefixTable;

            if (file_exists($file)) 
            {
                $sql = preg_replace(
                    "/(CREATE TABLE|TRUNCATE)\s(.*)(\s|;)/iu", 
                    "$1 $prefix$2$3",
                    file_get_contents($file)
                );

                return explode(";", $sql);
            }
            
            return $output;
        }
        


        /**
         * Add TextDomain
         * add the plugin translations files
         */
        private function add_textdomain ()
        {
            $text_domain_dir = $this->config->Path."languages/";
            $text_domain_files = scandir($text_domain_dir);
            $text_domain_base = trailingslashit(WP_LANG_DIR)."plugins/";

            foreach ($text_domain_files as $text_domain_file)
            {
                if (preg_match("/^(".$this->config->Namespace."|osw3_plugins)(.+)\.mo$/", $text_domain_file))
                {
                    $target = $text_domain_dir.$text_domain_file;
                    $link = $text_domain_base.$text_domain_file;
                    if (!file_exists($link))
                    {
                        @symlink( $target, $link );
                    }
                }
            }
        }
        
        /**
         * Delete TextDomain
         * delete the plugin translations files
         */
        private function delete_textdomain ()
        {
            $text_domain_base = trailingslashit(WP_LANG_DIR)."plugins/";
            $text_domain_files = scandir($text_domain_base);
            
            foreach ($text_domain_files as $text_domain_file)
            {
                if (preg_match("/^(".$this->config->Namespace."|osw3_plugins)(.+)\.mo$/", $text_domain_file))
                {
                    $link = $text_domain_base.$text_domain_file;
                    if (file_exists($link))
                    {
                        @unlink($link);
                    }
                }
            }
        }
    }
}