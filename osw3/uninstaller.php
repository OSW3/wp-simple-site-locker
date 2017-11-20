<?php

if (!class_exists('OSW3_V1_Uninstaller'))
{
    class OSW3_V1_Uninstaller extends OSW3_V1_Installer
    {
        /**
         * Trigger for plugin desactivation
         */
        public function uninstall()
        {
            $this->removeDatabase();
            $this->removeOptions();
        }

        /**
         * Remove entities
         */
        private function removeDatabase ()
        {
            foreach( $this->getDatabaseStructure() as $table)
            {
                preg_match("/CREATE TABLE\s(.*)\s\(/iu", $table, $matches);

                if (isset($matches[1])) 
                {
                    $this->state->wpdb()->query( "DROP TABLE IF EXISTS " . $matches[1] );
                }
            }
        }
    
        /**
         * Remove options
         */
        private function removeOptions ()
        {
            delete_option( $this->state->getSettingsNamespace() );
        }
    }
}