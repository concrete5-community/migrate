<?php

namespace A3020\Migrate\Payload;

use A3020\Migrate\Database\ListTables;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;

final class Records
{
    /**
     * @var ListTables
     */
    private $listTables;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(ListTables $listTables, Connection $connection, Repository $config)
    {
        $this->listTables = $listTables;
        $this->connection = $connection;
        $this->config = $config;
    }

    /**
     * Used to export table data.
     *
     * @param $options
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array
     */
    public function get($options)
    {
        // Merge passed options with some defaults.
        $options = $options + [
            'max_rows' => (int) $this->config->get('migrate::settings.batch.max_rows', 500),
            'max_size' => (int) $this->config->get('migrate::settings.batch.max_size', 102400), // in bytes
        ];

        $result = [
            'remaining_rows' => 0,
            'data' => [],
        ];

        $size = 0;

        foreach ($this->connection
                 ->fetchAll('SELECT * FROM ' . $options['table']
                     . ' LIMIT ' . (int) $options['start_at'] . ','. $options['max_rows'])
             as $row) {

            // To prevent huge responses (and time outs),
            // we can't rely on only the max number of rows.
            $size += mb_strlen(json_encode($row));
            $maxSizeReached = $size >= $options['max_size'];

            // Apparently there's 1 big record. We have to include it
            // even though it exceeds the max size!
            if ($maxSizeReached && count($result['data']) === 0) {
                $result['data'][] = $row;
                break;
            }

            // One or more records are included and we hit the max size, let's break here.
            if ($maxSizeReached && count($result['data'])) {
                break;
            }

            // Max size not exceeded yet, keep adding records.
            $result['data'][] = $row;
        }

        // If this is 0, then the importer doesn't need to query this table any further.
        $returned = (int) $options['start_at'] + (count($result['data']));
        $result['remaining_rows'] = $this->getTableRows($options['table']) - $returned;

        return $result;
    }

    /**
     * Return the number of rows in a particular table.
     *
     * @param string $tableName
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return int
     */
    private function getTableRows($tableName)
    {
        return (int) $this->connection->fetchColumn('SELECT COUNT(1) FROM ' . $tableName);
    }
}
