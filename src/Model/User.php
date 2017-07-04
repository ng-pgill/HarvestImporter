<?php

namespace pgddevil\Tools\HarvestImporter\Model;

/**
 * @Entity @Table(name="User")
 */
class User
{
    /**
     * @var int
     * @Id @Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @Column(type="string",length=100)
     */
    private $email;

    /**
     * @var int
     * @Column(type="string",length=255)
     */
    private $name;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    private $department;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    private $team;

    /**
     * @var \DateTimeImmutable
     * @Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @Column(type="datetime")
     */
    private $updatedAt;

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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param string $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return string
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param string $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }


}