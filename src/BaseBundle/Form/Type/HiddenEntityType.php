<?php

namespace Perform\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Form\DataTransformer\EntityToIdentifierTransformer;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class HiddenEntityType extends AbstractType
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EntityToIdentifierTransformer($this->em, $options['class']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
