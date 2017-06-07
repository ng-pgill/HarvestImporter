<?php

require_once __DIR__ . '/bootstrap.php';

$entityManager = initDoctrine($config->DBConnection);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);