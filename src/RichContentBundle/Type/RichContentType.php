<?php

namespace Perform\RichContentBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\AbstractType;
use Perform\BaseBundle\Asset\AssetContainer;
use Perform\BaseBundle\Form\Type\HiddenEntityType;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * Use the ``rich_content`` type for linking to rich content.
 *
 * @example
 * $config->add('content', [
 *     'type' => 'rich_content',
 *     'options' => [
 *         'versioned' => true,
 *         'block_types' => [],
 *     ],
 * ]);
 *
 * @doctrineType a unidirectional manyToOne relation with PerformRichContentBundle:Content
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RichContentType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        parent::__construct();
        $this->assets = $assets;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $this->assets->addCss('/bundles/performrichcontent/editor.css');
        $this->assets->addJs('/bundles/performrichcontent/editor.js');
        $this->assets->addJs('/bundles/performrichcontent/type.js');

        $builder->add($field, HiddenEntityType::class, [
            'class' => 'PerformRichContentBundle:Content',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getDefaultConfig()
    {
        return [
            'contexts' => [
                TypeConfig::CONTEXT_VIEW,
                TypeConfig::CONTEXT_CREATE,
                TypeConfig::CONTEXT_EDIT,
            ],
            'sort' => false,
            'template' => '@PerformRichContent/type/rich_content.html.twig',
        ];
    }
}
