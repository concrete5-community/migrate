<?php

namespace A3020\Migrate\Database;

use Concrete\Core\Database\Connection\Connection;

final class ListTables
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get an array with table information and the table sql structure.
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function get()
    {
        $this->analyzeAllTables();

        // Fetch the names and their size
        $data = $this->connection
            ->fetchAll('
            SELECT
                TABLE_NAME AS `name`,
                (DATA_LENGTH + INDEX_LENGTH) AS `size`,
                TABLE_ROWS AS `total_rows`
            FROM information_schema.TABLES 
            WHERE table_schema = ?
            ORDER BY table_name ASC
        ', [
            $this->connection->getDatabase(),
        ]);

        // Build up information about the structure of the tables.
        foreach ($data as $index => &$tableInfo) {
            // Make sure integers are returned instead of strings.
            $tableInfo['size'] = (int) $tableInfo['size'];
            $tableInfo['total_rows'] = (int) $tableInfo['total_rows'];

            $result = $this->connection->fetchAll('SHOW CREATE TABLE ' . $tableInfo['name']);
            if (isset($result[0]['Create Table'])) {
                // The second column of the first row contains the SQL
                $data[$index]['structure'] = $result[0]['Create Table'];
            }
        }

        return $data;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Makes sure the information from the information schema is accurate.
     *
     * If this is not executed, the row count can be incorrect, for example.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function analyzeAllTables()
    {
        foreach ($this->getTables() as $table) {
            $this->connection->executeQuery('ANALYZE TABLE ' . current($table));
        }
    }

    private function getTables()
    {
        return $this->connection
            ->fetchAll('SHOW TABLES');
    }
}
