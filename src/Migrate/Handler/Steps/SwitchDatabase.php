<?php

namespace A3020\Migrate\Handler\Steps;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Renderer;
use Concrete\Core\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;

final class SwitchDatabase implements ApplicationAwareInterface, StepInterface
{
    use ApplicationAwareTrait;

    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(DatabaseManager $databaseManager, Filesystem $filesystem)
    {
        $this->databaseManager = $databaseManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Swap the active database to the migrated one!
     *
     * @param StepContainer $stepContainer
     */
    public function run(StepContainer $stepContainer)
    {
        // Change the database name in the database.php config file.
        $this->filesystem->put(
            DIR_CONFIG_SITE . '/database.php',
            $this->getNewDatabaseConfig($stepContainer->shadowConnection->getDatabase())
        );

        // Disconnect and flush cache.
        $this->databaseManager->purge();

        // Reconnect to the migrated database.
        $this->databaseManager->reconnect();
    }

    /**
     * @param string $databaseName
     *
     * @return string
     */
    private function getNewDatabaseConfig($databaseName)
    {
        $config = require DIR_CONFIG_SITE . '/database.php';
        $config['connections']['concrete']['database'] = $databaseName;

        /** @var Renderer $renderer */
        $renderer = $this->app->make(Renderer::class, [$config]);

        return $renderer->render();
    }
}
