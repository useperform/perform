<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface TypeInterface
{
    public function listContext($entity, $field, array $options = []);

    public function viewContext($entity, $field, array $options = []);

    /**
     * Format an entity property suitable for exporting, e.g. to json or csv.
     *
     * @param object $entity
     * @param string $field
     * @param array  $options
     *
     * @return mixed
     */
    public function exportContext($entity, $field, array $options = []);

    public function createContext(FormBuilderInterface $builder, $field, array $options = []);

    public function editContext(FormBuilderInterface $builder, $field, array $options = []);

    /**
     * @return array the default config array passed to TypeConfig#add()
     */
    public function getDefaultConfig();

    /**
     * Define the options this type can accept.
     *
     * The option 'label' will already be defined.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @return string the name of template to render this field type
     */
    public function getTemplate();
}
