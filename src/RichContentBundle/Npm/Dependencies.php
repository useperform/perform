<?php

namespace Perform\RichContentBundle\Npm;

use Perform\DevBundle\Npm\DependenciesInterface;

final class Dependencies implements DependenciesInterface
{
    public function getDependencies(): array
    {
        return [
            'axios' => '^0.17.1',
            'bootstrap-vue' => '^1.0.2',
            'get-video-id' => '^2.1.5',
            'lodash.debounce' => '^4.0.8',
            'medium-editor' => '^5.23.3',
            'vue' => '^2.4.4',
            'vue-clickaway' => '^2.1.0',
            'vue-router' => '^3.0.1',
            'vuex' => '^3.0.1',
        ];
    }
}
