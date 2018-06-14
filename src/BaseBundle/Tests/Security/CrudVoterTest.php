<?php

namespace Perform\BaseBundle\Tests\Security;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Perform\BaseBundle\Security\CrudVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Perform\BaseBundle\Routing\CrudUrlGeneratorInterface;
use Perform\BaseBundle\Crud\CrudRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Perform\BaseBundle\Crud\CrudRequest;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudVoterTest extends \PHPUnit_Framework_TestCase
{
    protected $mockVoter;
    protected $voter;
    protected $crudRegistry;
    protected $urlGenerator;
    protected $em;
    protected $voters;

    public function setUp()
    {
        $this->mockVoter = $this->getMock(VoterInterface::class);
        $this->crudRegistry = $this->getMockBuilder(CrudRegistry::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $this->urlGenerator = $this->getMock(CrudUrlGeneratorInterface::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->voter = $this->voter = new CrudVoter(
            $this->crudRegistry,
            $this->urlGenerator,
            $this->em
        );
        $this->voters = [
            $this->mockVoter,
            $this->voter,
        ];
        $this->token = $this->getMock(TokenInterface::class);
    }

    private function mockVoterResult($result)
    {
        $this->mockVoter->expects($this->any())
            ->method('vote')
            ->will($this->returnValue($result));
    }

    private function crudExists($crudName, $exists = true)
    {
        $this->crudRegistry->expects($this->any())
            ->method('has')
            ->with($crudName)
            ->will($this->returnValue($exists));
    }

    private function entityHasCrud($classname, $exists = true)
    {
        $meta = $this->getMock(ClassMetadataFactory::class);
        $this->em->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($meta));
        $meta->expects($this->any())
            ->method('isTransient')
            ->will($this->returnValue(false));

        $this->crudRegistry->expects($this->any())
            ->method('hasForEntity')
            ->with($classname)
            ->will($this->returnValue($exists));
    }

    private function routeExists($crudName, $context, $exists = true)
    {
        $this->urlGenerator->expects($this->any())
            ->method('routeExists')
            ->with($crudName, $context)
            ->will($this->returnValue($exists));
    }

    public function testGrantedWithCrudName()
    {
        $this->crudExists('some_crud', true);
        $this->routeExists('some_crud', CrudRequest::CONTEXT_VIEW, true);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, 'some_crud', ['VIEW']));
    }

    public function testGrantedWithEntity()
    {
        $this->entityHasCrud(\stdClass::class, true);
        $this->routeExists('some_crud', CrudRequest::CONTEXT_VIEW, true);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, new \stdClass(), ['VIEW']));
    }

    public function testAbstainWithNoCrud()
    {
        $this->crudExists('some_crud', false);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, 'some_crud', ['VIEW']));
    }

    public function testAbstainWithNoRoute()
    {
        $this->crudExists('some_crud', true);
        $this->routeExists('some_crud', CrudRequest::CONTEXT_VIEW, false);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, 'some_crud', ['VIEW']));
    }

    public function testGrantedAffirmativeWhenOthersAbstain()
    {
        $dm = new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_AFFIRMATIVE);
        $this->entityHasCrud(\stdClass::class, true);
        $this->routeExists('some_crud', CrudRequest::CONTEXT_VIEW, true);
        $this->mockVoterResult(VoterInterface::ACCESS_ABSTAIN);
        $this->assertTrue($dm->decide($this->token, ['VIEW'], new \stdClass()));
    }

    public function testNoCrudDoesNotChangeUnanimousGranted()
    {
        $dm = new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_UNANIMOUS);
        $this->mockVoterResult(VoterInterface::ACCESS_GRANTED);
        $this->crudExists('some_crud', false);
        $this->assertTrue($dm->decide($this->token, ['VIEW'], 'some_crud'));
    }

    public function testNoCrudDoesNotChangeAffirmativeGranted()
    {
        $dm = new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_AFFIRMATIVE);
        $this->mockVoterResult(VoterInterface::ACCESS_GRANTED);
        $this->crudExists('some_crud', false);
        $this->assertTrue($dm->decide($this->token, ['VIEW'], 'some_crud'));
    }
}
