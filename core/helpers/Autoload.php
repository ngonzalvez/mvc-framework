<?
spl_autoload_register(function($classname) {
    include_once(CORE_PATH . "/libs/$classname.php");
});
?>