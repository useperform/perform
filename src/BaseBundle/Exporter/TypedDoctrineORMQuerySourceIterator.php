<?php

namespace Perform\BaseBundle\Exporter;

use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Doctrine\ORM\Query;
use Exporter\Source\SourceIteratorInterface;

/**
 * Format entity properties from a doctrine query with the
 * TypeRegistry and set the label.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypedDoctrineORMQuerySourceIterator implements SourceIteratorInterface
{
    protected $typeRegistry;
    protected $query;
    protected $exportFields;
    protected $iterator;

    /**
     * @param array $exportFields An array of configure types returned from FieldConfig with CONTEXT_EXPORT
     */
    public function __construct(FieldTypeRegistry $typeRegistry, Query $query, array $exportFields)
    {
        $this->typeRegistry = $typeRegistry;
        $this->query = clone $query;
        $this->query->setParameters($query->getParameters());
        foreach ($query->getHints() as $name => $value) {
            $this->query->setHint($name, $value);
        }
        $this->exportFields = $exportFields;
    }

    public function current()
    {
        $entity = $this->iterator->current()[0];
        $data = [];

        foreach ($this->exportFields as $field => $options) {
            try {
                $data[$options['exportOptions']['label']] = $this->typeRegistry->getType($options['type'])->exportContext($entity, $field, $options['exportOptions']);
            } catch (\Exception $e) {
                $data[$options['exportOptions']['label']] = '';
            }
        }

        $this->query->getEntityManager()->getUnitOfWork()->detach($entity);

        return $data;
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        if ($this->iterator) {
            throw new \RuntimeException('Unable to rewind a Doctrine database query.');
        }

        $this->iterator = $this->query->iterate();
        $this->iterator->rewind();
    }
}
