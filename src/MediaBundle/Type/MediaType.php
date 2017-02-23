<?php

namespace Perform\MediaBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Perform\BaseBundle\Type\AbstractType;
use Perform\MediaBundle\Plugin\PluginRegistry;
use Perform\BaseBundle\Type\TypeConfig;
use Perform\MediaBundle\Entity\File;

/**
 * MediaType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MediaType extends AbstractType
{
    protected $registry;

    public function __construct(PluginRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct();
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $types = (array) (isset($options['types']) ? $options['types'] : $this->registry->getPluginNames());

        $builder->add($field, EntityType::class, [
            'label' => $options['label'],
            'class' => 'PerformMediaBundle:File',
            'choice_label' => 'name',
            'placeholder' => 'None',
            'required' => false,
            'query_builder' => function($repo) use ($types) {
                return $repo->createQueryBuilder('f')
                    ->where('f.type IN (:types)')
                    ->setParameter('types', $types);
            }
        ]);
    }

    public function listContext($entity, $field, array $options = [])
    {
        $file = $this->accessor->getValue($entity, $field);

        if (!$file instanceof File) {
            return 'None';
        }

        return $this->registry->getPreview($file, ['size' => 'small']);
    }

    public function getDefaultConfig()
    {
        return [
            'sort' => false,
        ];
    }
}
