<?php

namespace Perform\Tools\Documentation;

use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\FieldType\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FieldTypeReferenceGenerator
{
    protected $twig;
    protected $registry;

    public function __construct($twig, $registry)
    {
        $this->twig = $twig;
        $this->registry = $registry;
        $this->docblock = DocBlockFactory::createInstance();
    }

    public function generateFile($name, $filename)
    {
        $f = new FileSystem();
        $f->dumpFile($filename, $this->generate($name));
    }

    public function generate($name)
    {
        $type = $this->registry->getType($name);
        $defaults = $type->getDefaultConfig();

        $ref = new \ReflectionObject($type);

        if ($ref->getDocComment()) {
            $doc = $this->docblock->create($ref->getDocComment());
            if (!empty($doc->getTagsByName('example'))) {
                $example = $doc->getTagsByName('example')[0];
                $example = '    '.implode("\n    ", explode("\n", $example));
            }
        }

        $pieces = explode('\\', $ref->getNamespaceName());
        $bundleName = $pieces[0].$pieces[1];

        $rst = $this->twig->render('type_reference.rst.twig', [
            'name' => $name,
            'summary' => $doc->getSummary(),
            'bundleName' => $bundleName,
            'description' => $doc->getDescription(),
            'example' => isset($example) ? $example : false,
            'options' => $this->getOptions($name, $type),
        ]);

        return $rst;
    }

    protected function getOptions($name, FieldTypeInterface $type)
    {
        $resolver = $this->registry->getOptionsResolver($name);
        $options = [];

        $descriptions = $this->getOptionDescriptions($type);
        $defaults = $this->getDefaults($resolver, $type);
        $allowedTypes = $this->getAllowedTypes($resolver);
        $required = array_fill_keys($resolver->getRequiredOptions(), true);

        foreach ($resolver->getDefinedOptions() as $key) {
            $options[$key] = [];
            $options[$key]['description'] = isset($descriptions[$key]) ? $descriptions[$key] : '';
            $options[$key]['required'] = isset($required[$key]) && !isset($defaults[$key]['all']);
            $options[$key]['defaults'] = $defaults[$key];
            $options[$key]['allowed_types'] = isset($allowedTypes[$key]) ? $allowedTypes[$key] : [];
        }
        // set descriptions for common options manually
        $options['form_options']['description'] = "An array of options to pass to the underlying form type in the ``create`` and ``edit`` contexts. These will be merged with (and will overwrite) any form options that have been created as a result of the field type's other options.";
        $options['label']['description'] = 'The label to use for form labels and table headings. If no label is provided, a sensible label will be created automatically.';
        //set label to be not required, since it will always have a value from FieldConfig
        $options['label']['required'] = false;

        ksort($options);
        return $options;
    }

    protected function getOptionDescriptions(FieldTypeInterface $type)
    {
        $ref = new \ReflectionObject($type);
        $method = $ref->getMethod('configureOptions');

        $descriptions = [];
        if ($method->getDocComment()) {
            $doc = $this->docblock->create($method->getDocComment());
            foreach ($doc->getTagsByName('doc') as $tag) {
                $body = trim($tag->getDescription());
                $pos = strpos($body, ' ');
                if ($pos === false) {
                    continue;
                }

                $key = substr($body, 0, $pos);
                $descriptions[$key] = trim(substr($body, $pos));
            }
        }

        return $descriptions;
    }

    protected function getDefaults(OptionsResolver $resolver, FieldTypeInterface $type)
    {
        $defaults = [];
        $defaultConfig = $type->getDefaultConfig();

        $ref = new \ReflectionObject($resolver);
        $prop = $ref->getProperty('defaults');
        $prop->setAccessible(true);
        $resolverDefaults = $prop->getValue($resolver);

        foreach ($resolver->getDefinedOptions() as $option) {
            $defaults[$option] = [];
            $i = 0;

            foreach (['list', 'view', 'create', 'edit'] as $key) {
                // real life priority is:
                // context specific options
                // options
                // defaults from the resolver

                if (isset($defaultConfig[$key.'Options'][$option])) {
                    $defaults[$option][$key] = $this->formatVar($defaultConfig[$key.'Options'][$option]);
                    continue;
                }

                if (isset($defaultConfig['options'][$option])) {
                    $defaults[$option][$key] = $this->formatVar($defaultConfig['options'][$option]);
                    continue;
                }

                if (isset($resolverDefaults[$option])) {
                    $defaults[$option][$key] = $this->formatVar($resolverDefaults[$option]);
                    continue;
                }

                ++$i;
                $defaults[$option][$key] = null;
            }
            //mark if all contexts have a default
            if ($i === 0) {
                $defaults[$option]['all'] = true;
            }
        }

        return $defaults;
    }

    protected function getAllowedTypes(OptionsResolver $resolver)
    {
        $ref = new \ReflectionObject($resolver);
        $prop = $ref->getProperty('allowedTypes');
        $prop->setAccessible(true);

        return $prop->getValue($resolver);
    }

    protected function formatVar($value)
    {
        switch (gettype($value)) {
        case 'string':
            return "'".$value."'";
        case 'integer':
        case 'double':
            return $value;
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'array':
            $pieces = [];
            foreach ($value as $key => $value) {
                $pieces[] = is_int($key) ?
                          $this->formatVar($value) :
                          $this->formatVar($key).' => '.$this->formatVar($value);
            }
            return '['.implode(', ', $pieces).']';
        case 'NULL':
            return null;
        case 'object':
            return '(object)';
        default:
            return '(resource)';
        }
    }
}
