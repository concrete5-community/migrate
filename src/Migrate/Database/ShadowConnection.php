<?php

namespace A3020\Migrate\Database;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Connection\ConnectionFactory;

/**
 * The shadow connection connects with the new / migrated database.
 */
final class ShadowConnection
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    public function __construct(Repository $config, ConnectionFactory $connectionFactory)
    {
        $this->config = $config;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Create a new Connection object that points to the new database.
     *
     * @param string $name
     *
     * @return Connection $connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function get($name)
    {
        $defaultConnection = current($this->config['database.connections']);
        $defaultConnection['database'] = $name;

        return $this->connectionFactory->createConnection($defaultConnection);
    }
}
