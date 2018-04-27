<?php

namespace Perform\SpamBundle\Entity;

class Report
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     *
     * @return SpamType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
