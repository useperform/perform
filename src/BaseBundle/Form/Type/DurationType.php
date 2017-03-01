<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Perform\BaseBundle\Form\DataTransformer\IntegerToDurationTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * DurationType.
 **/
class DurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new IntegerToDurationTransformer());
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'duration';
    }
}
