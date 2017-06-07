<?php

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