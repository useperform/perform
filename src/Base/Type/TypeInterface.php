<?php

namespace Admin\Base\Type;

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
}
