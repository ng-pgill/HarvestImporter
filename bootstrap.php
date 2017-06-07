<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');

function initDoctrine($dbConnection)
{
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/Model"), $isDevMode);
    return EntityManager::create($dbConnection, $config);
}

$entityManager = initDoctrine($config->DBConnection);
