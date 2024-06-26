<?php declare(strict_types=1);
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\DataSet;

/**
 * Provides basic functionality for table meta data.
 */
abstract class AbstractTableMetadata implements ITableMetadata
{
    /**
     * The names of all columns in the table.
     *
     * @var array
     */
    protected $columns;

    /**
     * The names of all the primary keys in the table.
     *
     * @var array
     */
    protected $primaryKeys;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     */
    public function matches(ITableMetadata $other)
    {
        if ($this->getTableName() != $other->getTableName() ||
            $this->getColumns() != $other->getColumns()
        ) {
            return false;
        }

        return true;
    }
}
