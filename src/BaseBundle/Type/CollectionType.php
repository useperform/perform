<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Perform\BaseBundle\Exception\InvalidTypeException;
use Perform\BaseBundle\Form\Type\CrudType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as CollectionFormType;
use Perform\BaseBundle\Crud\CrudRegistry;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Asset\AssetContainer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * CollectionType.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionType extends AbstractType
{
    protected $crudRegistry;
    protected $entityManager;
    protected $assets;

    public function __construct(CrudRegistry $crudRegistry, EntityManagerInterface $entityManager, AssetContainer $assets)
    {
        parent::__construct();
        $this->crudRegistry = $crudRegistry;
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
                'entity' => $options['entity'],
                'context' => $context,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);

        $entity = $builder->getData();
        $originalCollection = new ArrayCollection();
        foreach ($this->accessor->getValue($entity, $field) as $item) {
            $originalCollection[] = $item;
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($field, $originalCollection) {
            $entity = $event->getData();
            $collection = $this->accessor->getValue($entity, $field);
            foreach ($originalCollection as $item) {
                if (!$collection->contains($item)) {
                    $this->entityManager->remove($item);
                }
            }
        });

        return [
            'sortField' => $options['sortField'],
        ];
    }

    public function getDefaultConfig()
    {
        return [
            'sort' => false,
            'template' => '@PerformBase/type/collection.html.twig',
        ];
    }

    /**
     * @doc itemLabel The word to use when referring to one or many of the
     * entities. Defaults to 'item' or 'items'. Use a string and the
     * plural word will be guessed, or an array of two strings to
     * define the plural word explicitly.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity');
        $resolver->setAllowedTypes('entity', 'string');
        $resolver->setDefined('itemLabel');
        $resolver->setAllowedTypes('itemLabel', ['string', 'array']);
        $resolver->setDefaults([
            'sortField' => false,
        ]);
        $resolver->setAllowedTypes('sortField', ['boolean', 'string']);
    }

    public function viewContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $crud = null;
        if (isset($collection[0])) {
            $crud = $this->crudRegistry->get($collection[0]);
        }

        return [
            'collection' => $collection,
            'admin' => $crud,
        ];
    }

    public function listContext($entity, $field, array $options = [])
    {
        $collection = $this->accessor->getValue($entity, $field);
        $this->ensureCollection($collection);

        $itemLabel = isset($options['itemLabel']) ? (array) $options['itemLabel'] : ['item'];
        $label = $itemLabel[0];
        $count = count($collection);
        if ($count !== 1) {
            $label = isset($itemLabel[1]) ? $itemLabel[1] : $label.'s';
        }

        return $count.' '.trim($label);
    }

    protected function ensureCollection($value)
    {
        if (!$value instanceof Collection) {
            throw new InvalidTypeException(sprintf('The entity field "%s" passed to %s must be an instance of %s', $field, __CLASS__, Collection::class));
        }
    }
}
