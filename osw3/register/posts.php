<?php

if (!class_exists('OSW3_V1_RegisterPosts'))
{
    class OSW3_V1_RegisterPosts extends OSW3_V1
    {
        public $state;
        public $posts;

        public function __construct( $state )
        {
            // $this->state = $state;

            // $this->setPosts();
            // $this->posts_register();
        }

        public function posts_register()
        {
            foreach ($this->getPosts() as $post)
            {
                if (isset($post->type))
                {
                    // Convert Object to Array
                    if (isset($post->capabilities))
                    {
                        $post->capabilities = (array) $post->capabilities;
                    }

                    // Set dynamic slug
                    if (isset($post->rewrite)) 
                    {
                        if (false != $post->rewrite)
                        {
                            $post->rewrite = (array) $post->rewrite;

                            $do = OSW3_V1::tryToDo($post->rewrite['slug']);
                            if (false != $do)
                            {
                                $post->rewrite['slug'] = $do;
                            }
                        }
                    }

                    // Add Post Type to the Wordpress Register
                    register_post_type( $post->type, (array) $post );

                    // Add Post Category
                    if (isset($post->category))
                    {
                        if (!isset($post->category->taxonomy))
                        {
                            $post->category->taxonomy = $post->type.'_category';
                        }

                        $object_type = [$post->type];
                        if (isset($post->category->objects) && is_array($post->category->objects))
                        {
                            $object_type = array_merge($object_type,$post->category->objects);
                        }
                        
                        if (isset($post->category->rewrite))
                        {
                            if (false != $post->category->rewrite)
                            {
                                $post->category->rewrite = (array) $post->category->rewrite;
    
                                $do = OSW3_V1::tryToDo($post->category->rewrite['slug']);
                                if (false != $do)
                                {
                                    $post->category->rewrite['slug'] = $do;
                                }
                            }
                        }

                        $post->category->hierarchical = true;
                        register_taxonomy(
                            $post->category->taxonomy, 
                            $object_type,
                            $post->category
                        );
                    }

                    // Add Post Tag
                    if (isset($post->tag))
                    {
                        $post->tag->type = $post->type.'_tag';
                        $post->tag->hierarchical = false;
                        register_taxonomy( $post->tag->type, $post->type, $post->tag );
                    }

                    // Add MetaBoxes to the form
                    if (isset($post->metas))
                    {
                        add_action('save_post', array($this, "save_metaboxes"));
                        add_action('admin_init', array($this, 'display_metaboxes'));
                    }

                    // Manage Columns list for custom post
                    if (isset($post->listTable))
                    {
                        add_filter( "manage_{$post->type}_posts_columns", array($this, 'set_custom_columns') );
                        add_action( "manage_{$post->type}_posts_custom_column" , array($this, 'custom_column_data'), 10, 2 );
                        add_filter( "manage_edit-{$post->type}_sortable_columns", array($this, 'custom_columns_sortable') );
                    }
                }
            }
        }

        public function display_metaboxes()
        {
            $path = $this->state->state->getPath();
            require_once($path.'osw3/register/metaboxes.php');
            new OSW3_V1_RegisterMetaboxes( $this );
        }
        public function save_metaboxes( $id )
        {
            $path = $this->state->state->getPath();
            require_once($path.'osw3/register/metaboxes.php');
            OSW3_V1_RegisterMetaboxes::save( $this->posts, intval($id), $path );
        }

        public function setPosts()
        {
            if( isset($this->state->register->posts) ) {
                $this->posts = $this->state->register->posts;
            }
            else {
                $this->posts = [];
            }
        }
        public function getPosts()
        {
            return $this->posts;
        }

        public function set_custom_columns($columns)
        {
            unset( $columns['date'] );

            foreach ($this->getPosts() as $post)
            {
                if (isset($post->type))
                {
                    if (isset($post->listTable->columns))
                    {
                        foreach ($post->listTable->columns as $column)
                        {
                            $columns[OSW3_V1::slugify($column->name)] = $column->name;
                        }
                    }
                }
            }

            return $columns;
        }
        public function custom_columns_sortable($columns)
        {
            foreach ($this->getPosts() as $post)
            {
                if (isset($post->type))
                {
                    if (isset($post->listTable->columns))
                    {
                        foreach ($post->listTable->columns as $column)
                        {
                            if (isset($column->sortable) && $column->sortable === true)
                            {
                                $columns[OSW3_V1::slugify($column->name)] = OSW3_V1::slugify($column->name);
                            }
                        }
                    }
                }
            }

            return $columns;
        }
        public function custom_column_data($columnName, $post_id)
        {
            foreach ($this->getPosts() as $post)
            {
                if (isset($post->type))
                {
                    if (isset($post->listTable->columns))
                    {
                        foreach ($post->listTable->columns as $column)
                        {
                            if ($columnName === OSW3_V1::slugify($column->name))
                            {
                                if (is_array($column->data)) 
                                {
                                    $colData = [];
                                    $glue = isset($column->glue) ? $column->glue : " ";
                                    foreach ($column->data as $data) 
                                    {
                                        array_push($colData, get_post_meta($post_id , $data, true));
                                    }
                                    echo implode($glue, $colData); 
                                }
                                else {
                                    echo get_post_meta( $post_id , $column->data , true ); 
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
