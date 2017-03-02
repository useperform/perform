<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Type\TypeConfig;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Perform\BaseBundle\Type\TypeRegistry;
use Perform\BaseBundle\Type\StringType;
use Perform\BaseBundle\Type\TypeInterface;

/**
 * TypeConfigTest.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class TypeConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $config;

    public function setUp()
    {
        $this->typeRegistry = $this->getMockBuilder(TypeRegistry::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $this->config = new TypeConfig($this->typeRegistry);
        $this->typeRegistry->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(new StringType()));
    }

    public function testGetNoTypes()
    {
        $this->assertSame([], $this->config->getTypes(TypeConfig::CONTEXT_VIEW));
    }

    public function testAddSimpleType()
    {
        $this->assertSame($this->config, $this->config->add('title', [
            'type' => 'string',
        ]));
        $types = $this->config->getTypes(TypeConfig::CONTEXT_CREATE);
        $this->assertArrayHasKey('type', $types['title']);
    }

    public function testTypeMustBeSupplied()
    {
        $this->setExpectedException(MissingOptionsException::class);
        $this->config->add('title', []);
    }

    public function testFieldsCanBeRestrictedToAContext()
    {
        $this->config->add('title', [
            'type' => 'string',
            'contexts' => [
                TypeConfig::CONTEXT_LIST,
                TypeConfig::CONTEXT_VIEW,
            ],
        ]);

        $this->assertSame(1, count($this->config->getTypes(TypeConfig::CONTEXT_VIEW)));
        $this->assertSame(1, count($this->config->getTypes(TypeConfig::CONTEXT_LIST)));
        $this->assertSame(0, count($this->config->getTypes(TypeConfig::CONTEXT_CREATE)));
        $this->assertSame(0, count($this->config->getTypes(TypeConfig::CONTEXT_EDIT)));
    }

    public function testThereAreDefaults()
    {
        $this->config->add('title', ['type' => 'string']);
        $type = $this->config->getTypes(TypeConfig::CONTEXT_LIST)['title'];
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

        $this->assertSame(true, $this->config->getTypes(TypeConfig::CONTEXT_VIEW)['date']['viewOptions']['human']);
    }

    public function contextProvider()
    {
        return [
            [TypeConfig::CONTEXT_LIST, 'listOptions'],
            [TypeConfig::CONTEXT_VIEW, 'viewOptions'],
            [TypeConfig::CONTEXT_CREATE, 'createOptions'],
            [TypeConfig::CONTEXT_EDIT, 'editOptions'],
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

        if ($context === TypeConfig::CONTEXT_VIEW) {
            $notContext = TypeConfig::CONTEXT_LIST;
            $notContextKey = 'listOptions';
        } else {
            $notContext = TypeConfig::CONTEXT_VIEW;
            $notContextKey = 'viewOptions';
        }
        $this->assertSame(true, $this->config->getTypes($notContext)['date'][$notContextKey]['human']);

        $this->assertSame(false, $this->config->getTypes($context)['date'][$key]['human']);
    }

    public function testSensibleLabelIsGiven()
    {
        $this->config->add('superTitle', ['type' => 'string']);
        $this->assertSame('Super title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['listOptions']['label']);
    }

    public function testLabelCanBeOverridden()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['listOptions']['label']);
    }

    public function testLabelCanBeOverriddenPerContext()
    {
        $this->config->add('superTitle', [
            'type' => 'string',
            'listOptions' => [
                'label' => 'Title',
            ],
        ]);
        $this->assertSame('Title', $this->config->getTypes(TypeConfig::CONTEXT_LIST)['superTitle']['listOptions']['label']);
        $this->assertSame('Super title', $this->config->getTypes(TypeConfig::CONTEXT_EDIT)['superTitle']['editOptions']['label']);
    }

    public function testFieldsCanBeAddedMultipleTimes()
    {
        $this->config->add('title', [
            'type' => 'string',
            'options' => [
                'stuff' => [
                    'foo' => true,
                    'bar' => true,
                ]
            ],
        ]);
        $this->config->add('title', [
            'options' => [
                'stuff' => [
                    'bar' => false,
                    'baz' => true,
                ]
            ],
        ]);

        $expected = [
            'foo' => true,
            'bar' => false,
            'baz' => true,
        ];
        $actual = $this->config->getTypes(TypeConfig::CONTEXT_LIST)['title']['listOptions']['stuff'];
        $this->assertSame($expected, $actual);
    }

    public function testContextIsAlwaysOverwritten()
    {
        $this->config->add('title', [
            'type' => 'string',
            'contexts' => [TypeConfig::CONTEXT_VIEW, TypeConfig::CONTEXT_LIST],
        ]);
        $this->config->add('title', [
            'contexts' => [TypeConfig::CONTEXT_VIEW],
        ]);

        $this->assertSame(0, count($this->config->getTypes(TypeConfig::CONTEXT_LIST)));
        $this->assertSame(1, count($this->config->getTypes(TypeConfig::CONTEXT_VIEW)));
    }

    public function testSortCanBeDisabled()
    {
        $this->config->add('enabled', [
            'type' => 'boolean',
            'sort' => false,
        ]);
        $this->assertFalse($this->config->getTypes(TypeConfig::CONTEXT_LIST)['enabled']['sort']);
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
        $registry = $this->getMockBuilder(TypeRegistry::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $config = new TypeConfig($registry);
        $type = $this->getMock(TypeInterface::class);
        $registry->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        //contains options key, which should be normalised to the different contexts
        $defaultConfig = [
            'sort' => false,
            'options' => [
                'some_type_option' => 'foo'
            ],
            'listOptions' => [
                'some_type_option' => 'bar'
            ],
        ];
        $type->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue($defaultConfig));
        $config->add('title', ['type' => 'foo']);

        $resolved = $config->getTypes(TypeConfig::CONTEXT_LIST)['title'];
        $this->assertSame('foo', $resolved['viewOptions']['some_type_option']);
        $this->assertSame('bar', $resolved['listOptions']['some_type_option']);
    }

    public function testGetAllTypes()
    {
        $this->config->add('one', [
            'type' => 'string',
            'contexts' => [],
        ]);
        $this->config->add('two', [
            'type' => 'string',
            'contexts' => [TypeConfig::CONTEXT_LIST],
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
}
