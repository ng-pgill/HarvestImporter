<?php

namespace pgddevil\Tools\HarvestImporter\Model;

/**
 * @Entity @Table(name="DayEntry")
 */
class DayEntry
{
    /**
     * @var int
     * @Id @Column(type="integer")
     */
    private $id;

    /**
     * @var double
     * @Column(type="float")
     */
    public $hours;

    /**
     * @var string
     * @Column(type="string",nullable=true,length=500)
     */
    public $notes;

    /**
     * @var int
     * @Column(type="integer")
     */
    public $projectId;

    /**
     * @var int
     * @Column(type="integer")
     */
    public $taskId;

    /**
     * @var int
     * @Column(type="integer")
     */
    private $userId;

    /**
     * @var \DateTimeImmutable
     * @Column(type="datetime")
     */
    public $spentAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param float $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSpentAt()
    {
        return $this->spentAt;
    }

    /**
     * @param \DateTimeImmutable $spentAt
     */
    public function setSpentAt($spentAt)
    {
        $this->spentAt = $spentAt;
    }


}