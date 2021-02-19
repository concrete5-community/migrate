<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Database\DropDatabase;
use A3020\Migrate\Profile\ProfileRepository;

final class RemoveOldDatabases implements StepInterface
{
    /**
     * @var DropDatabase
     */
    private $dropDatabase;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        DropDatabase $dropDatabase
    )
    {
        $this->profileRepository = $profileRepository;
        $this->dropDatabase = $dropDatabase;
    }

    public function run(StepContainer $stepContainer)
    {
        $this->removeOldDatabases(function($database) use ($stepContainer) {
            $stepContainer
                ->output
                ->writeln("Dropping old database: " . $database);
        });
    }

    private function removeOldDatabases($callback)
    {
        foreach ($this->profileRepository->all() as $profile) {
            $databases = $profile->getDatabases();

            if (count($databases) === 0) {
                continue;
            }

            /// Keep the last database for each profile.
            $keep = array_pop($databases);

            // Let's drop all the others
            foreach ($databases as $database) {
                if ($this->dropDatabase
                    ->drop($database)) {
                    $callback($database);
                }
            }

            // Update the migrate.php config file
            $this->profileRepository
                ->store(
                    $profile->setDatabases([$keep])
                );
        }
    }
}
