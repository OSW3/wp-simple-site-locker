<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) 
{
    echo "Hi there!<br>I'm just an OSW3 Wordpress plugin.<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress-plugins/";
	exit;
}

// Files required
require_once(__DIR__.'/osw3.php');

// Plugin activation / desactivation
register_activation_hook( OSW3::base(__DIR__)."/index.php", function(){
    $c = OSW3::getClassname(); 
    $p = new $c(OSW3::base(__DIR__));
    $p->activate();
});
register_deactivation_hook( OSW3::base(__DIR__)."/index.php", function(){
    $c = OSW3::getClassname(); 
    $p = new $c(OSW3::base(__DIR__));
    $p->deactivate();
});
add_action( 'init', function(){
    $c = OSW3::getClassname(); 
    $p = new $c(OSW3::base(__DIR__));
    $p->start();
});

if (!class_exists($plugin_name))
{eval(sprintf('class %1$s extends OSW3 
{
    private static $loaded = false;
    public function start()
    {
        if (!self::$loaded)
        {
            self::$loaded = $this->load();
        }
    }
    public function activate()
    {
        $this->installer()->activate();
    }
    public function deactivate()
    {
        $this->installer()->deactivate();
    }
}', OSW3::getClassname()));}