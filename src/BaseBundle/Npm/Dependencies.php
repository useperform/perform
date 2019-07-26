<?php

namespace Perform\BaseBundle\Npm;

use Perform\DevBundle\Npm\DependenciesInterface;

final class Dependencies implements DependenciesInterface
{
    public function getDependencies(): array
    {
        return [
            'bootstrap' => '^4.0.0-beta.2',
            'bootstrap-vue' => '^1.0.2',
            'date-fns' => '^2.0.0-alpha.7',
            'font-awesome' => '^4.7.0',
            'jquery' => '^3.2.1',
            'jquery.cookie' => '^1.4.1',
            'markdown-it' => '^8.4.0',
            'popper.js' => '^1.14.3',
            'select2' => '^4.0.3',
            'underscore' => '^1.8.3',
            'vue' => '^2.5.3',
        ];
    }
}
