<?php

namespace Perform\Tools\Documentation;

use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Filesystem\Filesystem;
use Perform\BaseBundle\Type\TypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DocGenerator.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DocGenerator
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

        $rst = $this->twig->render(__DIR__.'/type_reference.rst.twig', [
            'name' => $name,
            'summary' => $doc->getSummary(),
            'bundleName' => $bundleName,
            'description' => $doc->getDescription(),
            'example' => isset($example) ? $example : false,
            'options' => $this->getOptions($name, $type),
        ]);

        return $rst;
    }

    protected function getOptions($name, TypeInterface $type)
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

        return $options;
    }

    protected function getOptionDescriptions(TypeInterface $type)
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

    protected function getDefaults(OptionsResolver $resolver, TypeInterface $type)
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
                    $defaults[$option][$key] = var_export($defaultConfig[$key.'Options'][$option], true);
                    continue;
                }

                if (isset($defaultConfig['options'][$option])) {
                    $defaults[$option][$key] = var_export($defaultConfig['options'][$option], true);
                    continue;
                }

                if (isset($resolverDefaults[$option])) {
                    $defaults[$option][$key] = var_export($resolverDefaults[$option], true);
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
}
