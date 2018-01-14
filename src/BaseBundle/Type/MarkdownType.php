<?php

namespace Perform\BaseBundle\Type;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Form\Extension\Core\Type\TextareaType as FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Use the ``markdown`` type for entity properties containing markdown
 * text.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MarkdownType extends AbstractType
{
    protected $markdown;

    public function __construct(CommonMarkConverter $markdown)
    {
        parent::__construct();
        $this->markdown = $markdown;
    }

    public function listContext($entity, $field, array $options = [])
    {
        $markdown = $this->accessor->getValue($entity, $field);

        return [
            'markdown' => $markdown,
            'html' => $this->markdown->convertToHtml($markdown),
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class);
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/type/markdown.html.twig',
        ];
    }

}
