<?
function parseURL() {
    $url = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
    
    // Array containing with parameters passed by the URL.
    $url_params = explode('/', ltrim($url, '/'));
    
    // Contructing request object.
    $request['controller'] = array_shift($url_params);
    $request['method'] = array_shift($url_params);
    $request['params'] = $url_params;
    
    return $request;
}

?>