<?php

if (!class_exists('OSW3_V1_Installer'))
{
    class OSW3_V1_Installer extends OSW3_V1
    {
        protected $state;
        
        public function __construct( $state )
        {
            $this->state = $state;
        }
        
        public function install()
        {
            
            $this->installOptions();
            $this->installDatabaseStructure();
        }
        
        
        private function installOptions ()
        {
            // var_dump( (array) $this->state->getOptions() );
            // die('INSTALLER');
            add_option( $this->state->getSettingsNamespace(), (array) $this->state->getOptions() );
        }


        protected function getDatabaseStructure ()
        {
            $output = [];
            $file = $this->state->getPath().'install/database-structure.sql';
            $prefix = $this->getTablePrefix();

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
        protected function getTablePrefix()
        {
            return $this->state->wpdb()->prefix . $this->state->getPrefix();
        }
        private function installDatabaseStructure () 
        {
            require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
            foreach ( $this->getDatabaseStructure() as $entity )
            {
                dbDelta( $entity );
            }
        }
    }
}