<?php

namespace Perform\BaseBundle\FieldType;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Form\Extension\Core\Type\TextareaType as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * Use the ``markdown`` type for entity properties containing markdown
 * text.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownType extends AbstractType
{
    protected $markdown;
    protected $assets;

    public function __construct(CommonMarkConverter $markdown, AssetContainer $assets)
    {
        $this->markdown = $markdown;
        $this->assets = $assets;
    }

    public function listContext($entity, $field, array $options = [])
    {
        $markdown = $this->getPropertyAccessor()->getValue($entity, $field);

        return [
            'markdown' => $markdown,
            'html' => $this->markdown->convertToHtml($markdown),
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $this->assets->addJs('/bundles/performbase/js/types/markdown.js');
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, FormType::class, array_merge($formOptions, $options['form_options']));

        return $options;
    }

    /**
     * @doc live_preview If true, show a live HTML preview next to the form field.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'live_preview' => true,
        ]);
        $resolver->setAllowedTypes('live_preview', 'boolean');
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/markdown.html.twig',
        ];
    }

}
