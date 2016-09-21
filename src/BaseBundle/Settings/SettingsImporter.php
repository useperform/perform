<?php

namespace Perform\BaseBundle\Settings;

use Symfony\Component\Yaml\Yaml;
use Perform\BaseBundle\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SettingsImporter adds and updates settings in the database according to a
 * their defined specification.
 *
 * If a setting definition is different to the definition in the database
 * (i.e. everything except the value), it will be updated.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsImporter
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository('PerformBaseBundle:Setting');
    }

    public function import(Setting $setting)
    {
        $existing = $this->find($setting->getKey());
        if (!$existing) {
            $this->entityManager->persist($setting);
            $this->entityManager->flush();

            return;
        }

        if (!$existing->requiresUpdate($setting)) {
            return;
        }

        $existing->update($setting);
        $this->entityManager->persist($existing);
        $this->entityManager->flush();
    }

    public function importYamlFile($path)
    {
        foreach ($this->parseYamlFile($path) as $setting) {
            $this->import($setting);
        }
    }

    protected function find($key)
    {
        if (!is_string($key)) {
        }

        return $this->repo->findOneBy(['key' => $key]);
    }

    public function parseYamlFile($path)
    {
        $config = Yaml::parse(file_get_contents($path));

        foreach ($config as $key => $definition) {
            $collection[] = $this->newSetting($key, $definition);
        }

        return $collection;
    }

    protected function newSetting($key, array $definition)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('type')
            ->setDefault('defaultValue', null)
            ->setDefault('requiredRole', null)
            ->setDefault('global', true);
        $definition = $resolver->resolve($definition);

        $setting = new Setting($key);
        $setting->setType($definition['type'])
            ->setGlobal($definition['global'])
            ->setDefaultValue($definition['defaultValue'])
            ->setRequiredRole($definition['requiredRole']);

        return $setting;
    }
}
