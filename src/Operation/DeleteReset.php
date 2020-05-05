<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PDO;
use PDOException;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Executes a truncate replacement to speed up things (delete from ...; alter table auto_increment = 1;)
 */
class DeleteReset implements Operation
{
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table ITable */
            $tname = $connection->quoteSchemaObject($table->getTableMetaData()->getTableName());
            $delete_query = "DELETE FROM {$tname};";
            $truncate_query = "ALTER TABLE {$tname} AUTO_INCREMENT=1;";

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->query($delete_query);
                $connection->getConnection()->query($truncate_query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof PDOException) {
                    throw new Exception('DELETE - RESET', "$delete_query $truncate_query", [], $table, $e->getMessage());
                }

                throw $e;
            }
        }
    }

    private function disableForeignKeyChecksForMysql(Connection $connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeyChecksForMysql(Connection $connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS=@PHPUNIT_OLD_FOREIGN_KEY_CHECKS');
        }
    }

    private function isMysql(Connection $connection)
    {
        return $connection->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql';
    }
}
