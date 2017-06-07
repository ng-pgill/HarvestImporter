<?php

namespace pgddevil\Tools\Harvest;

use pgddevil\Tools\Harvest\Model\ProjectEntry;
use pgddevil\Tools\Harvest\Model\DayEntry;
use pgddevil\Tools\Harvest\Model\TaskEntry;

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

    public function getProject($projectId)
    {
        $response = $this->requester->getRequest($this->getHarvestUrl( "/projects/{$projectId}"));
        $result = json_decode($response, true);
        return $result;
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
                $entry = new ProjectEntry();
                $entry->id = $projectData['project']['id'];
                $entry->name = $projectData['project']['name'];
                $entry->clientId = $projectData['project']['client_id'];

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
            $entry = new DayEntry();
            $entry->id = $entryData['day_entry']['id'];
            $entry->hours = $entryData['day_entry']['hours'];
            $entry->notes = $entryData['day_entry']['notes'];
            $entry->projectId = $entryData['day_entry']['project_id'];
            $entry->taskId = $entryData['day_entry']['task_id'];
            $entry->userId = $entryData['day_entry']['user_id'];
            $entry->spentAt = new \DateTimeImmutable($entryData['day_entry']['spent_at']);

            $result[] = $entry;
        }

        return new \ArrayIterator($result);
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
                $entry = new TaskEntry();
                $entry->id = $taskData['task']['id'];
                $entry->name = $taskData['task']['name'];

                $result[] = $entry;
            }
        }
        return new \ArrayIterator($result);
    }

    private function getHarvestUrl($path)
    {
        return "https://{$this->accountName}.harvestapp.com{$path}";
    }
}