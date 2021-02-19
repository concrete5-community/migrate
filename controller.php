<?php

namespace Concrete\Package\Migrate;

use A3020\Migrate\Installer\Installer;
use A3020\Migrate\ServiceProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;

final class Controller extends Package
{
    protected $pkgHandle = 'migrate';
    protected $appVersionRequired = '8.2.1';
    protected $pkgVersion = '1.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Migrate' => '\A3020\Migrate',
    ];

    public function getPackageName()
    {
        return t('Migrate');
    }

    public function getPackageDescription()
    {
        return t('Database migration / synchronization');
    }

    public function on_start()
    {
        $provider = $this->app->make(ServiceProvider::class);
        $provider->boot();
    }

    public function install()
    {
        $pkg = parent::install();

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle($this->pkgHandle);

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }
}
