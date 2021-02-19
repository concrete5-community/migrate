<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Profile\ProfileRepository;

final class StoreDatabaseInConfig implements StepInterface
{
    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function run(StepContainer $stepContainer)
    {
        $this->profileRepository
            ->store($stepContainer->profile->addDatabase(
                $stepContainer->shadowConnection->getDatabase()
            ));
    }
}
