<?php

namespace Concrete\Package\Migrate\Controller\SinglePage\Dashboard\Migrate;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class Settings extends DashboardPageController
{
    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $this->set('allowPull', (bool) $config->get('migrate::settings.allow_pull', false));
        $this->set('authToken', $config->get('migrate::auth.token'));
        $this->set('skipTables', (array) $config->get('migrate::settings.skip_tables', []));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.migrate.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/migrate/settings');
        }

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->save('migrate::settings.allow_pull', (bool) $this->post('allowPull'));

        // Regenerate the authorization token if needed
        if ($this->request->request->has('regenerateAuthToken')) {
            /** @var \Concrete\Core\Utility\Service\Identifier $token */
            $token = $this->app->make('helper/validation/identifier')
                ->getString(256);

            $config->save('migrate::auth.token', $token);
        }

        $config->save('migrate::settings.skip_tables', $this->getSkipTablesFromPost());

        $this->flash('success', t('Your settings have been saved.'));

        return Redirect::to('/dashboard/migrate/settings');
    }

    /**
     * @return array
     */
    private function getSkipTablesFromPost()
    {
        $tables = explode("\n", str_replace("\r", '', $this->post('skipTables')));

        return array_map('trim', $tables);
    }
}
