<?php

namespace pgddevil\Tools\HarvestImporter\Model;

/**
 * @Entity @Table(name="Project")
 */
class Project
{
    /**
     * @var int
     * @Id @Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @Column(type="string")
     */
    private $name;

    /**
     * @var int
     * @Column(type="integer")
     */
    private $clientId;

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
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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