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

use function in_array;
use PHPUnit\DbUnit\InvalidArgumentException;

/**
 * The default implementation of table meta data.
 */
class DefaultTableMetadata extends AbstractTableMetadata
{
    /**
     * Creates a new default table meta data object.
     *
     * @param string $tableName
     */
    public function __construct($tableName, array $columns, array $primaryKeys = [])
    {
        $this->tableName   = $tableName;
        $this->columns     = $columns;
        $this->primaryKeys = [];

        foreach ($primaryKeys as $columnName) {
            if (!in_array($columnName, $this->columns, true)) {
                throw new InvalidArgumentException('Primary key column passed that is not in the column list.');
            }
            $this->primaryKeys[] = $columnName;
        }
    }
}
