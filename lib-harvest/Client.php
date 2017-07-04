<?php

namespace pgddevil\Tools\Harvest;

use pgddevil\Tools\Harvest\Model\ClientEntry;
use pgddevil\Tools\Harvest\Model\ProjectEntry;
use pgddevil\Tools\Harvest\Model\DayEntry;
use pgddevil\Tools\Harvest\Model\TaskEntry;
use pgddevil\Tools\Harvest\Model\UserEntry;

class Client
{
    /** @var Requester  */
    private $requester;
    /** @var string */
    private $accountName;

    public function __construct($accountName, Requester $requester)
    {
        $this->requester = $requester;
        $this->accountName = $accountName;
    }

    public function whoAmI()
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/account/who_am_i"));
        return json_decode($response, true);
    }

    /**
     * @param int $userId
     * @return UserEntry
     */
    public function getUser($userId)
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/people/{$userId}"));

        $jsonResult = json_decode($response, true);
        $entry = $this->loadUserEntry($jsonResult);

        return $entry;
    }

    /**
     * @return \Iterator
     */
    public function getActiveUsers()
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/people"));

        $jsonResult = json_decode($response, true);
        $result = array();
        foreach ($jsonResult as $jsonEntry) {
            if ($jsonEntry['user']['is_active']) {
                $entry = $this->loadUserEntry($jsonEntry);

                $result[] = $entry;
            }
        }
        return new \ArrayIterator($result);

    }

    /**
     * @param int $projectId
     * @return ProjectEntry
     */
    public function getProject($projectId)
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/projects/{$projectId}"));

        $jsonResult = json_decode($response, true);
        $entry = $this->loadProjectEntry($jsonResult);
        return $entry;
    }

    /**
     * @return \Iterator
     */
    public function getActiveProjects()
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/projects"));

        $jsonResult = json_decode($response, true);
        $result = array();
        foreach ($jsonResult as $projectData) {
            if ($projectData['project']['active']) {
                $entry = $this->loadProjectEntry($projectData);

                $result[] = $entry;
            }
        }
        return new \ArrayIterator($result);
    }

    /**
     * @param $projectId
     * @param \DateTimeImmutable $fromDate
     * @param \DateTimeImmutable $toDate
     * @return \Iterator
     */
    public function getTimeEntriesForProject($projectId, \DateTimeImmutable $fromDate, \DateTimeImmutable $toDate)
    {
        $fromFilter = $fromDate->format("Ymd");
        $toFilter = $toDate->format("Ymd");

        $response = $this->requester->getRequest($this->getHarvestUrl("/projects/{$projectId}/entries?from={$fromFilter}&to={$toFilter}"));
        $jsonResult = json_decode($response, true);
        $result = array();
        foreach ($jsonResult as $entryData) {
            $result[] = $this->loadTimeEntry($entryData);
        }

        return new \ArrayIterator($result);
    }

    /**
     * @param int $taskId
     * @return TaskEntry
     */
    public function getTask($taskId)
    {
        $response = $this->requester->getRequest($this->getHarvestUrl("/tasks/{$taskId}"));
        $jsonResult = json_decode($response, true);
        $entry = $this->loadTaskEntry($jsonResult);
        return $entry;
    }

    /**
     * @param int $id
     * @return ClientEntry
     */
    public function getClient($id)
    {
        $response = $this->requester->getRequest($this->getHarvestUrl("/clients/{$id}"));
        $jsonResult = json_decode($response, true);
        $entry = $this->loadClientEntry($jsonResult);
        return $entry;
    }

    /**
     * @return \Iterator
     */
    public function getActiveTasks()
    {
        $response = $this->requester->getRequest($this->getHarvestUrl("/tasks"));

        $jsonResult = json_decode($response, true);
        $result = array();
        foreach ($jsonResult as $taskData) {
            if (!$taskData['task']['deactivated']) {
                $entry = $this->loadTaskEntry($taskData);

                $result[] = $entry;
            }
        }
        return new \ArrayIterator($result);
    }

    private function getHarvestUrl($path)
    {
        return "https://{$this->accountName}.harvestapp.com{$path}";
    }

    /**
     * @param $sourceData
     * @return ClientEntry
     */
    private function loadClientEntry($sourceData)
    {
        $source = $sourceData['client'];

        $entry = new ClientEntry();
        $entry->id = $source['id'];
        $entry->name = $source['name'];
        $entry->createdAt = new \DateTimeImmutable($source['created_at']);
        $entry->updatedAt = new \DateTimeImmutable($source['updated_at']);

        return $entry;
    }

    /**
     * @param $sourceData
     * @return UserEntry
     */
    private function loadUserEntry($sourceData)
    {
        $source = $sourceData['user'];

        $entry = new Model\UserEntry();
        $entry->id = $source['id'];
        $entry->email = $source['email'];
        $entry->firstName = $source['first_name'];
        $entry->lastName = $source['last_name'];
        $entry->department = $source['department'];
        $entry->createdAt = new \DateTimeImmutable($source['created_at']);
        $entry->updatedAt = new \DateTimeImmutable($source['updated_at']);

        return $entry;
    }

    /**
     * @param $sourceData
     * @return TaskEntry
     */
    private function loadTaskEntry($sourceData)
    {
        $source = $sourceData['task'];

        $entry = new TaskEntry();
        $entry->id = $source['id'];
        $entry->name = $source['name'];
        $entry->createdAt = new \DateTimeImmutable($source['created_at']);
        $entry->updatedAt = new \DateTimeImmutable($source['updated_at']);
        return $entry;
    }

    /**
     * @param $sourceData
     * @return ProjectEntry
     */
    private function loadProjectEntry($sourceData)
    {
        $source = $sourceData['project'];

        $entry = new ProjectEntry();
        $entry->id = $source['id'];
        $entry->name = $source['name'];
        $entry->clientId = $source['client_id'];
        $entry->createdAt = new \DateTimeImmutable($source['created_at']);
        $entry->updatedAt = new \DateTimeImmutable($source['updated_at']);

        return $entry;
    }

    /**
     * @param $sourceData
     * @return DayEntry
     */
    private function loadTimeEntry($sourceData)
    {
        $source = $sourceData['day_entry'];

        $entry = new DayEntry();
        $entry->id = $source['id'];
        $entry->hours = $source['hours'];
        $entry->notes = $source['notes'];
        $entry->projectId = $source['project_id'];
        $entry->taskId = $source['task_id'];
        $entry->userId = $source['user_id'];
        $entry->spentAt = new \DateTimeImmutable($source['spent_at']);
        $entry->createdAt = new \DateTimeImmutable($source['created_at']);
        $entry->updatedAt = new \DateTimeImmutable($source['updated_at']);

        return $entry;
    }
}