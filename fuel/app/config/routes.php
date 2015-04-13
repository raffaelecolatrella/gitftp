<?php

/**
 * Hosting configuration for homepage and dashboard application.
 */
$host = $_SERVER['HTTP_HOST'];
if($host == 'git.gitftp.com'){
    $is_dash = true;
    $controller = 'dashboard/index';
}else{
    $controller = 'welcome/index';
    $is_dash = false;
}
define('SITE', $is_dash);

return array(
    '_root_' => $controller, // The default route
    'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
    '_404_' => 'new/pages/404', // The main 404 route
);
