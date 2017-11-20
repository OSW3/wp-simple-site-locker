<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>I'm just an OSW3 Wordpress plugin.<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress-plugins/";
	exit;
}

$__DIR__ = preg_replace("/\/osw3$/", null, __DIR__);

// Files required
require_once(__DIR__.'/osw3.php');

// Plugin activation / desactivation
register_activation_hook( $__DIR__."/index.php", function(){
    $d = preg_replace("/\/osw3$/", null, __DIR__);
    $c = OSW3_V1::getClassname($d); 
    $p = new $c($d); 
    $p->activate();
});
register_deactivation_hook( $__DIR__."/index.php", function(){
    $d = preg_replace("/\/osw3$/", null, __DIR__);
    $c = OSW3_V1::getClassname($d); 
    $p = new $c($d); 
    $p->deactivate();
});
add_action( 'init', function(){
    $d = preg_replace("/\/osw3$/", null, __DIR__);
    $c = OSW3_V1::getClassname($d); 
    $p = new $c($d); 
    $p->start();
});

if (!class_exists($plugin_name))
{eval(sprintf('class %1$s extends OSW3_V1 
{
    private static $initialized = false;

    public function __construct($directory = null)
    {
        $this->init($directory);
    }
    public function start()
    {
        if (!self::$initialized )
        {
            self::$initialized = true;
            $this->plugin();
            $this->assets();
        }
    }
    public function activate ()
    {
        $this->plugin_installer()->install();
    }
    public function deactivate ()
    {
        $this->plugin_uninstaller()->uninstall();
    }
}', OSW3_V1::getClassname( $__DIR__ )));}