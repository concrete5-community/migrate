<?php

namespace Concrete\Package\Migrate\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class Migrate extends DashboardPageController
{
    public function view()
    {
        return Redirect::to('/dashboard/migrate/settings');
    }
}
