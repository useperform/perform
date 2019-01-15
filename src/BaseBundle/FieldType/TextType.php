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
        $formOptions = [
            'label' => $options['label'],
        ];
        $builder->add($field, TextareaType::class, array_merge($formOptions, $options['form_options']));
    }

    public function listContext($entity, $field, array $options = [])
    {
        $text = $this->getPropertyAccessor()->getValue($entity, $field);

        if ($options['preview']) {
            $text = StringUtil::preview(str_replace(PHP_EOL, ' ', $text));
        }

        return [
            'value' => $text,
        ];
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/text.html.twig',
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
