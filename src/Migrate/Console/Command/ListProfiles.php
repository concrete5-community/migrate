<?php

namespace A3020\Migrate\Console\Command;

use A3020\Migrate\Profile\Profile;
use A3020\Migrate\Profile\ProfileRepository;
use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListProfiles extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('migrate:list-profiles')
            ->setDescription('List all profiles')
            ->setCanRunAsRoot(false);
        $this->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  {$errExitCode} errors occurred
EOT
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();

        /** @var ProfileRepository $profileRepository */
        $profileRepository = $app->make(ProfileRepository::class);

        $table = new Table($output);
        $table->setHeaders(['Handle', 'URL']);

        foreach ($profileRepository->all() as $profile) {
            $table->addRow([
                $profile->getHandle(),
                $profile->getUrl(),
            ]);
        }

        $table->render();
    }
}
