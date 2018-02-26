<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Exception\InvalidTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Use the ``tag`` type for adding tags to different entities.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TagType extends EntityType
{
    /**
     * @doc discriminator A string to distinguish this entity from
     * others, e.g. 'blog_category' or 'project_group'
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
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
            'query_builder' => function($repo) use ($options) {
                return $repo->queryByDiscriminator($options['discriminator']);
            }]);
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/type/tag.html.twig',
            'sort' => false,
            'options' => [
                'multiple' => true,
                'class' => 'PerformBaseBundle:Tag',
                'display_field' => 'title',
                'link_to' => false,
            ]
        ];
    }
}
