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
        $countryCode = $this->getPropertyAccessor()->getValue($entity, $field);

        return [
            'value' => Intl::getRegionBundle()->getCountryName($countryCode),
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, $options['form_options']);
    }
}
