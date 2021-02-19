<?php

namespace A3020\Migrate\Handler;

use A3020\Migrate\Handler\Steps\CreateDatabase;
use A3020\Migrate\Handler\Steps\CreateTableStructure;
use A3020\Migrate\Handler\Steps\DisableForeignKeys;
use A3020\Migrate\Handler\Steps\EnableForeignKeys;
use A3020\Migrate\Handler\Steps\ImportRecords;
use A3020\Migrate\Handler\Steps\RemoteRequestStats;
use A3020\Migrate\Handler\Steps\RemoveOldDatabases;
use A3020\Migrate\Handler\Steps\StepContainer;
use A3020\Migrate\Handler\Steps\StepInterface;
use A3020\Migrate\Handler\Steps\StoreDatabaseInConfig;
use A3020\Migrate\Handler\Steps\SwitchDatabase;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

final class Pull implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * Pull a remote database and activate it.
     *
     * @param \A3020\Migrate\Profile\Profile $profile
     * @param OutputInterface $output
     *
     * @throws \Throwable
     */
    public function handle($profile, OutputInterface $output)
    {
        /** @var StepContainer $stepContainer */
        $stepContainer = $this->app->make(StepContainer::class);
        $stepContainer->profile = $profile;
        $stepContainer->output = $output;

        $stepContainer->startTimer();

        foreach ([
            CreateDatabase::class,
            StoreDatabaseInConfig::class,
            DisableForeignKeys::class,
            CreateTableStructure::class,
            ImportRecords::class,
            EnableForeignKeys::class,
            SwitchDatabase::class,
            RemoveOldDatabases::class,
            RemoteRequestStats::class,
        ] as $class) {
            /** @var StepInterface $step */
            $step = $this->app->make($class);
            $step->run($stepContainer);
        }

        $output->writeln(
            sprintf('<info>Operation completed in %s seconds (%s minutes)</info>',
                round($stepContainer->elapsed(), 2),
                round($stepContainer->elapsed() / 60, 1)
            )
        );
    }
}
