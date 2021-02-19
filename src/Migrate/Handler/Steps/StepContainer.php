<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Dto\StructureInformation;
use A3020\Migrate\Profile\Profile;
use Concrete\Core\Database\Connection\Connection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class StepContainer
{
    /** @var Profile */
    public $profile;

    /** @var Connection */
    public $shadowConnection;

    /** @var OutputInterface */
    public $output;

    /** @var StructureInformation */
    public $structureInformation;

    /** @var ProgressBar */
    public $progressBar;

    protected $startTime;

    public function startTimer()
    {
        $this->startTime = microtime(true);
    }

    // Show how long the migration took.
    public function elapsed()
    {
        return microtime(true) - $this->startTime;
    }
}
