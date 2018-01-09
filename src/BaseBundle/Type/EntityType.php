<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Exception\InvalidTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Use the ``entity`` type for relations to other entities.
 *
 * For example, suppose a ``Pet`` entity has an ``owner``, a
 * ``manyToOne`` doctrine relation to a ``User`` entity.
 * You would use the ``entity`` type on the ``owner`` property to
 * give a pet an owner.
 *
 * Collections of entities can be managed too - set the ``multiple``
 * option to ``true``.
 *
 * Note that sorting will not work out of the box.
 * You'll need to define a :ref:`custom sort function <type_sorting>`
 * if you want to sort by this field.
 *
 * @example
 * $config->add('owner', [
 *     'type' => 'entity',
 *     'options' => [
 *         'class' => 'PerformUserBundle:User',
 *         'display_field' => 'email',
 *     ],
 *     'sort' => false,
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class EntityType extends AbstractType
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, EntityFormType::class, [
            'class' => $options['class'],
            'choice_label' => $options['display_field'],
            'label' => $options['label'],
            'multiple' => $options['multiple'],
        ]);
    }

    /**
     * @doc class The related entity class
     * @doc display_field The property to use to display the related entity
     * @doc link_to If true, display a link to view the related entity
     * @doc multiple If true, assume the relation is a doctrine collection
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('display_field', 'id');
        $resolver->setAllowedTypes('display_field', 'string');
        $resolver->setRequired('class');
        $resolver->setAllowedTypes('class', 'string');
        $resolver->setDefault('link_to', true);
        $resolver->setAllowedTypes('link_to', 'boolean');
        $resolver->setDefault('multiple', false);
        $resolver->setAllowedTypes('multiple', 'boolean');
    }

    public function getDefaultConfig()
    {
        return [
            'template' => 'PerformBaseBundle:types:entity.html.twig',
            'sort' => false,
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $relatedEntity = $this->accessor->getValue($entity, $field);
        $this->ensureEntity($field, $relatedEntity);

        if (!$relatedEntity) {
            return '';
        }

        return [
            'value' => $relatedEntity,
            'display_field' => $options['display_field'],
            'link_to' => $options['link_to'],
            'multiple' => $options['multiple'],
        ];
    }

    public function exportContext($entity, $field, array $options = [])
    {
        $relatedEntity = $this->accessor->getValue($entity, $field);
        $this->ensureEntity($field, $relatedEntity);

        if (!$relatedEntity) {
            return '';
        }

        return $this->accessor->getValue($relatedEntity, $options['display_field']);
    }

    protected function ensureEntity($field, $value)
    {
        if (!is_object($value) && !is_null($value)) {
            throw new InvalidTypeException(sprintf('The entity field "%s" passed to %s must be a doctrine entity, a doctrine collection, or null.', $field, __CLASS__));
        }
    }
}
