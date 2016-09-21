<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Entity\User;
use Perform\BaseBundle\Type\StringType;

/**
 * StringTypeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class StringTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $this->type = new StringType();
        $user = new User();
    }

    public function testCreateContext()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $builder->expects($this->once())
            ->method('add')
            ->with('forename', 'Symfony\Component\Form\Extension\Core\Type\TextType');

        $this->type->createContext($builder, 'forename');
    }

    public function testEditContext()
    {
        $builder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $builder->expects($this->once())
            ->method('add')
            ->with('forename', 'Symfony\Component\Form\Extension\Core\Type\TextType');

        $this->type->createContext($builder, 'forename');
    }

    public function testListContext()
    {
        $user = new User();
        //html escaping is the job of twig functions in the presentation layer
        $user->setForename('<p>foo</p>');
        $this->assertSame('<p>foo</p>', $this->type->listContext($user, 'forename'));
    }
}
