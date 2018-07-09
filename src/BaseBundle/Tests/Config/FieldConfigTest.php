<?php

namespace Perform\BaseBundle\Tests\Config;

use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\FieldType\FieldTypeRegistry;
use Perform\BaseBundle\FieldType\StringType;
use Perform\BaseBundle\FieldType\FieldTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\BaseBundle\FieldType\DateTimeType;
use Perform\BaseBundle\FieldType\BooleanType;
use Perform\BaseBundle\Exception\InvalidFieldException;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Perform\BaseBundle\Test\Services;
use Perform\BaseBundle\Crud\CrudRequest;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FieldConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $config;
    protected $typeRegistry;
    protected $stubType;

    public function setUp()
    {
        $this->typeRegistry = Services::fieldTypeRegistry([
            'string' => new StringType(),
            'datetime' => new DateTimeType(),
            'boolean' => new BooleanType(),
            'stub' => $this->stubType = $this->getMock(FieldTypeInterface::class),
        ]);
        $this->config = new FieldConfig($this->typeRegistry);
    }

    protected function stubOptions($callback)
    {
        $this->stubType->expects($this->any())
            ->method('configureOptions')
            ->will($this->returnCallback($callback));
    }

    protected function stubDefaults(array $defaults)
    {
        $this->stubType->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue($defaults));
    }

    public function testGetNoTypes()
    {
        $this->assertSame([], $this->config->getTypes(CrudRequest::CONTEXT_VIEW));
    }

    public function testAddSimpleType()
    {
        $this->assertSame($this->config, $this->config->add('title', [
            'type' => 'string',
        ]));
        $types = $this->config->getTypes(CrudRequest::CONTEXT_CREATE);
        $this->assertArrayHasKey('type', $types['title']);
    }

    public function testTypeMustBeSupplied()
    {
        $this->setExpectedException(InvalidFieldException::class);
        $this->config->add('title', []);
    }

    public function testFieldsCanBeRestrictedToAContext()
    {
        $this->config->add('title', [
            'type' => 'string',
            'contexts' => [
                CrudRequest::CONTEXT_LIST,
                CrudRequest::CONTEXT_VIEW,
            ],
        ]);

        $this->assertSame(1, count($this->config->getTypes(CrudRequest::CONTEXT_VIEW)));
        $this->assertSame(1, count($this->config->getTypes(CrudRequest::CONTEXT_LIST)));
        $this->assertSame(0, count($this->config->getTypes(CrudRequest::CONTEXT_CREATE)));
        $this->assertSame(0, count($this->config->getTypes(CrudRequest::CONTEXT_EDIT)));
    }

    public function testThereAreDefaults()
    {
        $this->config->add('title', ['type' => 'string']);
        $type = $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title'];
        $this->assertInternalType('array', $type['listOptions']);
        $this->assertInternalType('string', $type['listOptions']['label']);
        $this->assertTrue($type['sort']);
    }

    public function testSuppliedOptionsAreReturned()
    {
        $this->config->add('date', [
            'type' => 'datetime',
            'options' => [
                'human' => true,
            ],
        ]);

        $this->assertSame(true, $this->config->getTypes(CrudRequest::CONTEXT_VIEW)['date']['viewOptions']['human']);
    }

    public function contextProvider()
    {
        return [
            [CrudRequest::CONTEXT_LIST, 'listOptions'],
            [CrudRequest::CONTEXT_VIEW, 'viewOptions'],
            [CrudRequest::CONTEXT_CREATE, 'createOptions'],
            [CrudRequest::CONTEXT_EDIT, 'editOptions'],
        ];
    }

    /**
     * @dataProvider contextProvider
     */
    public function testOptionsAreOverriddenPerContext($context, $key)
    {
        $this->config->add('date', [
            'type' => 'datetime',
            'options' => [
                'human' => true,
            ],
            $key => [
                'human' => false,
            ],
        ]);

        if ($context === CrudRequest::CONTEXT_VIEW) {
            $notContext = CrudRequest::CONTEXT_LIST;
            $notContextKey = 'listOptions';
        } else {
            $notContext = CrudRequest::CONTEXT_VIEW;
            $notContextKey = 'viewOptions';
        }
        $this->assertSame(true, $this->config->getTypes($notContext)['date'][$notContextKey]['human']);

        $this->assertSame(false, $this->config->getTypes($context)['date'][$key]['human']);
    }

    public function testSensibleLabelIsGiven()
    {
        $this->config->add('superTitle', ['type' => 'string']);
        $this->assertSame('Super title', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['superTitle']['listOptions']['label']);
    }

    public function testLabelCanBeOverridden()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['superTitle']['listOptions']['label']);
    }

    public function testLabelCanBeOverriddenPerContext()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'listOptions' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['superTitle']['listOptions']['label']);
        $this->assertSame('Super title', $this->config->getTypes(CrudRequest::CONTEXT_EDIT)['superTitle']['editOptions']['label']);
    }

    public function testOverriddenLabelIsNotChanged()
    {
        $this->stubOptions(function ($resolver) {
            $resolver->setDefined('other_option');
        });
        $this->config->add('title', [
            'type' => 'stub',
            'options' => [
                'label' => 'Some label',
            ],
        ]);
        $this->config->add('title', [
            'options' => [
                'other_option' => 'foo',
            ],
        ]);

        $this->assertSame('Some label', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title']['listOptions']['label']);
        $this->assertSame('foo', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title']['listOptions']['other_option']);
    }

    public function testOverriddenLabelCanBeChanged()
    {
        $this->stubOptions(function ($resolver) {
            $resolver->setDefined('other_option');
        });
        $this->config->add('title', [
            'type' => 'stub',
            'options' => [
                'label' => 'Some label',
            ],
        ]);
        $this->config->add('title', [
            'options' => [
                'label' => 'Custom label again',
                'other_option' => 'foo',
            ],
        ]);

        $this->assertSame('Custom label again', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title']['listOptions']['label']);
        $this->assertSame('foo', $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title']['listOptions']['other_option']);
    }

    public function testFieldsCanBeAddedMultipleTimes()
    {
        $this->stubOptions(function ($resolver) {
            $resolver->setDefined('stuff');
        });
        $this->config->add('title', [
            'type' => 'stub',
            'options' => [
                'stuff' => [
                    'foo' => true,
                    'bar' => true,
                ],
            ],
        ]);
        $this->config->add('title', [
            'options' => [
                'stuff' => [
                    'bar' => false,
                    'baz' => true,
                ],
            ],
        ]);

        $expected = [
            'foo' => true,
            'bar' => false,
            'baz' => true,
        ];
        $actual = $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title']['listOptions']['stuff'];
        $this->assertSame($expected, $actual);
    }

    public function testContextIsAlwaysOverwritten()
    {
        $this->config->add('title', [
            'type' => 'string',
            'contexts' => [CrudRequest::CONTEXT_VIEW, CrudRequest::CONTEXT_LIST],
        ]);
        $this->config->add('title', [
            'contexts' => [CrudRequest::CONTEXT_VIEW],
        ]);

        $this->assertSame(0, count($this->config->getTypes(CrudRequest::CONTEXT_LIST)));
        $this->assertSame(1, count($this->config->getTypes(CrudRequest::CONTEXT_VIEW)));
    }

    public function testSortCanBeDisabled()
    {
        $this->config->add('enabled', [
            'type' => 'boolean',
            'sort' => false,
        ]);
        $this->assertFalse($this->config->getTypes(CrudRequest::CONTEXT_LIST)['enabled']['sort']);
    }

    public function testDefaultSort()
    {
        $this->assertSame([null, 'ASC'], $this->config->getDefaultSort());
        $this->assertSame($this->config, $this->config->setDefaultSort('title', 'DESC'));
        $this->assertSame(['title', 'DESC'], $this->config->getDefaultSort());
    }

    public function testDefaultSortWithIllegalDirection()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->config->setDefaultSort('title', 'foo');
    }

    public function testDefaultConfigFromATypeIsNormalised()
    {
        $this->stubOptions(function ($resolver) {
            $resolver->setDefined('some_type_option');
        });
        //contains options key, which should be normalised to the different contexts
        $this->stubDefaults([
            'sort' => false,
            'options' => [
                'some_type_option' => 'foo',
            ],
            'listOptions' => [
                'some_type_option' => 'bar',
            ],
        ]);

        $this->config->add('title', ['type' => 'stub']);

        $resolved = $this->config->getTypes(CrudRequest::CONTEXT_LIST)['title'];
        $this->assertSame('foo', $resolved['viewOptions']['some_type_option']);
        $this->assertSame('bar', $resolved['listOptions']['some_type_option']);
    }

    public function testInvalidConfigIsWrappedInException()
    {
        try {
            $this->config->add('test', [
                'type' => 'stub',
                'options' => [
                    'not_an_option' => false,
                ],
            ]);
        } catch (InvalidFieldException $e) {
            $this->assertInstanceOf(ExceptionInterface::class, $e->getPrevious());

            return;
        }

        $this->fail('Failed asserting that an exception was thrown.');
    }

    public function testGetAllTypes()
    {
        $this->config->add('one', [
            'type' => 'string',
            'contexts' => [],
        ]);
        $this->config->add('two', [
            'type' => 'string',
            'contexts' => [CrudRequest::CONTEXT_LIST],
        ]);

        $all = $this->config->getAllTypes();
        $this->assertSame(['one', 'two'], array_keys($all));
    }

    public function testGetAddedConfigs()
    {
        $first = [
            'type' => 'string',
            'contexts' => [],
        ];
        $this->config->add('one', $first);
        $second = [
            'type' => 'string',
            'contexts' => [],
        ];
        $this->config->add('one', $second);

        $this->assertSame(['one' => [$first, $second]], $this->config->getAddedConfigs());
    }

    public function testDefaultContextsCanBeChanged()
    {
        $defaults = [
            CrudRequest::CONTEXT_LIST,
            CrudRequest::CONTEXT_VIEW,
        ];
        $this->assertSame($this->config, $this->config->setDefaultContexts($defaults));

        $this->config->add('one', [
            'type' => 'string',
        ]);

        $this->assertSame($defaults, $this->config->getAllTypes()['one']['contexts']);
    }

    public function testUnsetDefaultContextsDoNotOverrideContextsFromFieldType()
    {
        // default contexts have not been set, respect the default from the type
        $this->stubType->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue([
                'contexts' => [CrudRequest::CONTEXT_VIEW],
            ]));

        $this->config->add('one', [
            'type' => 'stub',
        ]);
        $this->assertSame([CrudRequest::CONTEXT_VIEW], $this->config->getAllTypes()['one']['contexts']);

        // default contexts have been set, should override the default from the type
        $defaults = [
            CrudRequest::CONTEXT_LIST,
            CrudRequest::CONTEXT_VIEW,
        ];
        $this->config->setDefaultContexts($defaults);
        $this->config->add('two', [
            'type' => 'stub',
        ]);
        $this->assertSame($defaults, $this->config->getAllTypes()['two']['contexts']);
    }
}
