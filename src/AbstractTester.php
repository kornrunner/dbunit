<?php declare(strict_types=1);
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\Operation\Operation;

/**
 * Can be used as a foundation for new DatabaseTesters.
 */
abstract class AbstractTester implements Tester
{
    /**
     * @var Operation
     */
    protected $setUpOperation;

    /**
     * @var Operation
     */
    protected $tearDownOperation;

    /**
     * @var IDataSet
     */
    protected $dataSet;

    /**
     * @var string
     */
    protected $schema;

    /**
     * Creates a new database tester.
     */
    public function __construct()
    {
        $this->setUpOperation    = Factory::CLEAN_INSERT_NO_TRUNCATE();
        $this->tearDownOperation = Factory::NONE();
    }

    /**
     * Closes the specified connection.
     */
    public function closeConnection(Connection $connection): void
    {
        $connection->close();
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp(): void
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown(): void
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Sets the test dataset to use.
     */
    public function setDataSet(IDataSet $dataSet): void
    {
        $this->dataSet = $dataSet;
    }

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema): void
    {
        $this->schema = $schema;
    }

    /**
     * Sets the DatabaseOperation to call when starting the test.
     */
    public function setSetUpOperation(Operation $setUpOperation): void
    {
        $this->setUpOperation = $setUpOperation;
    }

    /**
     * Sets the DatabaseOperation to call when ending the test.
     */
    public function setTearDownOperation(Operation $tearDownOperation): void
    {
        $this->tearDownOperation = $tearDownOperation;
    }

    /**
     * Returns the schema value.
     *
     * @return string
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the database operation that will be called when starting the test.
     *
     * @return Operation
     */
    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * Returns the database operation that will be called when ending the test.
     *
     * @return Operation
     */
    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}
