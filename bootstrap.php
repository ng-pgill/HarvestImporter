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

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');

function initDoctrine($dbConnection, $logger = false)
{
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/Model"), $isDevMode);
    if ($logger) {
        $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }
    return EntityManager::create($dbConnection, $config);
}
