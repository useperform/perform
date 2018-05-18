<?php

namespace Perform\RichContentBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Type\AbstractType;
use Perform\RichContentBundle\Form\Type\RichContentType as FormType;
use Perform\BaseBundle\Crud\CrudRequest;

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
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getDefaultConfig()
    {
        return [
            'contexts' => [
                CrudRequest::CONTEXT_VIEW,
                CrudRequest::CONTEXT_CREATE,
                CrudRequest::CONTEXT_EDIT,
            ],
            'sort' => false,
            'template' => '@PerformRichContent/type/rich_content.html.twig',
        ];
    }
}
