<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Util\StringUtil;

/**
 * Use the ``text`` type for fragments of non-formatted text.
 *
 * Recommended doctrine field type: ``text``
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TextType extends AbstractType
{
    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, TextareaType::class);
    }

    public function listContext($entity, $field, array $options = [])
    {
        $text = $this->accessor->getValue($entity, $field);

        return $options['preview'] ? StringUtil::preview($text) : $text;
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/type/text.html.twig',
            'listOptions' => [
                'preview' => true,
            ],
        ];
    }

    /**
     * @doc preview If true, only show the first few words of the text.
     * This is most useful in the list context.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'preview' => false,
        ]);
        $resolver->setAllowedTypes('preview', ['boolean']);
    }
}
