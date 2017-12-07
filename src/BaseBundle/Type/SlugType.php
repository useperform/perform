<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * Use the ``slug`` type for representing an entity in a URL.
 *
 * The slug is dynamically generated from another property, but can
 * optionally be set manually.
 *
 * Recommended doctrine field type: ``string``
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SlugType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        parent::__construct();
        $this->assets = $assets;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $this->assets->addJs('/bundles/performbase/js/types/slug.js');
        $builder->add($field, FormType::class, [
            'label' => $options['label'],
        ]);

        return [
            'target' => sprintf('#%s_%s', $builder->getName(), $options['target']),
            'edit' => $options['edit'],
        ];
    }

    /**
     * @doc target The name of the entity field to generate the slug
     * from, e.g. 'title'.
     * The field must exist for the current context.
     *
     * @doc edit Allow the input field to be edited.
     * If false, the input will be disabled.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'edit' => true,
        ]);
        $resolver->setRequired('target');
        $resolver->setAllowedTypes('target', ['string']);
        $resolver->setAllowedTypes('edit', ['boolean']);
    }

    public function getTemplate()
    {
        return 'PerformBaseBundle:types:slug.html.twig';
    }
}
