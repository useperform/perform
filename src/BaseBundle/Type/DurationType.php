<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\DurationType as FormType;
use Perform\BaseBundle\Util\DurationUtil;

/**
 * DurationType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DurationType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        return DurationUtil::toHuman($this->accessor->getValue($entity, $field));
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, []);
    }

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:duration.html.twig';
    }
}
