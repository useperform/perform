<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Asset\AssetContainer;

/**
 * Use the ``html`` type for entity properties storing html.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HtmlType extends AbstractType
{
    protected $assets;

    public function __construct(AssetContainer $assets)
    {
        parent::__construct();
        $this->assets = $assets;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $this->assets->addCss('https://cdn.quilljs.com/1.3.5/quill.snow.css');
        $this->assets->addJs('https://cdn.quilljs.com/1.3.5/quill.js');
        $this->assets->addJs('/bundles/performbase/js/types/html.js');
        $builder->add($field, HiddenType::class);

        return [
            'html' => $this->accessor->getValue($builder->getData(), $field),
        ];
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/type/html.html.twig',
        ];
    }
}
