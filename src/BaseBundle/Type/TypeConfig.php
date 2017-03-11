<?php

namespace Perform\BaseBundle\Type;

use Perform\BaseBundle\Type\TypeRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Perform\BaseBundle\Util\StringUtil;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * TypeConfig
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeConfig
{
    const CONTEXT_LIST = 'list';
    const CONTEXT_VIEW = 'view';
    const CONTEXT_CREATE = 'create';
    const CONTEXT_EDIT = 'edit';

    protected static $optionKeys = [
        'listOptions',
        'viewOptions',
        'createOptions',
        'editOptions'
    ];

    protected $resolver;
    protected $fields = [];
    protected $addedConfigs = [];
    protected $defaultSort;

    public function __construct(TypeRegistry $registry)
    {
        $this->registry = $registry;
        $this->configureOptionsResolver();
    }

    protected function configureOptionsResolver()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver
            ->setRequired(['type'])
            ->setDefaults([
                'contexts' => [
                    static::CONTEXT_LIST,
                    static::CONTEXT_VIEW,
                    static::CONTEXT_CREATE,
                    static::CONTEXT_EDIT,
                ],
                'sort' => true,
            ])
            ->setAllowedTypes('contexts', 'array')
            ->setDefined(static::$optionKeys);
        foreach (static::$optionKeys as $key) {
            $this->resolver->setAllowedTypes($key, 'array');
        }
    }

    public function getTypes($context)
    {
        $types = [];
        foreach ($this->fields as $field => $config) {
            if (!in_array($context, $config['contexts'])) {
                continue;
            }

            $types[$field] = $this->fields[$field];
        }

        return $types;
    }

    public function getAllTypes()
    {
        return $this->fields;
    }

    public function getAddedConfigs()
    {
        return $this->addedConfigs;
    }

    /**
     * Add or amend a field.
     * If the field is already registered, the config will be merged.
     * If the field is not registered, the config will be merged with
     * the default config for that type.
     */
    public function add($name, array $config)
    {
        $this->addedConfigs[$name][] = $config;

        //make sure a label exists
        //only do this the first time to prevent nuking a custom label
        //with an override that doesn't have a label
        if (!isset($config['options']['label']) && !isset($this->fields[$name])) {
            $config['options']['label'] = StringUtil::sensible($name);
        }

        $this->normaliseOptions($config);

        // use the default for the type if there is no existing config
        if (!isset($this->fields[$name])) {
            if (!isset($config['type'])) {
                throw new MissingOptionsException('TypeConfig#add() requires "type" to be set.');
            }

            $this->fields[$name] = $this->registry->getType($config['type'])->getDefaultConfig();
            $this->normaliseOptions($this->fields[$name]);
        }

        $existingConfig = $this->fields[$name];

        //replace the entire contexts array in the existing config if
        //they are given
        if (isset($config['contexts'])) {
            unset($existingConfig['contexts']);
        }

        //merge with the existing config
        $config = array_replace_recursive($existingConfig, $config);

        $this->fields[$name] = $this->resolver->resolve($config);

        return $this;
    }

    /**
     * Ensure the options for each context are set, and remove
     * 'options' itself.
     */
    protected function normaliseOptions(&$config)
    {
        if (!isset($config['options'])) {
            $config['options'] = [];
        }
        foreach (static::$optionKeys as $key) {
            $config[$key] = isset($config[$key]) ?
                          array_merge($config['options'], $config[$key]) :
                          $config['options'];
        }
        unset($config['options']);
    }

    /**
     * @param string $name
     * @param string $direction
     *
     * @return TypeConfig
     */
    public function setDefaultSort($name, $direction)
    {
        $direction = strtoupper($direction);
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            throw new \InvalidArgumentException('Default sort direction must be "ASC" or "DESC"');
        }

        $this->defaultSort = [$name, $direction];

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultSort()
    {
        if (!$this->defaultSort) {
            $this->defaultSort = [null, 'ASC'];
        }

        return $this->defaultSort;
    }
}
