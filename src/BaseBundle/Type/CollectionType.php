<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Perform\BaseBundle\Exception\InvalidTypeException;

/**
 * CollectionType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
    }

    public function viewContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $text = '';
        foreach ($collection as $entity) {
            $text .= $entity->getId();
        }

        return $text;
    }

    public function listContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $itemLabel = isset($options['itemLabel']) ? (array) $options['itemLabel'] : ['item'];
        $label = $itemLabel[0];
        $count = count($collection);
        if ($count > 1) {
            $label = isset($itemLabel[1]) ? $itemLabel[1] : $label.'s';
        }

        return $count.' '.trim($label);
    }

    protected function ensureCollection($value)
    {
        if (!$value instanceof Collection) {
            throw new InvalidTypeException(sprintf('The entity field "%s" passed to %s must be an instance of %s', $field, __CLASS__, Collection::class));
        }
    }
}
