<?php

namespace A3020\Migrate\Database;

use Concrete\Core\Database\Connection\Connection;

final class DropDatabase
{
    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $schemaManager;

    public function __construct(Connection $connection)
    {
        $this->schemaManager = $connection->getSchemaManager();
    }

    public function drop($databaseName)
    {
        if (!$this->exists($databaseName)) {
            return false;
        }

        $this->schemaManager
            ->dropDatabase($databaseName);

        return true;
    }

    /**
     * Only try to remove the database if it actually exists.
     *
     * @param $databaseName
     *
     * @return bool
     */
    private function exists($databaseName)
    {
        return in_array($databaseName, $this->schemaManager->listDatabases());
    }
}
