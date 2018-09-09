<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Perform\BaseBundle\Exception\InvalidFieldException;
use Perform\BaseBundle\Form\Type\CrudType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as CollectionFormType;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionType extends AbstractType
{
    protected $entityManager;
    protected $assets;

    public function __construct(EntityManagerInterface $entityManager, AssetContainer $assets)
    {
        $this->entityManager = $entityManager;
        $this->assets = $assets;
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->addFormField($builder, $field, $options, CrudRequest::CONTEXT_CREATE);
    }

    public function editContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        return $this->addFormField($builder, $field, $options, CrudRequest::CONTEXT_EDIT);
    }

    protected function addFormField(FormBuilderInterface $builder, $field, array $options, $context)
    {
        $this->assets->addJs('/bundles/performbase/js/types/collection.js');

        $builder->add($field, CollectionFormType::class, [
            'entry_type' => CrudType::class,
            'entry_options' => [
                'crud_name' => $options['crud_name'],
                'context' => $context,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);

        $entity = $builder->getData();
        $originalCollection = new ArrayCollection();
        foreach ($this->getPropertyAccessor()->getValue($entity, $field) as $item) {
            $originalCollection[] = $item;
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($field, $originalCollection) {
            $entity = $event->getData();
            $collection = $this->getPropertyAccessor()->getValue($entity, $field);
            foreach ($originalCollection as $item) {
                if (!$collection->contains($item)) {
                    $this->entityManager->remove($item);
                }
            }
        });

        return [
            'sort_field' => $options['sort_field'],
        ];
    }

    public function getDefaultConfig()
    {
        return [
            'sort' => false,
            'template' => '@PerformBase/field_type/collection.html.twig',
        ];
    }

    /**
     * @doc crud_name The crud name to use for the related entity.
     *
     * @doc item_label The translation key to use when referring to one or many
     * entities, usually in the list context.
     * The default label is 'item' or 'items', e.g. '1 item', '5
     * items', defined in the BaseBundle translation catalogue.
     * Either update the translation to change the label globally for
     * all collection fields in your application, or set this option
     * to use a different translation for just this field.
     * To not use a label at all, set this option to false.
     *
     * @doc item_label_domain The translation domain to use when translating the item label.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('crud_name');
        $resolver->setAllowedTypes('crud_name', 'string');
        $resolver->setDefaults([
            'item_label' => 'field_type.collection.item_label',
            'item_label_domain' => 'PerformBaseBundle',
            'sort_field' => false,
        ]);
        $resolver->setAllowedTypes('item_label', ['string', 'boolean']);
        $resolver->setAllowedTypes('sort_field', ['boolean', 'string']);
    }

    public function viewContext($entity, $field, array $options = [])
    {
        $collection = $this->getPropertyAccessor()->getValue($entity, $field);
        $this->ensureCollection($collection);

        return [
            'has_items' => isset($collection[0]),
            'collection' => $collection,
            'crud_name' => $options['crud_name'],
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $collection = $this->getPropertyAccessor()->getValue($entity, $field);
        $this->ensureCollection($collection);

        return [
            'item_label' => $options['item_label'],
            'count' => count($collection),
        ];
    }

    protected function ensureCollection($value)
    {
        if (!$value instanceof Collection) {
            throw new InvalidFieldException(sprintf('The entity field "%s" passed to %s must be an instance of %s', $field, __CLASS__, Collection::class));
        }
    }
}
