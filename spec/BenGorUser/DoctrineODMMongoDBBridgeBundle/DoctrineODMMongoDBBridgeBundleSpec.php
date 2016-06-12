<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\BenGorUser\DoctrineODMMongoDBBridgeBundle;

use BenGorUser\DoctrineODMMongoDBBridgeBundle\DependencyInjection\Compiler\DoctrineODMMongoDBServicesPass;
use BenGorUser\DoctrineODMMongoDBBridgeBundle\DoctrineODMMongoDBBridgeBundle;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Spec file of DoctrineODMMongoDBBridgeBundle class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DoctrineODMMongoDBBridgeBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DoctrineODMMongoDBBridgeBundle::class);
    }

    function it_extends_symfony_bundle()
    {
        $this->shouldHaveType(Bundle::class);
    }

    function it_builds_without_dependent_bundles_enabled(ContainerBuilder $container)
    {
        $this->shouldThrow(RuntimeException::class)->duringBuild($container);
    }

    function it_builds(ContainerBuilder $container)
    {
        $container->getParameter('kernel.bundles')->shouldBeCalled()->willReturn([
            'BenGorUserBundle'      => 'BenGorUser\\UserBundle\\BenGorUserBundle',
            'DoctrineMongoDBBundle' => 'Doctrine\\Bundle\\MongoDBBundle\\DoctrineMongoDBBundle',
        ]);

        $container->addCompilerPass(
            new DoctrineODMMongoDBServicesPass(), PassConfig::TYPE_OPTIMIZE
        )->shouldBeCalled()->willReturn($container);

        $container->loadFromExtension('doctrine_mongodb', [
            'document_managers' => [
                'default' => [
                    'mappings' => [
                        'DoctrineODMMongoDBBridgeBundle' => [
                            'type'      => 'yml',
                            'is_bundle' => true,
                            'prefix'    => 'BenGorUser\\User\\Domain\\Model',
                        ],
                    ],
                ],
            ],
        ])->shouldBeCalled()->willReturn($container);

        $this->build($container);
    }
}
