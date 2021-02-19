<?php

namespace A3020\Migrate\Installer;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;

final class Installer implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Concrete\Core\Package\Package $pkg
     */
    public function install($pkg)
    {
        $this->installPages($pkg);
        $this->generateAuthToken();

        if (!$this->config->has('migrate::settings.skip_tables')) {
            // By default, skip the data from these database tables.
            $this->config->save('migrate::settings.skip_tables', [
                'DownloadStatistics',
                'JobsLogs',
                'Logs',
            ]);
        }
    }

    /**
     * @param \Concrete\Core\Package\Package $pkg
     */
    public function installPages($pkg)
    {
        $pages = [
            '/dashboard/migrate' => 'Migrate',
            '/dashboard/migrate/settings' => 'Settings',
            '/dashboard/migrate/profiles' => 'Profiles',
            '/dashboard/migrate/help' => 'Help',
        ];

        // Using for loop because additional pages
        // may be added in the future.
        foreach ($pages as $path => $name) {
            /** @var Page $page */
            $page = Page::getByPath($path);
            if ($page && !$page->isError()) {
                continue;
            }

            $singlePage = Single::add($path, $pkg);
            $singlePage->update([
                'cName' => $name,
            ]);
        }
    }

    /**
     * The token can be used by other instances as a way to authenticate.
     */
    private function generateAuthToken()
    {
        if ($this->config->get('migrate::auth.token')) {
            return;
        }

        /** @var \Concrete\Core\Utility\Service\Identifier $token */
        $token = $this->app->make('helper/validation/identifier')
            ->getString(256);

        $this->config->save('migrate::auth.token', $token);
    }
}
