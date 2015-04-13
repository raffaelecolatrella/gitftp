<?php

/**
 * Hosting configuration for homepage and dashboard application.
 */
$host = $_SERVER['HTTP_HOST'];
if(preg_match('/git.gitftp.com|stg.gitftp.com/i', $host)) {
    $is_dash = true;
    $controller = 'dashboard/index';
}else{
    $controller = 'welcome/index';
    $is_dash = false;
}
if(preg_match('/localhost/', $host)){
    $base = 'http://stg.gitftp.com/';
}
if(preg_match('/stg.gitftp.com/', $host)){
    $base = 'http://localhost/';
}
if(preg_match('/git.gitftp.com/', $host)){
    $base = 'http://gitftp.com/';
}
if(preg_match('/gitftp.com/', $host)){
    $base = 'http://git.gitftp.com/';
}

define('base', $base);
echo base;
define('is_dash', $is_dash);
return array(
    '_root_' => $controller, // The default route
    'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
    '_404_' => 'new/pages/404', // The main 404 route
);
