<?php

// ini_set("register_argc_argv", "on");

if (!class_exists('OSW3_V1'))
{
    abstract class OSW3_V1
    {
        // private $_path;
        private $path;
        private $url;
        private $basename;
        private $name;
        private $settingsNamespace;
        private $namespace;
        private $config;
        private $prefix;
        private $version;
        private $assets;
        private $assetsCss;
        private $assetsJs;
        private $assetsAdminCss;
        private $assetsAdminJs;
        private $shortcodes;
        private $hooks;
        private $menus;
        private $submenus;
        private $options = [];
        private $register;
        private $schemas;
        private $settings;
        private $posts;
        private $plugin_uri;
        
        // public static $path;
        public $pluginHooksFile;
        
        public function init($directory)
        {
            session_start();
            
            $this->setPath($directory);
            $this->pluginHooksFile = $this->getPath().'functions.php';
            $this->setUrl(trailingslashit(plugins_url('/',$this->getPath()."index.php")));
            // $this->setConfig();

            $this->configuration();
                $this->setRegister();
                $this->setRegisterPosts();
                $this->setRegisterSettings();
            $this->setHooks();
            $this->setName();
            $this->setNamespace();
            $this->setPluginUri();
            $this->setMenus();
            $this->setVersion();
            $this->setPrefix();
            $this->setSchemas();
            $this->setOptions();
            $this->setShortcodes();


            // self::$path = $this->getPath();

            // var_dump($this->getConfig());
            // exit;
        }

        public function plugin()
        {
            // Init Events
            $this->plugin_hooks();
            
            // Init registers
            $this->plugin_register();
            
            // Init Shortcodes
            $this->plugin_shortcodes();
        }

        public function assets ()
        {
            // add_action('wp_print_styles', array($this, 'enqueue_styles'));
            // add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_action('admin_print_styles', array($this, 'enqueue_admin_styles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        }
        



        /**
         * Set Plugin Config
         */
        // public function setConfig()
        // {
        //     // $this->config = $this->plugin_configuration();

        //     // $file = $this->getPath().'config.json';

        //     // $this->config = (file_exists($file)) 
        //     // ? json_decode(file_get_contents($file))
        //     // : [];

        //     // // Get config form index.php
        //     // $this->config = (object) array_merge(
        //     //     (array) $this->config, 
        //     //     self::getIndexConfig($this->getPath())
        //     // );

        //     // $this->setName();
        //     // $this->setSettingsNamespace();
        //     // $this->setNamespace();
        //     // $this->setVersion();
        //     // $this->setPrefix();

        //     $this->setOptions();
        //     // $this->setRegister();
        //     // $this->setHooks();
        //     // $this->setAssetsStyles();
        //     // $this->setAssetsScripts();
        //     // $this->setAssetsAdminStyles();
        //     // $this->setAssetsAdminScripts();
        //     // $this->setShortcodes();

        //     // var_dump(self::$path);
        // }
        
        /**
         * Get Plugin Name
         * @return (string)
         */
        public function getConfig()
        {
            // return $this->config;
            return (object) [
                "Name"      => $this->getName(),
                "Namespace" => $this->getNamespace(),
                "Classname" => self::getClassname($this->path),
                "Prefix"    => $this->getPrefix(),
                "Version"   => $this->getVersion(),

                "Path"      => $this->getPath(),
                "Url"       => $this->getUrl(),
                "PluginUri" => $this->getPluginUri(),
                "Menus"     => $this->getMenus(),
                
                "Hooks"     => $this->getHooks(),
                "Shortcodes"=> $this->getShortcodes(),

                "Options"   => $this->getOptions(),
                "Schemas"   => $this->getSchemas(),
                // "Settings"  => $this->getSettings(),
            ];
        }
        



        /**
         * Get Plugin Classname
         * @return (string)
         */
        public static function getClassname($base)
        {
            $conf = self::configuration_index( $base."/" );

            return self::slugify(implode(" ", [
                $conf['author'],
                $conf['plugin_name']
            ]), "_");
        }
        



        /**
         * Set Plugin Hooks
         */
        public function setHooks()
        {
            $this->hooks = isset($this->config->hooks) 
                ? (object) $this->config->hooks 
                : (object) [];
        }
        
        /**
         * Get Plugin Hooks
         * @return (object)
         */
        public function getHooks()
        {
            return $this->hooks;
        }
        



        /**
         * Set Plugin Name
         * The name of the plugin was defined in the index.php of the plugin at the line "Plugin Name"
         */
        public function setName()
        {
            $this->name = isset($this->config->plugin_name) ? $this->config->plugin_name : null;
        }
        
        /**
         * Get Plugin Name
         * @return (string)
         */
        public function getName()
        {
            return $this->name;
        }
        



        /**
         * Set Plugin NameSpace
         * The namespace of the plugin was defined in the config.json as the line "namespace"
         * If the namespace is not set in the config file, The plugin namespace will automatically defined by the plugin name's slug (with underscore)
         */
        public function setNamespace()
        {
            $this->namespace = isset($this->config->namespace) 
                ? self::slugify($this->config->namespace, "_")
                : self::slugify($this->getName(), "_");
        }
        
        /**
         * Get Plugin NameSpace
         * @return (string)
         */
        public function getNamespace()
        {
            return $this->namespace;
        }
        



        /**
         * Set Plugin NameSpace
         * The namespace of the plugin was defined in the config.json as the line "namespace"
         * If the namespace is not set in the config file, The plugin namespace will automatically defined by the plugin name's slug (with underscore)
         */
        public function setMenus()
        {
            $this->menus = [
                "locations" => [],
                "icon" => null
            ];

            $menus = isset($this->settings->menus ) 
                ? (object) $this->settings->menus 
                : (object) [];

            if (true === $menus->admin) array_push($this->menus['locations'], "admin");
            if (true === $menus->action) array_push($this->menus['locations'], "action");
            if (true === $menus->settings) array_push($this->menus['locations'], "settings");

            if (isset($menus->icon))
            {
                if (preg_match("/^image:/", $menus->icon))
                {
                    $this->menus['icon'] = $this->getUrl()."assets/images/".preg_replace("/^image:/", null, $menus->icon);
                }
                else 
                {
                    $this->menus['icon'] = $menus->icon;
                }
            }
        }
        
        /**
         * Get Plugin NameSpace
         * @return (string)
         */
        public function getMenus()
        {
            return $this->menus;
        }
        



        /**
         * Set Plugin Options
         */
        public function setOptions($data = null)
        {
            if (null === $data)
            {
                $data = $this->getSchemas();
            }

            foreach ($data as $key => $value) {
                if (is_array($value))
                {
                    $this->setOptions($value);
                }
                else {
                    if ('default' === $key)
                    {
                        $this->options[$data['key']] = $value;
                    }
                }
            }
        }
        
        /**
         * Get Plugin Options
         * @return (string)
         */
        public function getOptions( $key=null )
        {
            if (null !== $key) {
                return isset($this->options->$key) ? $this->options->$key : null;
            }
            return $this->options;
        }
        



        /**
         * Set Plugin Path
         */
        public function setPath( $path )
        {
            $this->path = $path.DIRECTORY_SEPARATOR;
        }

        /**
         * Get Plugin Path
         * @return (string) something like : /var/www/my_webstie/wp-content/plugins/plugin-name/
         */
        public function getPath()
        {
            return $this->path;
        }
        



        /**
         * Set Plugin URI
         * The name of the plugin was defined in the index.php of the plugin at the line "Plugin Name"
         */
        public function setPluginUri()
        {
            $this->plugin_uri = isset($this->config->plugin_uri) ? $this->config->plugin_uri : null;
        }
        
        /**
         * Get Plugin URI
         * @return (string)
         */
        public function getPluginUri()
        {
            return $this->plugin_uri;
        }
        



        /**
         * Set Plugin Prefix
         */
        public function setPrefix()
        {
            $this->prefix = $this->getNamespace()."_";
        }
        
        /**
         * Get Plugin Prefix
         * @return (string)
         */
        public function getPrefix()
        {
            return $this->prefix;
        }
        



        /**
         * Set Plugin Schemas
         */
        public function setSchemas()
        {
            $posts = $this->posts;
            $settings = $this->settings;
            $schemas = [
                "posts" => [],
                "settings" => []
            ];

            // Build Custom posts schema
            foreach ($posts as $post)
            {
                if (isset($post['type']) && !empty($post['type']))
                {
                    if (isset($post['metas']['schema']))
                    {
                        $schemas['posts'][$post['type']] = $post['metas']['schema'];
                    }
                }
            }

            // Build Settings schema
            if (isset($settings->schema) && !empty($settings->schema))
            {
                foreach ($settings->schema as $section_key => $section_data)
                {
                    if (isset($section_data['schema']) && !empty($section_data['schema']))
                    {
                        array_push(
                            $schemas['settings'], 
                            $section_data
                        );
                    }
                }
            }

            $this->schemas = $schemas;
        }
        
        /**
         * Get Plugin Schemas
         * @return (object)
         */
        public function getSchemas()
        {
            return $this->schemas;
        }
        



        /**
         * Set Plugin Shortcodes
         */
        public function setShortcodes()
        {
            $this->shortcodes = isset($this->config->shortcodes) 
                ? (object) $this->config->shortcodes 
                : (object) [];
        }
        
        /**
         * Get Plugin Shortcodes
         * @return (object)
         */
        public function getShortcodes()
        {
            return $this->shortcodes;
        }
        



        /**
         * Set Plugin URL
         */
        public function setUrl( $url )
        {
            $this->url = preg_replace("/osw3\/$/", null, $url);
        }
        
        /**
         * Get Plugin URL
         * @return (string) something like : http://my-website.com/wp-content/plugins/plugin-name/
         */
        public function getUrl()
        {
            return $this->url;
        }
        



        /**
         * Set Plugin Version
         */
        public function setVersion()
        {
            $this->version = isset($this->config->version) ? $this->config->version : null;
        }
        
        /**
         * Get Plugin Version
         * @return (string)
         */
        public function getVersion()
        {
            return $this->version;
        }





        
        

















        
        // public function setSettingsNamespace()
        // {
        //     $this->settingsNamespace = isset($this->config->namespace) ? "settings_".$this->config->namespace : null;
        // }
        // public function getSettingsNamespace()
        // {
        //     return $this->settingsNamespace;
        // }
        
        // public function setAssets()
        // {
        //     $this->assets = $this->config;
        // }
        // public function getAssets()
        // {
        //     return $this->assets;
        // }
        
        public function setAssetsCss()
        {
            $this->assetsCss = isset($this->config->assets->styles) ? $this->config->assets->css : (object) [];
        }
        public function getAssetsCss()
        {
            return $this->assetsCss;
        }
        
        public function setAssetsJs()
        {
            $this->assetsJs = isset($this->config->assets->scripts) ? $this->config->assets->js : (object) [];
        }
        public function getAssetsJs()
        {
            return $this->assetsJs;
        }
        










        private function setRegister()
        {
            $this->register = isset($this->config->register)
                ? (object) $this->config->register
                : (object) [];
        }
        private function setRegisterPosts()
        {
            $this->posts = isset($this->register->posts)
                ? (object) $this->register->posts
                : (object) [];
        }
        private function setRegisterSettings()
        {
            $this->settings = isset($this->register->settings)
                ? (object) $this->register->settings
                : (object) [];
        }

        protected function getRegister()
        {
            return $this->register;
        }

















        // public function getRegister()
        // {
        //     return $this->register;
        // }


        // public function setSettings()
        // {
        //     $register = isset($this->config->register)
        //         ? (object) $this->config->register
        //         : null;
            
        //     if (isset($register->settings))
        //     {
        //         var_dump($register->settings);
        //     }

        //     // $this->register = isset($this->config->register) ? $this->config->register : null;

        //     // if ($this->register)
        //     // {
        //     //     $this->setSchema();
        //     // }
        // }
        // public function getSettings()
        // {
        //     return $this->register;
        // }





        public function wpdb()
        {
            global $wpdb;
            return $wpdb;
        }


        public function enqueue_styles()
        {
            // $styles = $this->getAssetsStyles();
        }

        public function enqueue_scripts()
        {
            // $scripts = $this->getAssetsScripts();
        }

        public function enqueue_admin_styles()
        {
            // $styles = $this->getAssetsAdminStyles();
        }

        public function enqueue_admin_scripts()
        {
            // $scripts = $this->getAssetsAdminScripts();
        }

        // public function admin_assets_css() 
        // {
        //     $admin_stylesheet = $this->getUrl() . 'assets/css/admin.css';   
        //     wp_enqueue_style( $this->getPrefix().'admin_css', $admin_stylesheet );
        // }

        // public function admin_assets_js() 
        // {
        //     var_dump($this->getPath());
        //     var_dump($this->setAssetsJs());

        //     wp_register_script('admin_js', $this->getUrl() . 'assets/js/admin.js');
        //     wp_enqueue_script('admin_js');
        //     // wp_register_script( $this->getPrefix().'admin_metabox_js', $this->getUrl() . 'assets/js/admin-metabox.js');
        // }
        // public function assets_css()
        // {
        //     if (!is_admin()) 
        //     {
        //         foreach ($this->getAssetsCss() as $asset) 
        //         {
        //             $asset_id = preg_replace("/\./", "_", $asset);
        //             $asset_id = strtolower($asset_id);
        //             $asset_file = $this->getUrl().'assets/css/'.$asset.'.css';

        //             wp_enqueue_style($asset_id, $asset_file);
        //         }
        //     }
        // }
        // public function assets_js()
        // {
        //     if (!is_admin()) 
        //     {
        //         foreach ($this->getAssetsJs() as $asset) 
        //         {
        //             if (isset($asset[0]))
        //             {
        //                 $asset_id = preg_replace("/\./", "_", $asset[0]);
        //                 $asset_id = strtolower($asset_id);
        //                 $asset_file = $this->getUrl().'assets/js/'.$asset[0].'.js';
        //                 $asset_dep = isset($asset[1]) ? $asset[1] : [];

        //                 wp_enqueue_script($asset_id, $asset_file, $asset_dep);
        //             }
        //         }
        //     }
        // }













        /**
         * Configuration builder
         */
        private function configuration()
        {
            $config = [];
            $config_json = $this->getPath().'config.json';

            // Parse config from jSon file
            if (file_exists($config_json))
            {
                $config_json = file_get_contents($config_json);
                $config_json = json_decode($config_json, true);

                $config = array_merge( $config, $config_json );
            }

            // Parse config from Index.php
            $config = array_merge(
                $config,
                self::configuration_index($this->getPath())
            );

            $this->config = (object) $config;
        }

        /**
         * 
         */
        public static function configuration_index( $path )
        {
            $config = [];

            $config_php = $path."index.php";
            
            if (file_exists($config_php)) 
            {
                $config_php = file_get_contents($config_php);
                
                preg_match(
                    "/\/\*\*(.*)\*\//uis", 
                    $config_php, 
                    $config_params
                );
                $config_params = explode("* ", $config_params[0]);

                foreach( $config_params as $config_param )
                {
                    list($config_param_key, $config_param_value) = explode(": ", $config_param);
                    $config_param_key   = OSW3_V1::slugify($config_param_key, "_");
                    $config_param_value = trim(preg_replace("/\*\//", null, $config_param_value));

                    if (!empty($config_param_key))
                    {
                        $config[$config_param_key] = trim($config_param_value);
                    }
                }
            }

            return $config;
        }














        /**
         * Instantiate Installer
         */
        protected function plugin_installer ()
        {
            require_once(__DIR__.'/installer.php');
            return new OSW3_V1_Installer( $this );
        }
    
        /**
         * Instantiate Uninstaller
         */
        protected function plugin_uninstaller ()
        {
            // $path_installer = __DIR__.'/installer.php';
            // $path_uninstaller = __DIR__.'/uninstaller.php';
            
            // if (file_exists($path_installer) && file_exists($path_uninstaller))
            // {
                require_once(__DIR__.'/installer.php');
                require_once(__DIR__.'/uninstaller.php');
            // }
            // if (class_exists('OSW3_V1_Uninstaller'))
            // {
                return new OSW3_V1_Uninstaller( $this );
                // $uninstaller->uninstall();
            // }
            
        }
    
        /**
         * Instantiate Post
         */
        protected function plugin_register ()
        {
            $path_register = __DIR__.'/register.php';

            // if (file_exists($path_register))
            // {
                require_once($path_register);
            // }

            // if (class_exists('OSW3_V1_Register'))
            // {
                return new OSW3_V1_Register( $this );
            // }

            // return false;
        }
        
        /**
         * Instantiate Shortcodes
         */
        protected function plugin_shortcodes()
        {
            if (file_exists($this->pluginHooksFile))
            {
                require_once($this->pluginHooksFile);
                foreach ($this->getShortcodes() as $shortcode => $function)
                {
                    if ( function_exists($function) )
                    {
                        add_shortcode($shortcode, $function);
                    }
                }
            }
        }
        
        /**
         * Instantiate Events
         */
        protected function plugin_hooks()
        {
            if (file_exists($this->pluginHooksFile))
            {
                require_once($this->pluginHooksFile);
                foreach ($this->getHooks() as $function => $event)
                {
                    if ( function_exists($function) )
                    {
                        $order = $event == 'init' ? 1 : 10;
                        add_action($event, $function, $order);
                    }
                }
            }
        }















        public static function include_class($file, $className, $params = [])
        {
            if (file_exists($file))
            {
                require_once($file);

                if (class_exists($className))
                {
                    new $className( $params );
                }
            }
        }


        public static function slugify($text,$separator="-")
        {
            $text = preg_replace('~[^\pL\d]+~u', $separator, $text);
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            $text = preg_replace('~[^-\w]+~', '', $text);
            $text = trim($text, $separator);
            $text = preg_replace('~-+~', $separator, $text);
            $text = strtolower($text);

            if (empty($text)) return false;

            return $text;
        }







        public static function tryToDo( $expression )
        {
            list($a, $b) = explode(":", $expression);

            if ("do" === $a && function_exists($b)) {
                return $b();
            }

            else if ("menu" === $a) {
                $o = [];
                $items = wp_get_nav_menu_items($b);

                if (is_array($items))
                {
                    foreach ($items as $item) 
                    {
                        $o[ OSW3_V1::slugify($item->title) ] = $item->title;
                    }
                }
                return $o;
            }

            return false;
        }


        public static function getPostSchema($configFile, $params)
        {
            $config = [];
            $calssName = null;
            $attrNameIsArray = false;
            
            if (file_exists($configFile))
                $config = json_decode(file_get_contents($configFile));

            if (isset($config->register->posts))
                $config = $config->register->posts;

            foreach ($config as $postConfig)
                if (isset($postConfig->type) && isset($params['post']) && $postConfig->type == $params['post'])
                    $config = $postConfig;
            
            if (isset($config->view) && $config->view === true)
                $attrNameIsArray = true;
            
            if (isset($config->metas->schema))
                $config = $config->metas->schema;

            
            foreach ($config as $field)
                if (isset($field->key) && isset($params['key']) && $field->key == $params['key'])
                    $config = $field;
            
            if (isset($config->type))
            {
                if (isset($params['value']))
                    $config->value = $params['value'];
                
                if (isset($params['disabled']))
                    $config->disabled = $params['disabled'] === 'true' ? true : false;
                
                if (isset($params['class']))
                    $config->class = $params['class'];

                if (isset($params['cols']))
                    $config->cols = $params['cols'];

                if (isset($params['rows']))
                    $config->rows = $params['rows'];

                $className = "OSW3_V1_".ucfirst(strtolower($config->type))."Type";
                require_once $path.'form/form.php';
                require_once $path.'form/'.strtolower($config->type).'.php';

                $field = new $className($config);
                $render = $field->render( $params['post'], false );

                if (!$attrNameIsArray)
                {
                    $render = preg_replace("/".$params['post']."\[(.*)\]/", "$1", $render);
                }

                return $render;
            }
        }
        public static function checkPostSchema($configFile, $postType)
        {
            $hasErrors = false;
            $_SESSION[$postType] = [];
            $typeFiles = [];
            $schema = [];

            $post = $_POST;
            if (isset($_POST[$postType]))
            {
                $post = $_POST[$postType];
            }
            
            if($_SERVER['REQUEST_METHOD'] == 'POST' 
            && isset($_POST[$postType.'-token'])
            && wp_verify_nonce($_POST[$postType.'-token'], $postType)
            ) {
                if (file_exists($configFile))
                    $config = json_decode(file_get_contents($configFile));

                if (isset($config->register->posts))
                    $config = $config->register->posts;

                foreach ($config as $postConfig)
                    if (isset($postConfig->type) && $postConfig->type == $postType)
                        $config = $postConfig;
                
                if (isset($config->metas->schema))
                    $schema = $config->metas->schema;

                // Checking fields
                foreach ($schema as $field) 
                {
                    $_SESSION[$postType][$field->key] = [];

                    if ($field->type === 'file')
                    {
                        $typeFiles[$field->key] = $field;
                    }
                    
                    if (isset( $post[$field->key] ))
                    {
                        $_SESSION[$postType][$field->key]['value'] = $post[$field->key];
                        
                        // Required
                        if (isset($field->required) && $field->required === true && empty($post[$field->key]))
                        {
                            $_SESSION[$postType][$field->key]['error'] = isset($field->required_message) ? $field->required_message : "This field is required.";
                            $hasErrors = true;
                        }
                        
                        // Is Email
                        else if (($field->type == 'email' && !filter_var($post[$field->key], FILTER_VALIDATE_EMAIL)) && (isset($field->required) && $field->required === true))
                        {
                            $_SESSION[$postType][$field->key]['error'] = isset($field->syntax_error) ? $field->syntax_error : "This field is not a valid email address.";
                            $hasErrors = true;
                        }
                        
                        // Is URL
                        else if (($field->type == 'url' && !filter_var($post[$field->key], FILTER_VALIDATE_URL)) && (isset($field->required) && $field->required === true))
                        {
                            $_SESSION[$postType][$field->key]['error'] = isset($field->syntax_error) ? $field->syntax_error : "This field is not a valid url address.";
                            $hasErrors = true;
                        }
                        
                        // RegEx
                        else if (isset($field->regex) && !empty($field->regex) && !preg_match($field->regex, $post[$field->key]))
                        {
                            $_SESSION[$postType][$field->key]['error'] = isset($field->syntax_error) ? $field->syntax_error : "This field is not a valid.";
                            $hasErrors = true;
                        }
                    }
                }

                // Checking files
                if (!empty($_FILES[$_REQUEST['post_type']]))
                {
                    $files = [];
                    $allowed_types = [];

                    foreach ($_FILES[$_REQUEST['post_type']] as $key => $file)
                    {
                        foreach (array_keys($file) as $keyName)
                        {
                            if (!isset($files[$keyName]))
                            {
                                $files[$keyName] = [];
                            }
                            $files[$keyName][$key] = $file[$keyName];
                        }
                    }

                    foreach ($files as $key => $file)
                    {
                        if (!empty($file['name']))
                        {
                            // Get allowed types
                            if (isset($typeFiles[$key]->allowed_types))
                            {
                                $allowed_types = $typeFiles[$key]->allowed_types;
                            }
    
                            // Get file type
                            $fileType = wp_check_filetype(basename($file['name']));
    
                            // Check if type is allowed
                            if (!empty($allowed_types))
                            {
                                if (!in_array($fileType['type'], $allowed_types))
                                {
                                    $_SESSION[$_REQUEST['post_type']][$key]['error'] = isset($typeFiles[$key]->type_error) ? $typeFiles[$key]->type_error : "The file type that you've uploaded is not allowed.";
                                    $hasErrors = true;
                                }
                            }
                        }
                    }
                }
                
                if (!$hasErrors)
                {
                    $_SESSION[$postType]['success'] = true;
                }

                return !$hasErrors;
            }
        }
    }
}
