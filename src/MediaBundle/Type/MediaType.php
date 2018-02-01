<?php

namespace Perform\MediaBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Perform\BaseBundle\Type\AbstractType;
use Perform\MediaBundle\Entity\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\MediaBundle\Exception\PluginNotFoundException;
use Perform\BaseBundle\Asset\AssetContainer;
use Perform\BaseBundle\Form\Type\HiddenEntityType;
use Perform\MediaBundle\MediaType\MediaTypeRegistry;

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
    protected $assets;

    public function __construct(MediaTypeRegistry $registry, AssetContainer $assets)
    {
        $this->registry = $registry;
        $this->assets = $assets;
        parent::__construct();
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        if ($options['use_selector']) {
            $this->assets->addJs('/bundles/performmedia/app.js');
            $this->assets->addJs('/bundles/performmedia/editor.js');
            $builder->add($field, HiddenEntityType::class, [
                'class' => 'PerformMediaBundle:File',
            ]);

            return [
                'use_selector' => true,
                'file' => $this->accessor->getValue($builder->getData(), $field),
            ];
        }

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

        return [
            'use_selector' => false,
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $this->assets->addJs('/bundles/performmedia/app.js');
        $this->assets->addJs('/bundles/performmedia/editor.js');
        $file = $this->accessor->getValue($entity, $field);

        if ($file && !$file instanceof File) {
            throw new \Exception('Must be a file or null');
        }

        return [
            'file' => $file,
        ];
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
     * @doc use_selector If true, use the media selector modal to
     * choose media.
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
        $resolver->setDefault('use_selector', true);
        $resolver->setAllowedTypes('use_selector', ['boolean']);
    }
}
