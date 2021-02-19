<?php

namespace Concrete\Package\Migrate\Controller\SinglePage\Dashboard\Migrate;

use A3020\Migrate\Profile\Profile;
use A3020\Migrate\Profile\ProfileRepository;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class Profiles extends DashboardPageController
{
    public function view()
    {
        $this->set('profiles', $this->getProfilesRepository()->all());
    }

    public function add()
    {
        $this->set('pageTitle', t('Add profile'));

        $profile = new Profile();

        return $this->addEdit($profile);
    }

    public function edit($handle = null)
    {
        $profile = $this->getProfilesRepository()->findByHandle($handle);
        if (!$profile) {
            $this->flash('error', t('Profile not found.'));

            return Redirect::to('/dashboard/migrate/profiles');
        }

        $this->set('pageTitle', t('Edit profile'));

        return $this->addEdit($profile);
    }

    private function addEdit(Profile $profile)
    {
        $this->set('profile', $profile);

        return $this->render('/dashboard/migrate/profiles/add_edit');
    }

    public function save()
    {
        if (!$this->token->validate('a3020.migrate.profile')) {
            $this->flash('error', $this->token->getErrorMessage());

            return;
        }

        /** @var Request $request */
        $request = $this->app->make(Request::class);

        $profile = $this->getProfilesRepository()->findByHandle(
            $request->request->get('oldHandle')
        );

        if ($profile) {
            // Remove the old profile first, otherwise duplicates
            // are created if the handle changes.
            $this->getProfilesRepository()->remove($profile);
        }

        if (!$profile) {
            $profile = new Profile();
        }

        /** @var \Concrete\Core\Utility\Service\Text $th */
        $th = $this->app->make('helper/text');

        $profile->setHandle($th->handle($request->request->get('handle')));
        $profile->setUrl(trim(strtolower($request->request->get('url'))));
        $profile->setToken(trim($request->request->get('token')));

        $this->getProfilesRepository()->store($profile);

        $this->flash('success', t('Profile has been saved.'));

        return Redirect::to('/dashboard/migrate/profiles');
    }

    public function delete($handle = null)
    {
        $profile = $this->getProfilesRepository()->findByHandle($handle);
        if (!$profile) {
            $this->flash('error', t('Profile not found.'));

            return Redirect::to('/dashboard/migrate/profiles');
        }

        $this->flash('success', t('Profile has been removed.'));

        $this->getProfilesRepository()->remove($profile);

        return Redirect::to('/dashboard/migrate/profiles');
    }

    /**
     * @return ProfileRepository
     */
    private function getProfilesRepository()
    {
        return $this->app->make(ProfileRepository::class);
    }
}
