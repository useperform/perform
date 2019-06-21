<?php

namespace Perform\PageEditorBundle\Npm;

use Perform\DevBundle\Npm\DependenciesInterface;

final class Dependencies implements DependenciesInterface
{
    public function getDependencies(): array
    {
        return [
            'axios' => '^0.17.1',
            'vue' => '^2.4.4',
            'vuex' => '^3.0.1',
            'date-fns' => '^2.0.0-alpha.7',
        ];
    }
}
