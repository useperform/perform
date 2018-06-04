<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            #symfony
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),

            #doctrine
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            #other third party
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            #perform
            new Perform\BaseBundle\PerformBaseBundle(),
            new Perform\TeamBundle\PerformTeamBundle(),
            new Perform\NotificationBundle\PerformNotificationBundle(),
            new Perform\ContactBundle\PerformContactBundle(),
            new Perform\TwitterBundle\PerformTwitterBundle(),
            new Perform\BlogBundle\PerformBlogBundle(),
            new Perform\MediaBundle\PerformMediaBundle(),
            new Perform\EventsBundle\PerformEventsBundle(),
            new Perform\AnalyticsBundle\PerformAnalyticsBundle(),
            new Perform\MailingListBundle\PerformMailingListBundle(),
            new Perform\PageEditorBundle\PerformPageEditorBundle(),
            new Perform\MediaPlayerBundle\PerformMediaPlayerBundle(),

            #app
            new AppBundle\AppBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Perform\DevBundle\PerformDevBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
