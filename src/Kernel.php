<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    //https://github.com/symfony/symfony/issues/37005
    //https://symfony.com/doc/current/service_container/compiler_passes.html
    public function process(ContainerBuilder $container): void
    {
        if (php_sapi_name() === 'cli') {
            $container->removeDefinition('messenger.listener.dispatch_pcntl_signal_listener');
            $container->removeDefinition('messenger.listener.stop_worker_on_sigterm_signal_listener');
        }
    }
}
