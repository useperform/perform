<?php

namespace Perform\DevBundle\File;

/**
 * ComposerConfig.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ComposerConfig
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function update(array $config)
    {
        if (empty($config)) {
            return;
        }

        return $this->save(array_replace_recursive($this->getConfig(), $config));
    }

    public function save(array $config)
    {
        $config = static::filter($config);
        file_put_contents($this->file, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK).PHP_EOL);
    }

    public function replace($key, array $values)
    {
        $config = $this->getConfig();
        $config[$key] = $values;

        $this->save($config);
    }

    public function getConfig()
    {
        $config = json_decode(file_get_contents($this->file), true);

        return $config ?: [];
    }

    public function getProperty($name)
    {
        $config = $this->getConfig();

        return isset($config[$name]) ? $config[$name] : false;
    }

    protected static function filter($array)
    {
        foreach ($array as $k => $v) {
            if (empty($v)) {
                unset($array[$k]);
                continue;
            }

            if (is_array($v)) {
                $array[$k] = static::filter($v);
            }
        }

        return $array;
    }
}
