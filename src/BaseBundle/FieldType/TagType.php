<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Entity\Tag;

/**
 * Use the ``tag`` type for tagging entities with instances of ``Perform\BaseBundle\Entity\Tag``.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TagType extends AbstractType
{
    /**
     * @doc discriminator A string to distinguish this group of tags from
     * others, e.g. 'blog_post_category' or 'project_group'
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Tag::class,
            'display_field' => 'title',
            'multiple' => true,
        ]);
        $resolver->setAllowedTypes('class', 'string');
        $resolver->setAllowedTypes('display_field', 'string');
        $resolver->setAllowedTypes('multiple', 'boolean');
        $resolver->setRequired('discriminator');
        $resolver->setAllowedTypes('discriminator', 'string');
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, EntityFormType::class, [
            'class' => $options['class'],
            'choice_label' => $options['display_field'],
            'label' => $options['label'],
            'multiple' => $options['multiple'],
            'query_builder' => function ($repo) use ($options) {
                return $repo->queryByDiscriminator($options['discriminator']);
            },
        ]);
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/tag.html.twig',
            'sort' => false,
        ];
    }
}
