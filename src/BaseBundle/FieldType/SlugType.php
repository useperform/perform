<?php

namespace Perform\BaseBundle\FieldType;

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
        $this->assets = $assets;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $this->assets->addJs('/bundles/performbase/js/types/slug.js');
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, FormType::class, array_merge($formOptions, $options['form_options']));

        return [
            'target' => sprintf('#%s_%s', $builder->getName(), $options['target']),
            'readonly' => $options['readonly'],
        ];
    }

    /**
     * @doc target The name of the entity field to generate the slug
     * from, e.g. 'title'.
     * The field must exist for the current context.
     *
     * @doc readonly Set the readonly attribute on the input element.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'readonly' => false,
        ]);
        $resolver->setRequired('target');
        $resolver->setAllowedTypes('target', ['string']);
        $resolver->setAllowedTypes('readonly', ['boolean']);
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/slug.html.twig',
        ];
    }
}
