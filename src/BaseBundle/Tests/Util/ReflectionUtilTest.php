<?php

namespace Perform\BaseBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\ParentEntity;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\ChildEntity;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\OneTrait;
use Perform\BaseBundle\Tests\Fixtures\ReflectionUtil\TwoTrait;
use Perform\BaseBundle\Util\ReflectionUtil;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ReflectionUtilTest extends TestCase
{
    public function usesTraitProvider()
    {
        return [
            [ParentEntity::class, OneTrait::class],
            [ChildEntity::class, OneTrait::class],
            [ChildEntity::class, TwoTrait::class],
        ];
    }

    /**
     * @dataProvider usesTraitProvider
     */
    public function testHasTrait($classname, $traitname)
    {
        $this->assertTrue(ReflectionUtil::usesTrait($classname, $traitname));
    }
}
