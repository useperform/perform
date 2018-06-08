<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\CountryType as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CountryType extends AbstractType
{
    public function listContext($entity, $field, array $options = [])
    {
        $countryCode = $this->accessor->getValue($entity, $field);

        return Intl::getRegionBundle()->getCountryName($countryCode);
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, $options['form_options']);
    }

    /**
     * @doc form_options The options to pass to the form type in the create and edit contexts.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'form_options' => [],
        ]);
        $resolver->setAllowedTypes('form_options', 'array');
    }
}
