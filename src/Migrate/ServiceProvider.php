<?php

namespace A3020\Migrate;

use A3020\Migrate\Client\Client;
use A3020\Migrate\Console\Command\CreateProfile;
use A3020\Migrate\Console\Command\ListProfiles;
use A3020\Migrate\Console\Command\PullDatabase;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Routing\RouterInterface;

final class ServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function boot()
    {
        $this->routes();

        $this->app->singleton(Client::class, Client::class);

        if ($this->app->isRunThroughCommandLineInterface()) {
            $console = $this->app->make('console');
            $console->add(new PullDatabase());
            $console->add(new CreateProfile());
            $console->add(new ListProfiles());
        }
    }

    private function routes()
    {
        $this->router->registerMultiple([
            '/ccm/system/migrate' => [
                '\A3020\Migrate\Controller\Endpoint::receive',
            ],
        ]);
    }
}
