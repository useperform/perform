<?php

namespace Perform\BaseBundle\Tests\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;

/**
 * TestAdmin
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TestAdmin extends AbstractAdmin
{
    protected $listFields = [
        'one',
        'two',
    ];
    protected $viewFields = [
        'three',
        'four',
    ];
    protected $createFields = [
        'five',
        'six',
    ];
    protected $editFields = [
        'seven',
        'eight',
    ];
    protected $fieldOptions = [
        'three' => [
            'type' => 'datetime',
        ],
        'four' => [
            'label' => 'Special Label',
        ],
        'five' => [
            'type' => 'datetime',
        ],
        'six' => [
            'label' => 'Special Label',
        ],
        'seven' => [
            'type' => 'type_for_editing',
        ],
        'eight' => [
            'label' => 'Special Label',
            'type' => 'datetime',
        ],
    ];
    protected $createFieldOptions = [
        'five' => [
            'label' => 'Label for creation',
        ],
        'six' => [
            'foo' => 'bar',
        ],
    ];
    protected $editFieldOptions = [
        'seven' => [
            'type' => 'type_for_editing',
        ],
        'eight' => [
            'foo' => 'bar',
            'type' => null,
        ],
    ];
}
