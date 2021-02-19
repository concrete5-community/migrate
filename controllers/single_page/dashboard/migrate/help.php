<?php

namespace Concrete\Package\Migrate\Controller\SinglePage\Dashboard\Migrate;

use A3020\Migrate\Profile\ProfileRepository;
use Concrete\Core\Page\Controller\DashboardPageController;

final class Help extends DashboardPageController
{
    public function view()
    {
        $this->set('executablePath', $this->getExecutablePath());
        $this->set('profileHandles', $this->getProfileHandles());
    }

    /**
     * @return string
     */
    private function getExecutablePath()
    {
        if ($this->isComposerBased()) {
            return './vendor/bin/concrete5';
        }

        return './concrete/bin/concrete5';
    }

    /**
     * @return bool
     */
    private function isComposerBased()
    {
        return basename(getcwd()) === 'public';
    }

    private function getProfileHandles()
    {
        /** @var ProfileRepository $repo */
        $repo = $this->app->make(ProfileRepository::class);

        return $repo->allHandles();
    }
}
