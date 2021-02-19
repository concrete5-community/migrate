<?php

namespace A3020\Migrate\Profile;

use Concrete\Core\Config\Repository\Repository;

final class ProfileRepository
{
    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Find a profile by its handle.
     *
     * @param string $handle
     *
     * @return Profile|null
     */
    public function findByHandle($handle)
    {
        $profiles = $this->getProfiles();

        if (!array_key_exists($handle, $profiles)) {
            return null;
        }

        $profile = Profile::createByHandle($handle);

        return $profile->setProperties($profiles[$handle]);
    }

    /**
     * Get all profiles.
     *
     * @return Profile[]
     */
    public function all()
    {
        $objects = [];

        foreach ($this->getProfiles() as $handle => $properties) {
            $objects[] = Profile::createByHandle($handle)
                ->setProperties($properties);
        }

        return $objects;
    }

    /**
     * Get an array of profiles (no profile objects!)
     *
     * @return array
     */
    private function getProfiles()
    {
        return $this->config->get('migrate::profiles', []);
    }

    /**
     * Get an array with all profile handles.
     *
     * @return array
     */
    public function allHandles()
    {
        return array_keys(
            $this->getProfiles()
        );
    }

    /**
     * Add a profile to the config.
     *
     * @param Profile $profile
     */
    public function store(Profile $profile)
    {
        $profiles = $this->getProfiles();

        $profiles[$profile->getHandle()] = $profile->toArray();

        $this->config->save('migrate::profiles', $profiles);
    }

    /**
     * Removes a profile.
     *
     * @param Profile $profile
     */
    public function remove(Profile $profile)
    {
        $profiles = $this->getProfiles();

        if (array_key_exists($profile->getHandle(), $profiles)) {
            unset($profiles[$profile->getHandle()]);
        }

        $this->config->save('migrate::profiles', $profiles);
    }
}
