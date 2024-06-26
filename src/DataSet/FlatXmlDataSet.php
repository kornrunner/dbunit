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

use function count;
use function in_array;
use PHPUnit\DbUnit\RuntimeException;

/**
 * The default implementation of a data set.
 */
class FlatXmlDataSet extends AbstractXmlDataSet
{
    protected function getTableInfo(array &$tableColumns, array &$tableValues): void
    {
        if ($this->xmlFileContents->getName() != 'dataset') {
            throw new RuntimeException('The root element of a flat xml data set file must be called <dataset>');
        }

        foreach ($this->xmlFileContents->children() as $row) {
            $tableName = $row->getName();

            if (!isset($tableColumns[$tableName])) {
                $tableColumns[$tableName] = [];
                $tableValues[$tableName]  = [];
            }

            $values = [];

            foreach ($row->attributes() as $name => $value) {
                if (!in_array($name, $tableColumns[$tableName], true)) {
                    $tableColumns[$tableName][] = $name;
                }

                $values[$name] = $value;
            }

            if (count($values)) {
                $tableValues[$tableName][] = $values;
            }
        }
    }
}
