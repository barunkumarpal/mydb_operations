<?php 

session_start();

$server_ip = $_SERVER['REMOTE_ADDR'];

if($server_ip == '::1' || $server_ip == '127.0.0.1' || $server_ip == 'localhost'){
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PWD', '');
    define('DB_NAME','custom_php_ecommerce');
}else{
    define('DB_HOST', 'sql209.byethost8.com');
    define('DB_USER', 'b8_24150096');
    define('DB_PWD', 'tqpj9sng');
    define('DB_NAME','b8_24150096_core_php_ecommerce');
}



    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $current_user_ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $current_user_ip = $forward;
    }
    else
    {
        $current_user_ip = $remote;
    }


    if(isset($_SESSION['user_ip'])){
        $user_ip_session = $_SESSION['user_ip'];
    }
   

if(isset($_SESSION['logged_in']) && !isset($user_ip_session)){
    unset($_SESSION['logged_in']);
    session_destroy();    
}
if(isset($user_ip_session) && empty($user_ip_session)){
    unset($_SESSION['logged_in']);
    session_destroy(); 
}
if(isset($user_ip_session) && $user_ip_session == ''){
    unset($_SESSION['logged_in']);
    session_destroy();  
}
if(isset($user_ip_session) && !empty($user_ip_session)){
    if($user_ip_session !== $current_user_ip){
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_ip']);
        session_destroy(); 
    }
}
?>