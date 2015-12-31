<?php

namespace Tabbi89\Behat\SilexExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Silex\Application;
use Tabbi89\Behat\SilexExtension\Manager\ApplicationManager;
use Tabbi89\Behat\SilexExtension\Manager\Parameters;
use Tabbi89\Behat\SilexExtension\ServiceContainer\Driver\SilexFactory;

class SilexExtension implements Extension
{
    const KERNEL_ID = 'silex_extension.kernel';
    const KERNEL_AWARE_ID = 'silex_extension.context_initializer.kernel_aware';
    const KERNEL_MANAGER_ID = 'silex.kernel.manager';
    const SESSION_LISTENER_ID = 'silex.listener.sessions';

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new SilexFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $boolFilter = function ($v) {
            $filtered = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return (null === $filtered) ? $v : $filtered;
        };

        $builder
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('kernel')
                        ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('bootstrap')->defaultValue('app/autoload.php')->end()
                                ->scalarNode('path')->defaultValue('app/Application.php')->end()
                                ->scalarNode('class')->defaultValue(null)->end()
                                ->scalarNode('env')->defaultValue('test')->end()
                                ->booleanNode('debug')->beforeNormalization()->ifString()->then($boolFilter)->end()->defaultTrue()->end()
                                ->booleanNode('testSession')->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // get base path
        $basePath = $container->getParameter('paths.base');

        // find and require bootstrap
        $bootstrapPath = $container->getParameter(self::KERNEL_ID.'.bootstrap');
        if (strlen($bootstrapPath) > 0) {
            if (file_exists($bootstrap = $basePath.DIRECTORY_SEPARATOR.$bootstrapPath)) {
                require_once $bootstrap;
            } elseif (file_exists($bootstrapPath)) {
                require_once $bootstrapPath;
            }
        }

        $kernelPath = $container->getParameter(self::KERNEL_ID.'.path');

        $parameters = new Parameters([
            'env' => $container->getParameter(self::KERNEL_ID.'.env'),
            'debug' => $container->getParameter(self::KERNEL_ID.'.debug'),
            'appClass' => $container->getParameter(self::KERNEL_ID.'.class'),
            'app' => $basePath.DIRECTORY_SEPARATOR.$kernelPath,
            'testSession' => $container->getParameter(self::KERNEL_ID.'.testSession')
        ]);
        $kernelManager = new ApplicationManager($parameters);

        $container->set(self::KERNEL_ID, $kernelManager->boot());
        $container->set(self::KERNEL_MANAGER_ID, $kernelManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'silex';
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadContextInitializer($container);
        $this->loadKernel($container, $config['kernel']);
        $this->loadSessionsListener($container);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function loadKernel(ContainerBuilder $container, array $config)
    {
        $container->setParameter(self::KERNEL_ID . '.class', $config['class']);
        $container->setParameter(self::KERNEL_ID . '.env', $config['env']);
        $container->setParameter(self::KERNEL_ID . '.debug', $config['debug']);
        $container->setParameter(self::KERNEL_ID . '.path', $config['path']);
        $container->setParameter(self::KERNEL_ID . '.bootstrap', $config['bootstrap']);
        $container->setParameter(self::KERNEL_ID . '.testSession', $config['testSession']);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadSessionsListener(ContainerBuilder $container)
    {
        $definition = new Definition('Tabbi89\Behat\SilexExtension\Listener\SessionsListener', array(
            new Reference(self::KERNEL_MANAGER_ID),
            new Reference(MinkExtension::MINK_ID)
        ));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 1));
        $container->setDefinition(self::SESSION_LISTENER_ID, $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('Tabbi89\Behat\SilexExtension\Context\Initializer\ApplicationAwareInitializer', array(
            new Reference(self::KERNEL_MANAGER_ID),
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition(self::KERNEL_AWARE_ID, $definition);
    }
}
