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
        $delete_stmnt = 'DELETE FROM %1$s;';
        $reset_stmnt = $this->getResetSequenceStatement($connection);

        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table ITable */
            $tname = $connection->quoteSchemaObject($table->getTableMetaData()->getTableName());
            $delete_query = sprintf($delete_stmnt, $tname);
            $reset_query = sprintf($reset_stmnt, $tname);

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $c = $connection->getConnection();
                $c->query($delete_query);
                $c->query($reset_query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof PDOException) {
                    throw new Exception('DELETE - RESET', "$delete_query $reset_query", [], $table, $e->getMessage());
                }

                throw $e;
            }
        }
    }

    private function getResetSequenceStatement(Connection $connection): string
    {
        switch($connection->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                $statement = 'ALTER TABLE %1$s AUTO_INCREMENT = 1;';
                break;
            case 'sqlite':
                $statement = 'DELETE FROM sqlite_sequence WHERE name=%1$s;';
                break;
            default:
                $statement = 'ALTER TABLE %1$s AUTO_INCREMENT = 1;';
        }

        return $statement;
    }

    private function disableForeignKeyChecksForMysql(Connection $connection): void
    {
        if ($this->isMysql($connection)) {
            $c = $connection->getConnection();
            $c->query('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
            $c->query('SET FOREIGN_KEY_CHECKS = 0');
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
