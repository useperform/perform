<?php

namespace Perform\MediaBundle\Npm;

use Perform\DevBundle\Npm\DependenciesInterface;

final class Dependencies implements DependenciesInterface
{
    public function getDependencies(): array
    {
        return [
            'axios' => '^0.17.1',
            'bootstrap-vue' => '^1.0.2',
            'vue' => '^2.4.4',
            'vue-router' => '^3.0.1',
            'vuex' => '^3.0.1',
        ];
    }
}
