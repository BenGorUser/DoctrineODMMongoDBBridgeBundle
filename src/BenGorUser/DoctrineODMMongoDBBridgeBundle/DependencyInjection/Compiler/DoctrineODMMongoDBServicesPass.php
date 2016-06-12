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

namespace BenGorUser\DoctrineODMMongoDBBridgeBundle\DependencyInjection\Compiler;

use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\DoctrineODMMongoDBUserRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register Doctrine ODM MongoDB services compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DoctrineODMMongoDBServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bengor_user.config');
        foreach ($config['user_class'] as $key => $user) {
            if ('doctrine_odm_mongodb' !== $user['persistence']) {
                continue;
            }

            $container->setDefinition(
                'bengor.user.infrastructure.persistence.' . $key . '_repository',
                (new Definition(
                    DoctrineODMMongoDBUserRepository::class, [
                        $user['class'],
                    ]
                ))->setFactory([
                    new Reference('doctrine.odm.mongodb.document_manager'), 'getRepository',
                ])->setPublic(false)
            );
            $container->setAlias(
                'bengor_user.' . $key . '.repository',
                'bengor.user.infrastructure.persistence.' . $key . '_repository'
            );
        }
    }
}
