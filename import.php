<?php

require_once (__DIR__ . '/bootstrap.php');

$completionFile = __DIR__ . "/last_completion.txt";

if ($argc >= 2 && $argv[1] !== '.') {
    $fromDate = new \DateTimeImmutable($argv[1]);
} else if (file_exists($completionFile)) {
    $fromDate = new \DateTimeImmutable(file_get_contents($completionFile));
} else {
    throw new \RuntimeException("No date to start from.  Please specify it as the first argument with this format: YYYY-MM-DD.  You can use the '.' to start from last completion date.");
}

if ($argc >= 3) {
    $toDate = new \DateTimeImmutable($argv[2]);
} else {
    $toDate = new \DateTimeImmutable("now");
}

$log = new Monolog\Logger('import');
$log->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::INFO));

$requester = new \pgddevil\Tools\Harvest\BasicAuthRequester($config->harvestCredentials['username'], $config->harvestCredentials['password']);
$client = new \pgddevil\Tools\Harvest\Client($config->harvestAccountName, $requester);

$importer = new \pgddevil\Tools\HarvestImporter\Import($client, $entityManager, $log);

$log->notice("Importing entries between {$fromDate->format('Y-m-d')} and {$toDate->format('Y-m-d')} inclusive");
$importer->import($fromDate, $toDate);

file_put_contents($completionFile, $toDate->format('Y-m-d'));