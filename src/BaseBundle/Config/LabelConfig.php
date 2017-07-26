<?php

namespace Perform\BaseBundle\Config;

use Doctrine\Common\Inflector\Inflector;

/**
 * Configure how an entity and group of entities are labelled.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LabelConfig
{
    protected $entityLabel;
    protected $entityName;
    protected $entityNamePlural;

    /**
     * @param \Closure $entityLabel
     *
     * @return LabelConfig
     */
    public function setEntityLabel(\Closure $entityLabel)
    {
        $this->entityLabel = $entityLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityLabel($entity)
    {
        return $this->entityLabel ? call_user_func($this->entityLabel, $entity) : '';
    }

    /**
     * @param string $entityName
     *
     * @return LabelConfig
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return (string) $this->entityName;
    }

    /**
     * @param string $entityNamePlural
     */
    public function setEntityNamePlural($entityNamePlural)
    {
        $this->entityNamePlural = $entityNamePlural;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityNamePlural()
    {
        if ($this->entityNamePlural) {
            return $this->entityNamePlural;
        }

        $singular = $this->getEntityName();

        return Inflector::pluralize($singular);
    }
}
