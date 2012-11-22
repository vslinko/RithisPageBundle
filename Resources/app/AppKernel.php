<?php

use Symfony\Component\Config\Loader\LoaderInterface,
    Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Rithis\PageBundle\RithisPageBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function getCacheDir()
    {
        return sprintf("%s/symfony-bundle-%s/cache", sys_get_temp_dir(), md5(__DIR__));
    }

    public function getLogDir()
    {
        return sprintf("%s/symfony-bundle-%s/logs", sys_get_temp_dir(), md5(__DIR__));
    }
}
