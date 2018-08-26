<?php

namespace Perform\BaseBundle\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\DurationType as FormType;
use Perform\BaseBundle\Util\DurationUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Use the ``duration`` type to show periods of time.
 *
 * @example
 * $config->add('length', [
 *     'type' => 'duration',
 *     'options' => [
 *         'format' => DurationType::FORMAT_HUMAN,
 *     ],
 * ]);
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DurationType extends AbstractType
{
    const FORMAT_DIGITAL = 0;
    const FORMAT_HUMAN = 1;
    const FORMAT_VERBOSE = 2;

    public function listContext($entity, $field, array $options = [])
    {
        $duration = $this->getPropertyAccessor()->getValue($entity, $field);

        switch ($options['format']) {
        case static::FORMAT_DIGITAL:
            $value = DurationUtil::toDigital($duration);
            break;
        case static::FORMAT_HUMAN:
            $value = DurationUtil::toHuman($duration);
            break;
        case static::FORMAT_VERBOSE:
            $value = DurationUtil::toVerbose($duration);
            break;
        default:
            throw new \InvalidArgumentException(sprintf('Invalid "format" option passed to "%s"', __CLASS__));
        }

        return [
            'value' => $value,
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, FormType::class, []);
    }

    /**
     * @doc format How to display the duration. Use one of the DurationType::FORMAT_* constants.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['format' => 0])
            ->setAllowedTypes('format', 'integer');
    }

    public function getDefaultConfig()
    {
        return [
            'template' => '@PerformBase/field_type/duration.html.twig',
            'listOptions' => [
                'format' => DurationType::FORMAT_DIGITAL,
            ],
            'viewOptions' => [
                'format' => DurationType::FORMAT_VERBOSE,
            ]
        ];
    }
}
