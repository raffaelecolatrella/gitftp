<?php
/**
 * The staging database settings. These get merged with the global settings.
 */

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'mysql:host=52.28.221.93;dbname=craftrzt_gitftp',
            'username' => 'craftrzt_gitftp',
            'password' => 's.AmN-V?rXCT',
        ),
    ),
    'frontend' => array(
        'type'         => 'mysqli',
        'connection'   => array(
            'hostname' => 'craftpip.com',
            'port'     => '3306',
            'database' => 'craftrzt_gitftp_frontend',
            'username' => 'craftrzt_gitftp',
            'password' => 's.AmN-V?rXCT',
        ),
        'table_prefix' => '',
    ),
);
