<?php

namespace Admin\MediaBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Admin\Base\Type\AbstractType;
use Admin\MediaBundle\Plugin\PluginRegistry;

/**
 * ImageType
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImageType extends AbstractType
{
    protected $registry;

    public function __construct(PluginRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct();
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, EntityType::class, [
            'class' => 'AdminMediaBundle:File',
            'choice_label' => 'name',
            'placeholder' => 'None',
            'required' => false,
        ]);
    }

    public function listContext($entity, $field, array $options = [])
    {
        $file = $this->accessor->getValue($entity, $field);

        if (!$file) {
            return 'None';
        }

        return $this->registry->getPreview($file);
    }
}
