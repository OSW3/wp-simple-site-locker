<?php

if (!class_exists('OSW3_V1_RegisterSettings'))
{
    class OSW3_V1_RegisterSettings extends OSW3_V1
    {
        private $config;
        public $errors = [];
        
        public function __construct( $params )
        {
            $this->config = $params[0];
            $this->plugin_menus();
        }

        /**
         * Plugin Menus
         * Display the link of the settings page
         */
        private function plugin_menus()
        {
            if (isset($this->config->Menus))
            {
                foreach ($this->config->Menus['locations'] as $menu)
                {
                    switch ($menu)
                    {
                        // Main Admin Menu
                        case 'admin':
                            add_action(
                                'admin_menu', 
                                function () {
                                    add_menu_page( 
                                        $this->config->Name, 
                                        $this->config->Name, 
                                        'manage_options', 
                                        $this->config->Namespace, 
                                        array($this, 'add_options_page'),
                                        $this->config->Menus['icon']
                                    );
                                }
                            );
                            break;

                        // Avtive / Deactivate plugin menu
                        case 'action':
                            $basename = plugin_basename($this->config->Path."index.php");
                            add_filter(
                                "plugin_action_links_".$basename, 
                                function ($links) {
                                    $settings_link = '<a href="options-general.php?page='.$this->config->Namespace.'">' . __( 'Settings' ) . '</a>';
                                    array_push( $links, $settings_link );
                                    return $links;
                                }
                            );
                            break;

                        // Submenu of Settings Menu
                        case 'settings':
                            add_action( 
                                'admin_menu', 
                                function () {
                                    add_options_page( 
                                        $this->config->Name, 
                                        $this->config->Name,
                                        'manage_options', 
                                        $this->config->Namespace,
                                        array($this, 'add_options_page')
                                    );
                                }
                            );
                            break;
                    }
                }
            }
        }

        /**
         * Settings Form Schema
         * Get schema for the settings
         */
        public function settings_form_schema()
        {
            $namespace = $this->config->Namespace;
            $options = get_option( $namespace );

            $schema = isset($this->config->Schemas['settings'])
                ? $this->config->Schemas['settings']
                : [];

            foreach ( $schema as $section_key => $section_value )
            {
                if (!isset($section_value['ID']))
                    $schema[$section_key]['ID'] = uniqid();
                    
                if (!isset($section_value['namespace']))
                    $schema[$section_key]['namespace'] = $this->config->Namespace.'_'.$schema[$section_key]->ID;
                    
                if (!isset($section_value['schema']))
                    $schema[$section_key]['schema'] = [];

                foreach ($schema[$section_key]['schema'] as $field_key => $field_value) 
                {
                    if (!isset($field_value['ID']))
                        $schema[$section_key]['schema'][$field_key]['ID'] = uniqid();
                    
                    // if (!isset($l1_value->value))
                    // {
                        $value = null;
                        if (isset($field_value->default)) $value = $field_value->default;
                        if (isset($options[$field_value->key])) $value = $options[$field_value->key];

                        $schema[$section_key]['schema'][$field_key]['value'] = $value;
                    // }
                    
                    if (!isset($field_value['section']))
                        $schema[$section_key]['schema'][$field_key]['section'] = $_TempSchema[$field_key]['ID'];
                }
            }

            return $schema;
        }

        /**
         * Settings Form Builder
         * Generate the settings page
         */
        public function settings_form_builder()
        {
            global $settings_form_schema;

            $settings_form_schema = $this->settings_form_schema();
            $name = $this->config->Name;
            $version = $this->config->Version;
            $url = $this->config->PluginUri;

            echo '<div class="wrap osw3-plugin">';
            echo '<h3>'.$name.'</h3>';
            echo '<p>';
            echo 'version '.$version;
            echo ' - ';
            echo '<a href="'.$url.'" target="_blank">get info</a>';
            echo '</p>';
            echo '<hr>';

            if ('POST' === $_SERVER['REQUEST_METHOD'] && !empty($this->errors))
            {
                echo '<div class="alert alert-danger">';
                echo 'This form has errors';
                echo '</div>';
            } 
            else if ('POST' === $_SERVER['REQUEST_METHOD'] && empty($this->errors))
            {
                echo '<div class="alert alert-success">';
                echo 'This form is updated succesfully';
                echo '</div>';
            }

            echo '<form method="post">';

            foreach ( $settings_form_schema as $section_key => $section_data )
            {
                
                $section_namespace = $section_data['namespace'].$section_data['ID'];
                
                add_settings_section(
                    $section_namespace,
                    $section_data['title'],
                    function ($data)
                    {
                        global $settings_form_schema;
                        foreach ($settings_form_schema as $section)
                        {
                            if ($data['id'] === $section['namespace'].$section['ID'])
                            {
                                echo $section['description'];
                            }
                        }
                    },
                    $this->config->Namespace
                );

                if (isset($section_data['schema'])) 
                {
                    foreach ($section_data['schema'] as $field) 
                    {
                        $field = (object) $field;

                        require_once $this->config->Path.'osw3/form/form.php';
                        require_once $this->config->Path.'osw3/form/'.$field->type.'.php';

                        $classType = "OSW3_V1_".ucfirst(strtolower($field->type))."Type";
                        $formType = new $classType($field, $this);

                        $callback = $formType->render($this->config->Namespace, false);
                        if (!empty($this->errors[$field->key]))
                        {
                            $callback.= '<div class="has-error">'.$this->errors[$field->key].'</div>';
                        }
                        $callback = create_function('', "echo '".$callback."';");

                        add_settings_field( 
                            $field->key, 
                            $field->label, 
                            $callback,
                            $this->config->Namespace, 
                            $section_namespace
                        );
                    }
                }

            }
            
            settings_fields( $this->config->Namespace );
            do_settings_sections( $this->config->Namespace );
            submit_button();

            echo '</form>';
            echo '</div>';
        }

        /**
         * Settings Form Submit
         * Check and save the settings form
         */
        public function settings_form_submit()
        {
            if ('POST' === $_SERVER['REQUEST_METHOD']) 
            {
                $settings_form_schema = $this->settings_form_schema();

                $fields = [];
                $response = [];
                $data = isset($_POST[$this->config->Namespace]) 
                    ? $_POST[$this->config->Namespace] 
                    : [];
                
                foreach ($settings_form_schema as $section)
                {
                    foreach ($section['schema'] as $field) 
                    {
                        $field = (object) $field;

                        $fields[$field->key] = [];
                        $fields[$field->key]['type'] = $field->type;
                        
                        if (isset($field->required))
                            $fields[$field->key]['required'] = true;
                            
                        if (isset($field->regex->rule))
                            $fields[$field->key]['regex-rule'] = $field->regex->rule;
                            
                        if (isset($field->regex->errmsg))
                            $fields[$field->key]['regex-errmsg'] = $field->regex->errmsg;
                    }
                }

                // Define response
                foreach ($fields as $field_key => $field_data)
                {
                    
                    switch ($field_data['type'])
                    {
                        case 'checkbox':
                            $response[$field_key] = null !== $data[$field_key] ? "on" : "off";
                            break;
                        case 'password':
                            $response[$field_key] = !empty($data[$field_key]) ? password_hash($data[$field_key], PASSWORD_DEFAULT) : "";
                            break;
                        default:
                            $response[$field_key] = $data[$field_key];
                            break;
                    }
                }

                // Check form
                foreach ($data as $key => $value)
                {
                    if (!empty($fields[$key]))
                    {
                        if (!isset($this->errors[$key]) && isset($fields[$key]['required']) && empty($value))
                        {
                            $this->errors[$key] = "This field is required";
                        }

                        if (!isset($this->errors[$key]) && isset($fields[$key]['regex-rule']) && !preg_match($fields[$key]['regex-rule'], $value))
                        {
                            if (isset($fields[$key]['regex-errmsg']))
                            {
                                $this->errors[$key] = $fields[$key]['regex-errmsg'];
                            }
                            else
                            {
                                $this->errors[$key] = "This field has an error";
                            }
                        }
                    }
                }
                
                // Update database
                if (empty($this->errors)) {
                    update_option($this->config->Namespace, $response);
                }
            }
        }
        
        
        public function add_options_page()
        {
            $this->settings_form_submit();
            $this->settings_form_builder();
        }
    }
}
