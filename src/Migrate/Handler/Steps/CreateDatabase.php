<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Database\ShadowConnection;
use Concrete\Core\Database\Connection\Connection;
use Exception;

final class CreateDatabase implements StepInterface
{
    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    protected $schemaManager;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ShadowConnection
     */
    private $shadowConnection;

    public function __construct(Connection $connection, ShadowConnection $shadowConnection)
    {
        $this->connection = $connection;

        $this->schemaManager = $connection->getSchemaManager();
        $this->shadowConnection = $shadowConnection;
    }

    /**
     * @param StepContainer $container
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run(StepContainer $container)
    {
        // Get the name for the new database.
        $newName = $this->getNewName(
            $container->profile->getHandle()
        );

        // Create the new database and get the connection.
        $container->shadowConnection = $this->create($newName);

        $container->output
            ->writeln('Creating database ' . $container->shadowConnection->getDatabase());
    }

    /**
     * Create a new database and return a new connection.
     *
     * @param string $name
     *
     * @return Connection $connection
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws Exception
     */
    public function create($name)
    {
        if ($this->exists($name)) {
            throw new Exception('Database already exists');
        }

        // Create database
        $this->connection
            ->getSchemaManager()
            ->createDatabase($name);

        // Return new connection
        return $this->shadowConnection
            ->get($name);
    }

    /**
     * Generate a name for the new database.
     *
     * @param string $profileHandle
     *
     * @return string
     */
    public function getNewName($profileHandle)
    {
        // E.g. a3020__production_20181109_101523
        $name = $this->connection->getDatabase();

        // E.g. a3020
        $name = current(explode('__', $name));

        // E.g. a3020__production_20181110_112334
        return $name . '__' . $profileHandle . '_' . date('Ymd_His');
    }


    /**
     * Check if the database name is unique.
     *
     * @param $name
     *
     * @return bool
     */
    private function exists($name)
    {
        return in_array($name, $this->schemaManager->listDatabases());
    }
}
