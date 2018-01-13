<?php

namespace Perform\MediaBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Perform\BaseBundle\Type\AbstractType;
use Perform\MediaBundle\Plugin\PluginRegistry;
use Perform\MediaBundle\Entity\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\MediaBundle\Exception\PluginNotFoundException;

/**
 * Use the ``media`` type to link to a file in the media library.
 *
 * @example
 * $config->add('image', [
 *     'type' => 'media',
 *     'options' => [
 *         'types' => 'image',
 *         // same as
 *         // 'types' => ['image'],
 *     ],
 *     'contexts' => [
 *         TypeConfig::CONTEXT_LIST,
 *         TypeConfig::CONTEXT_EDIT,
 *     ],
 * ])
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
        $availableTypes = $this->registry->getPluginNames();
        $types = empty($options['types']) ? $availableTypes : $options['types'];

        $unknownTypes = array_values(array_diff($types, $availableTypes));
        if (!empty($unknownTypes)) {
            throw new PluginNotFoundException(sprintf('Unknown media plugin "%s"', $unknownTypes[0]));
        }

        $builder->add($field, EntityType::class, [
            'label' => $options['label'],
            'class' => 'PerformMediaBundle:File',
            'choice_label' => 'name',
            'placeholder' => 'None',
            'required' => false,
            'query_builder' => function ($repo) use ($types) {
                return $repo->createQueryBuilder('f')
                    ->where('f.type IN (:types)')
                    ->setParameter('types', $types);
            },
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
            'template' => '@PerformMedia/type/media.html.twig',
            'sort' => false,
        ];
    }

    /**
     * @doc types The type of media to choose from.
     * Each entry should refer to the name of a plugin.
     *
     * You may use a bare string instead of an array to use only one
     * plugin.
     *
     * If no types are supplied, all media will be available.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('types', []);
        $resolver->setAllowedTypes('types', ['array', 'string']);
        $resolver->setNormalizer('types', function ($options, $val) {
            return (array) $val;
        });
    }
}
