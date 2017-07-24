<?php

namespace Perform\MailingListBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;

/**
 * Create multiple forms of the same type with different names
 * according to the action URL they will submit to.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UniqueFormFactory
{
    protected $forms = [];
    protected $instances = [];
    protected $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function addType($name, $type)
    {
        $this->forms[$name] = $type;
    }

    protected function createKey($name, $action)
    {
        $path = trim(strtolower((string) parse_url($action, PHP_URL_PATH)), '/');

        return $name.'__'.preg_replace('/[^[:alnum:]]/', '_', $path);
    }

    public function create($name, $action)
    {
        $key = $this->createKey($name, $action);
        if (!isset($this->instances[$key])) {
            if (!isset($this->forms[$name])) {
                throw new \InvalidArgumentException(sprintf('Unknown mailing list form type "%s" requested. Registered types are "%s".', $name, implode(array_keys($this->forms), '", "')));
            }
            $type = $this->forms[$name];

            $this->instances[$key] = $this->factory->createNamed($key, $type, null, [
                'action' => $action,
            ]);
        }

        return $this->instances[$key];
    }
}
