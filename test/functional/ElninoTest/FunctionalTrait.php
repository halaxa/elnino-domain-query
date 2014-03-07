<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 7.3.14
 * Time: 7:53
 */

namespace ElninoTest;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\SchemaTool;

trait FunctionalTrait
{
    static private $conn;

    /** @var  EntityManager */
    static private $em;

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        if ( ! self::$em) {
            $config = new Configuration();
            $config->setProxyDir(__DIR__.'/../proxy');
            $config->setProxyNamespace('ElninoProxy');
            $driver = $config->newDefaultAnnotationDriver(__DIR__.'/DomainQuery/Entity', false);
            $config->setMetadataDriverImpl($driver);
            $config->setNamingStrategy(new UnderscoreNamingStrategy());

            $cache = new \Doctrine\Common\Cache\ArrayCache();
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);

            $conn = array(
                'driver' => 'pdo_sqlite',
                'memory' => true,
            );

            self::$em = EntityManager::create($conn, $config);

            $st = new SchemaTool(self::$em);
            $classes = self::$em->getMetadataFactory()->getAllMetadata();

            $st->dropSchema($classes);
            $st->createSchema($classes);
        }

        return self::$em;
    }

    public function getPdo()
    {
        return $this->getEm()->getConnection()->getWrappedConnection();
    }
} 
