<?php declare(strict_types=1);
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\Operation;

use function sprintf;
use PDO;
use PDOException;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Executes a truncate replacement to speed up things (delete from ...; alter table auto_increment = 1;).
 */
class DeleteReset implements Operation
{
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        $sql = !$this->isMysql($connection)
            ? 'DELETE FROM %1$s; ALTER TABLE %1$s AUTOINCREMENT=1;'
            : 'DELETE FROM %1$s; ALTER TABLE %1$s AUTO_INCREMENT=1;';

        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table ITable */
            $tname = $connection->quoteSchemaObject($table->getTableMetaData()->getTableName());
            $query = sprintf($sql, $tname);

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->query($query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof PDOException) {
                    throw new Exception('DELETE - RESET', $query, [], $table, $e->getMessage());
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
