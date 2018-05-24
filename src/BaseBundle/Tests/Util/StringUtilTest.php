<?php

namespace BaseBundle\Tests\Util;

use Perform\BaseBundle\Util\StringUtil;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    public function sensibleProvider()
    {
        return [
            ['password', 'Password'],
            ['user-id', 'User id'],
            ['EmailAddress', 'Email address'],
            ['date_format', 'Date format'],
            ['_save', 'Save'],
            ['_save_', 'Save'],
        ];
    }

    /**
     * @dataProvider sensibleProvider()
     */
    public function testSensibleLabelString($string, $expected)
    {
        $this->assertSame($expected, StringUtil::sensible($string));
    }

    public function previewProvider()
    {
        return [
            ['', ''],
            ['This is a short sentence.', 'This is a short sentence.'],
            ['This is a long sentence, with a lot of words and letters. It will surely have to be shortened.', 'This is a long sentence, with a lot of words and l…'],
        ];
    }

    /**
     * @dataProvider previewProvider()
     */
    public function testPreviewString($string, $expected)
    {
        $this->assertSame($expected, StringUtil::preview($string));
    }

    public function crudClassProvider()
    {
        return [
            ['AppBundle\Crud\TestCrud', 'Test'],
            ['AppBundle\Crud\MySuperEntityCrud', 'My Super Entity'],
            ['AppBundle\Crud\HTMLEntityCrud', 'HTML Entity'],
            ['AppBundle\Crud\CrudNamedDifferently', 'Crud Named Differently'],
        ];
    }

    /**
     * @dataProvider crudClassProvider()
     */
    public function testCrudClassToEntityName($class, $expected)
    {
        $this->assertSame($expected, StringUtil::crudClassToEntityName($class));
    }

    public function basenameProvider()
    {
        return [
            ['Foo', 'Foo'],
            ['AppBundle\Some\Long\Namespace\Service', 'Service'],
            ['AppBundle\MyService', 'MyService'],
        ];
    }

    /**
     * @dataProvider basenameProvider()
     */
    public function testClassBasename($class, $expected)
    {
        $this->assertSame($expected, StringUtil::classBasename($class));
    }

    public function entityClassForCrudProvider()
    {
        return [
            ['App\Crud\FooCrud', 'App\Entity\Foo'],
        ];
    }

    /**
     * @dataProvider entityClassForCrudProvider()
     */
    public function testEntityClassForCrud($class, $expected)
    {
        $this->assertSame($expected, StringUtil::entityClassForCrud($class));
    }

    public function basenameWithSuffixProvider()
    {
        return [
            ['FooBar', 'Bar', 'Foo'],
            ['AppBundle\Some\Long\Namespace\ServiceName', 'Name', 'Service'],
            ['AppBundle\MyService', 'Service', 'My'],
            // suffix not found
            ['AppBundle\MyService', 'Foo', 'MyService'],
            ['AppBundle\MyService', 'My', 'MyService'],
        ];
    }

    /**
     * @dataProvider basenameWithSuffixProvider()
     */
    public function testClassBasenameWithSuffix($class, $suffix, $expected)
    {
        $this->assertSame($expected, StringUtil::classBasename($class, $suffix));
    }

    public function crudTemplateProvider()
    {
        return [
            ['AppBundle:Foo', 'list', '@App/crud/foo/list.html.twig'],
            ['SomeOtherAppBundle:UserProfile', 'view', '@SomeOtherApp/crud/user_profile/view.html.twig'],
        ];
    }

    /**
     * @dataProvider crudTemplateProvider()
     */
    public function testCrudTemplateForEntity($entity, $context, $expected)
    {
        $this->assertSame($expected, StringUtil::crudTemplateForEntity($entity, $context));
    }
}
