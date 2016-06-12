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

namespace BenGorUser\DoctrineODMMongoDBBridgeBundle;

use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\Types\UserEmailType;
use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\Types\UserIdType;
use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\Types\UserPasswordType;
use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\Types\UserRolesType;
use BenGorUser\DoctrineODMMongoDBBridge\Infrastructure\Persistence\Types\UserTokenType;
use BenGorUser\DoctrineODMMongoDBBridgeBundle\DependencyInjection\Compiler\DoctrineODMMongoDBServicesPass;
use BenGorUser\UserBundle\DependentBenGorUserBundle;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Doctrine ODM MongoDB bridge bundle kernel class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class DoctrineODMMongoDBBridgeBundle extends Bundle
{
    use DependentBenGorUserBundle;

    /**
     * Constructor.
     */
    public function __construct()
    {
        Type::registerType('user_email', UserEmailType::class);
        Type::registerType('user_id', UserIdType::class);
        Type::registerType('user_password', UserPasswordType::class);
        Type::registerType('user_roles', UserRolesType::class);
        Type::registerType('user_token', UserTokenType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->checkDependencies(['BenGorUserBundle', 'DoctrineMongoDBBundle'], $container);

        $container->addCompilerPass(new DoctrineODMMongoDBServicesPass(), PassConfig::TYPE_OPTIMIZE);

        $container->loadFromExtension('doctrine_mongodb', [
            'document_managers' => [
                'default' => [
                    'mappings' => [
                        'BenGorUserBundle' => [
                            'type'      => 'yml',
                            'is_bundle' => true,
                            'prefix'    => 'BenGor\\User\\Domain\\Model',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
