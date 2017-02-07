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

    protected $resolver;
    protected $fields = [];
    protected $defaultSort;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $optionKeys = ['options', 'listOptions', 'viewOptions', 'createOptions', 'editOptions'];

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
                'options' => [],
            ])
            ->setAllowedTypes('contexts', 'array')
            ->setDefined($optionKeys);
        foreach ($optionKeys as $key) {
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

            $mergeKey = $context.'Options';
            $options = isset($config[$mergeKey]) ?
                     array_merge($config['options'], $config[$mergeKey]) :
                     $config['options'];

            if (!isset($options['label'])) {
                $options['label'] = StringUtil::sensible($field);
            }
            $options['sort'] = $config['sort'];

            $types[$field] = [
                'type' => $config['type'],
                'options' => $options,
            ];
        }

        return $types;
    }

    /**
     * Add or amend a field.
     * If the field is already registered, the config will be merged.
     * If the field is not registered, the config will be merged with
     * the default config for that type.
     */
    public function add($name, array $config)
    {
        if (isset($this->fields[$name])) {
            $initialConfig =  $this->fields[$name];
        } else {
            if (!isset($config['type'])) {
                throw new MissingOptionsException('TypeConfig#add() requires "type" to be set.');
            }

            $initialConfig = [];
            // $initialConfig = $this->registry->getType($config['type'])->getDefaultConfig();
        }

        //replace the entire contexts array if they are given in the override
        if (isset($config['contexts'])) {
            unset($initialConfig['contexts']);
        }

        $config = array_replace_recursive($initialConfig, $config);

        $this->fields[$name] = $this->resolver->resolve($config);

        return $this;
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
