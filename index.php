<?
define("CORE_PATH", dirname(__FILE__).'/core');

// This allows core classes to be autoloaded.
include CORE_PATH."/helpers/Autoload.php";

// Gets an instance of the loader singleton and initializes it.
$load = Loader::Instance();
$load->setConfig(Config::Instance());

// Gets the request array out of the URL.
$load->helper("parseURL");
$request = parseURL();

// Routing.
$router = new Router(Loader::Instance(), Config::Instance());
$router->handle($request);
    
?>