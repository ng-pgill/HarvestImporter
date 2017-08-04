<?php
/*
             DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.
 */

$config = new stdClass();

$config->DBConnection =  array(
        'driver' => 'pdo_mysql',
        'dbname' => 'harvest',
        'user' => 'root',
        'password' => 'root',
        'host' => '127.0.0.1',
        'charset' => 'utf8'
    );

$config->harvestAccountName = 'myaccountname';

$config->harvestCredentials = array(
    'username' => 'myaccount@mycompany.com',
    'password' => 'mypassword');