<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Client\Client;
use A3020\Migrate\Dto\StructureInformation;
use A3020\Migrate\Dto\TableInfo;
use Concrete\Core\Database\Connection\Connection;
use Exception;

final class CreateTableStructure implements StepInterface
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param StepContainer $stepContainer
     *
     * @throws Exception
     */
    public function run(StepContainer $stepContainer)
    {
        // The profile contains the token.
        $this->client->setProfile($stepContainer->profile);

        $stepContainer->output->writeln('Fetching table information');
        $stepContainer->structureInformation = $this->fetchStructureInformation();

        foreach ($stepContainer->structureInformation->getTables() as $tableInfo) {
            $stepContainer->output->writeln(sprintf(
                'Creating table structure for %s',
                $tableInfo->getName()
            ));

            $this->createStructure(
                $stepContainer->shadowConnection,
                $tableInfo->getSqlStructure()
            );
        }
    }

    /**
     * Returns information about all tables.
     *
     * @return StructureInformation
     *
     * @throws Exception
     */
    private function fetchStructureInformation()
    {
        $result = json_decode($this->client->post([
            'method' => 'structure',
        ]), true);

        $structureInformation = new StructureInformation();
        $structureInformation->setTotalTables($result['total_tables']);
        $structureInformation->setTotalSize($result['total_size']);

        foreach ($result['tables'] as $table) {
            $tableInfo = new TableInfo();
            $tableInfo->setName($table['name']);
            $tableInfo->setSize($table['size']);
            $tableInfo->setTotalRows($table['total_rows']);
            $tableInfo->setSqlStructure($table['structure']);

            $structureInformation->addTable($tableInfo);
        }

        return $structureInformation;
    }

    /**
     * Create the structure of a table.
     *
     * @param Connection $connection
     * @param string $query E.g. "CREATE TABLE `Areas`..."
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createStructure(Connection $connection, $query)
    {
        $query = str_replace(
            'CREATE TABLE',
            'CREATE TABLE IF NOT EXISTS',
            $query
        );

        $connection->executeQuery($query);
    }
}
