<?php

namespace pgddevil\Tools\HarvestImporter;

use Doctrine\ORM\EntityManager;
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
        $this->importUsers();
        $this->importProjects();
        $this->importTasks();
        $this->importDayEntries($fromDate, $toDate);
    }

    private function importDayEntries(\DateTimeImmutable $fromDate, \DateTimeImmutable $toDate)
    {
        $this->logger->info("Importing day entries...");

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

        $this->logger->info("Importing day entries - completed");
    }

    private function importDayEntry(\pgddevil\Tools\Harvest\Model\DayEntry $sourceEntry)
    {
        $entry = $this->getDayEntry($sourceEntry->id);
        if ($entry == null) {
            $entry = new DayEntry();
            $entry->setId($sourceEntry->id);
            $entry->setHours($sourceEntry->hours);
            $entry->setNotes($sourceEntry->notes);
            $entry->setProjectId($sourceEntry->projectId);
            $entry->setTaskId($sourceEntry->taskId);
            $entry->setUserId($sourceEntry->userId);
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
     * @param \pgddevil\Tools\Harvest\Model\TaskEntry $sourceTask
     * @return Task
     */
    private function importTask(\pgddevil\Tools\Harvest\Model\TaskEntry $sourceTask)
    {
        $task = $this->getTask($sourceTask->id);
        if ($task == null) {
            $task = $this->createTask($sourceTask);
            $this->logger->info("Task created: {$sourceTask->name}");
        } else {
            $this->logger->debug("Task already exists: {$sourceTask->name}");
        }

        return $task;
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
     * @param \pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject
     * @return Project
     */
    private function importProject(\pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject)
    {
        $project = $this->getProject($sourceProject->id);
        if ($project == null) {
            $project = $this->createProject($sourceProject);
            $this->logger->info("Project created: {$sourceProject->name}");
        } else {
            $this->logger->debug("Project already exists: {$sourceProject->name}");
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

    private function importProjects()
    {
        $this->logger->info("Importing projects...");
        $sourceProjects = $this->sourceGateway->getActiveProjects();
        foreach ($sourceProjects as $sourceProject) {
            /** @var \pgddevil\Tools\Harvest\Model\ProjectEntry $sourceProject */

            $this->importProject($sourceProject);
        }

        $this->targetGateway->flush();
        $this->logger->info("Importing projects - completed");
    }

    private function importTasks()
    {
        $this->logger->info("Importing tasks...");
        $sourceTasks = $this->sourceGateway->getActiveTasks();
        foreach ($sourceTasks as $sourceTask) {
            /** @var \pgddevil\Tools\Harvest\Model\TaskEntry $sourceTask */

            $this->importTask($sourceTask);
        }

        $this->targetGateway->flush();
        $this->logger->info("Importing tasks - completed");
    }

    private function importUsers()
    {
        $this->logger->info("Importing users...");
        $sourceUsers = $this->sourceGateway->getActiveUsers();
        foreach ($sourceUsers as $sourceUser) {
            /** @var \pgddevil\Tools\Harvest\Model\UserEntry $sourceUser */

            $this->importUser($sourceUser);
        }

        $this->targetGateway->flush();
        $this->logger->info("Importing users - completed");
    }

    private function importUser(\pgddevil\Tools\Harvest\Model\UserEntry $sourceUser)
    {
        $user = $this->getUser($sourceUser->id);
        if ($user == null) {
            $user = $this->createUser($sourceUser);
            $this->logger->info("User created: {$sourceUser->firstName} {$sourceUser->lastName}");
        } else {
            $this->logger->debug("User already exists: {$sourceUser->firstName} {$sourceUser->lastName}");
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
        $this->targetGateway->persist($user);

        return $user;
    }
}