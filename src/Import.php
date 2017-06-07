<?php

namespace pgddevil\Tools\HarvestImporter;

use Doctrine\ORM\EntityManager;
use pgddevil\Tools\Harvest\Client;
use pgddevil\Tools\HarvestImporter\Model\DayEntry;
use pgddevil\Tools\HarvestImporter\Model\Task;
use pgddevil\Tools\HarvestImporter\Model\User;
use Psr\Log\LoggerInterface;

class Import
{
    /**
     * @var \pgddevil\Tools\Harvest\Client
     */
    private $sourceGateway;
    /**
     * @var EntityManager
     */
    private $targetGateway;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(\pgddevil\Tools\Harvest\Client $sourceGateway, EntityManager $targetGateway, LoggerInterface $logger)
    {
        $this->targetGateway = $targetGateway;
        $this->sourceGateway = $sourceGateway;
        $this->logger = $logger;
    }

    public function import(\DateTimeImmutable $fromDate, \DateTimeImmutable $toDate)
    {
        $this->logger->notice("Importing entries between {$fromDate->format('Y-m-d')} and {$toDate->format('Y-m-d')} inclusive");

        $sourceProjects = $this->sourceGateway->getActiveProjects();
        foreach ($sourceProjects as $sourceProject) {
            /** @var \pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject */
            $sourceEntries = $this->sourceGateway->getTimeEntriesForProject($sourceProject->id, $fromDate, $toDate);
            foreach ($sourceEntries as $sourceEntry) {
                /** @var \pgddevil\Tools\Harvest\Model\DayEntry $sourceEntry */
                $this->importDayEntry($sourceEntry);
            }

            $this->targetGateway->flush();
        }

        $this->logger->info("import completed!");
    }

    private function importDayEntry(\pgddevil\Tools\Harvest\Model\DayEntry $sourceEntry)
    {
        $user = $this->importUser($sourceEntry->userId);
        $task = $this->importTask($sourceEntry->taskId);
        $project = $this->importProject($sourceEntry->projectId);

        $entry = $this->getDayEntry($sourceEntry->id);
        if ($entry == null) {

            $entry = new DayEntry();
            $entry->setId($sourceEntry->id);
            $entry->setHours($sourceEntry->hours);
            $entry->setNotes($sourceEntry->notes);
            $entry->setProjectId($project->getId());
            $entry->setTaskId($task->getId());
            $entry->setUserId($user->getId());
            $entry->setSpentAt($sourceEntry->spentAt);

            $this->targetGateway->persist($entry);

            $this->logger->info("Import Entry: {$sourceEntry->spentAt->format('Y-m-d')}, {$sourceEntry->id}");
        } else {
            $this->logger->debug("Entry already exists: {$sourceEntry->spentAt->format('Y-m-d')}, {$sourceEntry->id}");
        }
    }

    /**
     * @param $id
     * @return null|\pgddevil\Tools\HarvestImporter\Model\DayEntry
     */
    private function getDayEntry($id) {
        return $this->targetGateway->find(DayEntry::class, $id);
    }

    /**
     * @param $id
     * @return null|\pgddevil\Tools\HarvestImporter\Model\Task
     */
    private function getTask($id) {
        return $this->targetGateway->find(Task::class, $id);
    }

    /**
     * @param \pgddevil\Tools\Harvest\Model\TaskEntry $sourceTask
     * @return \pgddevil\Tools\HarvestImporter\Model\Task
     */
    private function createTask(\pgddevil\Tools\Harvest\Model\TaskEntry $sourceTask)
    {
        $task = new \pgddevil\Tools\HarvestImporter\Model\Task();
        $task->setId($sourceTask->id);
        $task->setName($sourceTask->name);
        $this->targetGateway->persist($task);

        return $task;
    }

    /**
     * @param $id
     * @return null|Client
     */
    private function getClient($id) {
        return $this->targetGateway->find(\pgddevil\Tools\HarvestImporter\Model\Client::class, $id);
    }

    /**
     * @param int $id
     * @return Client
     */
    private function importClient($id)
    {
        $client = $this->getClient($id);
        if ($client == null) {
            $sourceClient = $this->sourceGateway->getClient($id);
            $client = $this->createClient($sourceClient);
            $this->logger->info("Client created: {$client->getName()}");
        } else {
            $this->logger->debug("Client already exists: {$client->getName()}");
        }

        return $client;
    }

    /**
     * @param \pgddevil\Tools\Harvest\Model\ClientEntry $sourceClient
     * @return Client
     */
    private function createClient(\pgddevil\Tools\Harvest\Model\ClientEntry $sourceClient)
    {
        $client = new \pgddevil\Tools\HarvestImporter\Model\Client();
        $client->setId($sourceClient->id);
        $client->setName($sourceClient->name);
        $this->targetGateway->persist($client);

        return $client;
    }

    /**
     * @param int $projectId
     * @return Project
     */
    private function importProject($projectId)
    {
        $project = $this->getProject($projectId);
        if ($project == null) {
            $sourceProject = $this->sourceGateway->getProject($projectId);
            $this->importClient($sourceProject->clientId);

            $project = $this->createProject($sourceProject);
            $this->logger->info("Project created: {$project->getName()}");
        } else {
            $this->logger->debug("Project already exists: {$project->getName()}");
        }

        return $project;
    }

    /**
     * @param $id
     * @return null|Project
     */
    private function getProject($id) {
        return $this->targetGateway->find(\pgddevil\Tools\HarvestImporter\Model\Project::class, $id);
    }

    /**
     * @param \pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject
     * @return Project
     */
    private function createProject(\pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject)
    {
        $project = new \pgddevil\Tools\HarvestImporter\Model\Project();
        $project->setId($sourceProject->id);
        $project->setName($sourceProject->name);
        $project->setClientId($sourceProject->clientId);
        $this->targetGateway->persist($project);

        return $project;
    }

    private function importTask($taskId)
    {
        $task = $this->getTask($taskId);
        if ($task == null) {
            $sourceTask = $this->sourceGateway->getTask($taskId);
            $task = $this->createTask($sourceTask);
            $this->logger->info("Task created: {$task->getName()}");
        } else {
            $this->logger->debug("Task already exists: {$task->getName()}");
        }

        return $task;
    }

    private function importUser($userId)
    {
        $user = $this->getUser($userId);
        if ($user == null) {
            $sourceUser = $this->sourceGateway->getUser($userId);
            $user = $this->createUser($sourceUser);
            $this->logger->info("User created: {$user->getName()}");
        } else {
            $this->logger->debug("User already exists: {$user->getName()}");
        }

        return $user;
    }

    /**
     * @param $id
     * @return null|User
     */
    private function getUser($id) {
        return $this->targetGateway->find(\pgddevil\Tools\HarvestImporter\Model\User::class, $id);
    }

    private function createUser(\pgddevil\Tools\Harvest\Model\UserEntry $sourceUser)
    {
        $user = new \pgddevil\Tools\HarvestImporter\Model\User();
        $user->setId($sourceUser->id);
        $user->setName($sourceUser->firstName . " " . $sourceUser->lastName);
        $user->setEmail($sourceUser->email);

        if (!empty($sourceUser->department)) {
            $segments = explode(' - ',$sourceUser->department, 2);
            if (count($segments) > 1) {
                $user->setDepartment(trim($segments[0]));
                $user->setTeam(trim($segments[1]));
            } else {
                $user->setDepartment($sourceUser->department);
            }
        }

        $this->targetGateway->persist($user);

        return $user;
    }
}