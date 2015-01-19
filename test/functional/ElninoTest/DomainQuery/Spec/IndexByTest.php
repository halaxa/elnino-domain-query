<?php
use Elnino\DomainQuery\DefaultSpecificationRepository;
use Elnino\DomainQuery\Spec\IndexBy;
use ElninoTest\DomainQuery\Entity\Person;
use ElninoTest\FunctionalTrait;

/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 19.1.2015
 * Time: 9:43
 */

class IndexByTest extends PHPUnit_Extensions_Database_TestCase
{
    use FunctionalTrait;

    public function testIndexBy()
    {
        $repo = new DefaultSpecificationRepository($this->getEm(), Person::class);
        $result = $repo->match('p', new IndexBy('p.id'));

        $this->assertCount(3, $result);
        $this->assertArrayNotHasKey(0, $result); // result is indexed by ids
    }

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/../Entity/testData.yml');
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        return new \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($this->getPdo());
    }
}
