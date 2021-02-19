<?php

namespace A3020\Migrate\Handler\Steps;

use A3020\Migrate\Client\Client;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

final class RemoteRequestStats implements ApplicationAwareInterface, StepInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function run(StepContainer $stepContainer)
    {
        $stepContainer->output->writeln('Total requests made to remote server: ' .
            $this->client->getNumberOfRequests(),
            OutputInterface::VERBOSITY_VERBOSE
        );
    }
}
