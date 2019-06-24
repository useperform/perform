<?php

namespace Perform\BaseBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\DependencyInjection\PerformBaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Perform\BaseBundle\EventListener\SimpleMenuListener;
use Perform\BaseBundle\PerformBaseBundle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\InMemoryFactory;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension;
use Perform\BaseBundle\Settings\Manager\ParametersManager;
use Perform\BaseBundle\Settings\Manager\CacheableManager;
use Perform\BaseBundle\Settings\Manager\DoctrineManager;
use Perform\BaseBundle\Settings\Manager\WriteableSettingsManagerInterface;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformBaseExtensionTest extends TestCase
{
    public function testSimpleMenus()
    {
        $ext = new PerformBaseExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.bundles', []);
        $config = [
            'menu' => [
                'simple' => [
                    'test' => [
                        'route' => 'some_route',
                    ]
                ]
            ],
        ];
        $ext->load([$config], $container);

        $listenerService = $container->findDefinition('perform_base.menu.simple.test');
        $this->assertSame(SimpleMenuListener::class, $listenerService->getClass());
        $this->assertSame('some_route', $listenerService->getArgument(2));
    }

    private function loadContainer(array $config = [], $compile = true)
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => true,
            'kernel.bundles' => [],
            'kernel.bundles_metadata' => [],
            'kernel.charset' => 'UTF-8',
            'kernel.secret' => 'secret',
            'kernel.project_dir' => '/path/to/project',
            'kernel.root_dir' => '/path/to/project',
            'kernel.cache_dir' => '/path/to/project',
            'kernel.name' => 'test',
            'kernel.environment' => 'test',
            'kernel.container_class' => 'TestContainer',
            'locale' => 'en',
        ]));
        $container->registerExtension(new FrameworkExtension());
        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => '',
            ],
        ]);
        $security = new SecurityExtension();
        $security->addUserProviderFactory(new InMemoryFactory());
        $container->registerExtension($security);
        $container->loadFromExtension('security', [
            'providers' => [
                'test' => [
                    'memory' => [
                        'users' => [],
                    ]
                ],
            ],
            'firewalls' => [
                'main' => [
                    'anonymous' => true,
                ],
            ]
        ]);
        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', [
            'dbal' => [],
            'orm' => [],
        ]);
        $container->registerExtension(new TwigExtension());
        $container->loadFromExtension('twig', [
            'strict_variables' => true,
        ]);
        (new TwigBundle)->build($container);
        $container->registerExtension(new MonologExtension());
        $container->loadFromExtension('monolog', []);

        $container->registerExtension(new PerformBaseExtension());
        $container->loadFromExtension('perform_base', $config);
        $bundle = new PerformBaseBundle();
        $bundle->build($container);
        // can be removed when test.container exists
        $container->addCompilerPass(new MakePublicServicesPass([
            'perform_base.settings_manager',
            SettingsManagerInterface::class,
            WriteableSettingsManagerInterface::class,
        ]));

        if ($compile) {
            $container->compile();
        }

        return $container;
    }

    public function testSettingsManagers()
    {
        // default
        $container = $this->loadContainer();
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(ParametersManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertFalse($container->has(WriteableSettingsManagerInterface::class));

        // default with cache
        $container = $this->loadContainer([
            'settings' => [
                'cache' => 'cache.app',
            ]
        ]);
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(CacheableManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertFalse($container->has(WriteableSettingsManagerInterface::class));

        // doctrine
        $container = $this->loadContainer([
            'settings' => [
                'manager' => 'doctrine',
            ]
        ]);
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(DoctrineManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertTrue($container->has(WriteableSettingsManagerInterface::class));
        $this->assertSame($manager, $container->findDefinition(WriteableSettingsManagerInterface::class));

        // non-writeable service
        $container = $this->loadContainer([
            'settings' => [
                'manager' => 'app.settings',
            ]
        ], false);
        $container->register('app.settings', ParametersManager::class);
        $container->compile();
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(ParametersManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertFalse($container->has(WriteableSettingsManagerInterface::class));

        // non-writeable service with cache
        $container = $this->loadContainer([
            'settings' => [
                'manager' => 'app.settings',
                'cache' => 'cache.app',
            ]
        ], false);
        $container->register('app.settings', ParametersManager::class);
        $container->compile();
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(CacheableManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertFalse($container->has(WriteableSettingsManagerInterface::class));

        // writeable service
        $container = $this->loadContainer([
            'settings' => [
                'manager' => 'app.settings',
            ]
        ], false);
        $container->register('app.settings', DoctrineManager::class);
        $container->compile();
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(DoctrineManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertTrue($container->has(WriteableSettingsManagerInterface::class));
        $this->assertSame($manager, $container->findDefinition(WriteableSettingsManagerInterface::class));

        // writeable service with cache
        $container = $this->loadContainer([
            'settings' => [
                'manager' => 'app.settings',
                'cache' => 'cache.app',
            ]
        ], false);
        $container->register('app.settings', DoctrineManager::class);
        $container->compile();
        $manager = $container->findDefinition('perform_base.settings_manager');
        $this->assertSame(CacheableManager::class, $manager->getClass());
        $this->assertSame($manager, $container->findDefinition(SettingsManagerInterface::class));
        $this->assertTrue($container->has(WriteableSettingsManagerInterface::class));
        $this->assertSame($manager, $container->findDefinition(WriteableSettingsManagerInterface::class));
    }
}
