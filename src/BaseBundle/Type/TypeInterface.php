<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * TypeInterface
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface TypeInterface
{
    public function listContext($entity, $field, array $options = []);

    public function viewContext($entity, $field, array $options = []);

    public function createContext(FormBuilderInterface $builder, $field, array $options = []);

    public function editContext(FormBuilderInterface $builder, $field, array $options = []);

    /**
     * @return array An array of TypeConfig::CONTEXT_* constants
     */
    public function getHtmlContexts();

    /**
     * @return array The default config array passed to TypeConfig#add().
     */
    public function getDefaultConfig();

    /**
     * @return string The name of template to render this field type.
     */
    public function getTemplate();
}
