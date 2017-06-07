<?php

namespace pgddevil\Tools\HarvestImporter\Model;

/**
 * @Entity @Table(name="Client")
 */
class Client
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

}