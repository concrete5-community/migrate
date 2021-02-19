<?php

namespace A3020\Migrate\Console\Command;

use A3020\Migrate\Handler\Pull;
use A3020\Migrate\Profile\ProfileRepository;
use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class PullDatabase extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('migrate:pull-database')
            ->setAliases(['pull', 'migrate:pull-db'])
            ->setDescription('Pulls a remote database to this environment while keeping a copy.')
            ->setCanRunAsRoot(false)
            ->addArgument('profile', InputOption::VALUE_REQUIRED, "The handle of a profile.")
        ;
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
     * @return int
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();

        /** @var ProfileRepository $profileRepository */
        $profileRepository = $app->make(ProfileRepository::class);

        if (count($profileRepository->allHandles()) === 0) {
            $output->writeln('<error>Please create a profile first.</error>');

            return 1;
        }

        $profile = $profileRepository->findByHandle($input->getArgument('profile'));
        if (!$profile) {
            $output->writeln('<error>Please provide a valid profile handle.</error>');
            $output->writeln('');
            $output->writeln('Available profiles: ' . implode(', ', $profileRepository->allHandles()));

            return 1;
        }

        $output->writeln('');

        /** @var Pull $handler */
        $handler = $app->make(Pull::class);
        $handler->handle($profile, $output);

        // All went fine.
        return 0;
    }
}
