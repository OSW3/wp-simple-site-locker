<?php

if (!class_exists('OSW3_RegisterMetaboxes'))
{
    class OSW3_RegisterMetaboxes extends OSW3
    {
        public $state;
        public $posts;
        public $schema;

        private $id = 0;
        private $new = true;

        public $posttype;

        public function __construct( $state )
        {
            $this->state = $state->state;
            $this->posts = $state->posts;

            $this->setPostType();
            $this->metaboxes_register();

            foreach ($this->posts as $post)
            {
                if (isset($post->thumbnails))
                {
                    foreach($post->thumbnails as $thumbnail)
                    {
                        $name = isset($thumbnail->name) ? $thumbnail->name : null;
                        $width = isset($thumbnail->width) ? $thumbnail->width : null;
                        $height = isset($thumbnail->height) ? $thumbnail->height : null;
                        $crop = isset($thumbnail->crop) ? $thumbnail->crop : false;

                        if (null != $name)
                        {
                            add_image_size( $name, $width, $height, $crop );
                        }
                    }
                }
            }
        }

        public function metaboxes_register()
        {
            foreach ($this->posts as $post)
            {
                if (isset($post->metas)) 
                {
                    if ($post->type === $this->getPostType())
                    {
                        add_action('post_edit_form_tag', array( $this, 'add_form_novalidate'));

                        $this->setMetas($post);
                        foreach ($this->getMetas() as $meta) 
                        {
                            if ('file' === $meta->type) add_action('post_edit_form_tag', array( $this, 'add_form_enctype'));
                        }

                        $id             = $post->type.'_meta';
                        $title          = !empty($post->metas->title) ? $post->metas->title : "-";
                        $callback       = array( $this, 'metaboxes_callback' );
                        $screen         = $post->type;
                        $context        = !empty($post->metas->context) ? $post->metas->context : 'normal';
                        $priority       = !empty($post->metas->priority) ? $post->metas->priority : 'high';
                        $callback_args  = !empty($post->metas->callback_args) ? $post->metas->callback_args : [];

                        add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

                        if (isset($_SESSION[$this->getPostType()]) && !isset($_SESSION[$this->getPostType()]['success']))
                        {
                            add_action('admin_notices', array($this, 'add_admin_notices'));
                        }
                        
                        if ($post->permalink === false)
                        {
                            add_filter( 'get_sample_permalink_html', array($this, 'hide_permalink') );
                        }
                    }
                }
            }

        }

        public function metaboxes_callback($wp_post)
        {
            $output = '';
            $path = $this->state->state->getPath();

            foreach ($this->posts as $post)
            {
                $this->setMetas($post);
                
                if (isset($post->metas)) 
                {
                    if ($post->type === $this->getPostType())
                    {
                        wp_nonce_field( $this->getPostType(), $this->getPostType().'-token' );

                        // Set form from view file
                        if (isset($post->view) && $post->view)
                        {
                            $file = $path."views/".$post->type.".php";
                            if (file_exists($file)) 
                            {
                                include_once $file;
                            }
                        }

                        // Set form by default
                        else 
                        {
    
                            foreach ($this->getMetas() as $meta) 
                            {
                                if (isset($meta->type))
                                {
                                    $before = null;
                                    $after = null;
                                    $className = "OSW3_".ucfirst(strtolower($meta->type))."Type";
    
                                    require_once $path.'osw3/form/form.php';
                                    require_once $path.'osw3/form/'.$meta->type.'.php';
                                    
                                    if (isset($_SESSION[$post->type][$meta->key]['error']))
                                    {
                                        $meta->class .= " has-error";
                                        $after .= '<div class="has-error">';
                                        $after .= $_SESSION[$post->type][$meta->key]['error'];
                                        $after .= '</div>';
                                    }
                                    
                                    $field = new $className($meta, $this);
                                    $output .= $field->render( $post->type, true, true, $before, $after );
                                }
                            }
                        }

                        if (isset($_SESSION[$this->getPostType()]))
                        {
                            unset($_SESSION[$this->getPostType()]);
                        }
                    }
                }
            }

            echo $output;
        }

        public function add_form_enctype()
        {
            echo ' enctype="multipart/form-data"';
        }
        public function add_form_novalidate()
        {
            echo ' novalidate="novalidate"';
        }
        public function add_admin_notices()
        {
            $message = "This form has been saved with some errors";

            foreach ($this->posts as $post)
            {
                if (isset($post->metas)) 
                {
                    if ($post->type === $this->getPostType())
                    {
                        if (isset($post->metas->error))
                        {
                            $message = $post->metas->error;
                        }
                    }
                }
            }

            echo "<style>#message{display: none;}</style>";
            echo "<div class=\"notice-warning notice\"><p>".$message."</p></div>";
        }

        public function setPostType()
        {
            if (isset($_GET['post_type']))
            {
                $this->posttype = $_GET['post_type'];
            }
            elseif (isset($_GET['post'])) 
            {
                $this->posttype = get_post_type($_GET['post']);
            }
            else 
            {
                $this->posttype = null;
            }
        }
        public function getPostType()
        {
            return $this->posttype;
        }

        public function setMetas($post)
        {
            if (!empty($post->metas->schema))
            {
                $this->schema = $post->metas->schema;
            }
            else 
            {
                $this->schema = null;
            }
        }
        public function getMetas()
        {
            return $this->schema;
        }

        public function setId( $id )
        {
            $this->id = $id;
        }
        public function getId()
        {
            return $this->id;
        }

        public function save( $posts, $id, $path )
        {
            $isValid = OSW3::checkPostSchema(
                $path.'config.json', 
                $_REQUEST['post_type']
            );

            if ($isValid && $id === intval($_REQUEST['post_ID']))
            {
                // save data
                foreach ($_REQUEST[$_REQUEST['post_type']] as $key => $value)
                {
                    update_post_meta( $_REQUEST['post_ID'], $key, $value);
                }

                // Save files
                if (!empty($_FILES[$_REQUEST['post_type']]))
                {
                    $files = [];

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
                            // $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
                            $upload = wp_handle_upload($file, array('test_form' => false));
                            
                            // Add to medias list
                            $attach_id = wp_insert_attachment( [
                                'post_mime_type'    => $upload['type'],
                                'post_title'        => addslashes($file['name']),
                                'post_content'      => '',
                                'post_status'       => 'inherit',
                                'post_parent'       => $_REQUEST['post_ID']
                            ], $upload['file'], $_REQUEST['post_ID']);
                            
                            // Generate thumbnails
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
                            wp_update_attachment_metadata( $attach_id,  $attach_data );
                            
                            $existing_download = (int) get_post_meta($_REQUEST['post_ID'], $key, true);
                            if(is_numeric($existing_download)) {
                                wp_delete_attachment($existing_download);
                            }
                            
                            $upload['attachment'] = $attach_id;
    
                            // add_post_meta($_REQUEST['post_ID'], $key, $upload);
                            update_post_meta($_REQUEST['post_ID'], $key, $upload); 
                            // update_post_meta($_REQUEST['post_ID'], $key, $attach_id); 
                        }
                    }
                }
            }
        }

        public function hide_permalink()
        {
            return false;
        }
    }
}