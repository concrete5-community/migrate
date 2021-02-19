<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Client\Client;
use A3020\Migrate\Dto\TableInfo;
use Concrete\Core\Config\Repository\Repository;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportRecords implements StepInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var StepContainer
     */
    private $stepContainer;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(Client $client, Repository $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param StepContainer $stepContainer
     *
     * @throws Exception
     *
     * @throws \Throwable
     */
    public function run(StepContainer $stepContainer)
    {
        $this->stepContainer = $stepContainer;

        // The profile contains the token.
        $this->client->setProfile($stepContainer->profile);

        $stepContainer->output->writeln('Starting the import');

        try {
            // Set up a progress bar.
            $stepContainer->progressBar = $this->createProgressBar(
                $stepContainer->output,
                $stepContainer->structureInformation->getTotalTables()
            );

            $this->importAllTables($stepContainer->structureInformation->getTables());
        } catch (Exception $e) {
            $stepContainer
                ->output->writeln('');

            $stepContainer
                ->output
                ->writeln('Something went wrong, cancelling import');

            throw $e;
        }

        $stepContainer->output->writeln('Table data import completed');
    }

    /**
     * @param TableInfo[] $tableInfos
     *
     * @throws \Throwable
     */
    public function importAllTables($tableInfos)
    {
        $progressBar = $this->stepContainer->progressBar;

        foreach ($tableInfos as $tableInfo) {
            $progressBar->setMessage($tableInfo->getName(), 'table');
            $progressBar->setMessage(0, 'records');
            $progressBar->advance();

            $this->importTableRecords($tableInfo, function($records) use ($progressBar) {
                $progressBar->setMessage($records, 'records');
                $progressBar->display();
            });
        }

        $progressBar->clear();
    }


    /**
     * Import one table, including its data.
     *
     * @param TableInfo $tableInfo
     * @param callable $callback The number of inserts will be passed to the callback.
     *
     * @throws \Throwable
     */
    private function importTableRecords(TableInfo $tableInfo, callable $callback)
    {
        // Don't import data if there are no records.
        if ($tableInfo->getTotalRows() === 0) {
            return;
        }

        if ($this->shouldSkipTable($tableInfo->getName())) {
            return;
        }

        // The start_at parameter will be modified in the while loop.
        $requestOptions = [
            'method' => 'records',
            'table' => $tableInfo->getName(),
            'start_at' => 0,
        ];

        $totalNumberOfInserts = 0;
        while (true) {
            $formerBatch = $totalNumberOfInserts;

            // Keep on fetching new records until we reach 'total_rows'.
            $result = $this->client->post($requestOptions);
            $result = json_decode($result, true);

            // Insert the received table data.
            $this->insert($tableInfo->getName(), $result['data'],
                function () use (&$totalNumberOfInserts, $callback) {
                    $totalNumberOfInserts++;

                    $callback($totalNumberOfInserts);
                }
            );

            // In the next request, start at row xxx.
            $requestOptions['start_at'] = $totalNumberOfInserts;

            // All records have been imported.
            if ((int) $result['remaining_rows'] === 0) {
                break;
            }

            // Apparently no new records are inserted.
            // Let's break here, to prevent an infinite loop.
            if ($formerBatch === $totalNumberOfInserts) {
                break;
            }
        }
    }

    /**
     * Insert the table records.
     *
     * The callback is to keep track of how many records have been inserted.
     *
     * @param string $table
     * @param array $data
     * @param callable|null $callback
     *
     * @throws \Throwable
     */
    private function insert($table, $data, callable $callback = null)
    {
        foreach ($data as $rowData) {
            if ($callback) {
                $callback();
            }

            // Get rid of the column names.
            $rowData = array_values($rowData);

            $qMarks = str_repeat('?,', count($rowData) - 1) . '?';

            $this->stepContainer->shadowConnection->executeQuery('INSERT INTO ' . $table . ' VALUES (' . $qMarks . ')',
                $rowData
            );
        }
    }

    /**
     * @param OutputInterface $output
     * @param int $numberOfTables
     *
     * @return ProgressBar
     */
    private function createProgressBar(OutputInterface $output, $numberOfTables)
    {
        $progress = new ProgressBar($output, $numberOfTables);
        $progress->setBarCharacter('<fg=magenta>=</>');
        $progress->setProgressCharacter("\xF0\x9F\x8D\xBA");
        $progress->setFormat(
            "<info>Table: %table%</info>\n" .
            "<info>Rows imported: %records%</info>\n" .
            $progress->getFormatDefinition('very_verbose')
        );
        $progress->setMessage('-', 'table');
        $progress->setMessage(0, 'records');
        $progress->start();

        return $progress;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function shouldSkipTable($name)
    {
        return in_array(
            $name,
            (array) $this->config->get('migrate::settings.skip_tables', [])
        );
    }
}
