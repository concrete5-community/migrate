<?php

namespace A3020\Migrate\Console\Command;

use A3020\Migrate\Profile\Profile;
use A3020\Migrate\Profile\ProfileRepository;
use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateProfile extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('migrate:create-profile')
            ->setDescription('Create a profile')
            ->setCanRunAsRoot(false)
            ->addArgument('handle', InputOption::VALUE_REQUIRED, "The handle of a profile.")
            ->addArgument('url', InputOption::VALUE_REQUIRED, 'The URL of the remote server.')
            ->addArgument('token', InputOption::VALUE_REQUIRED, 'The authorization token.')
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
     * @return int|null|void
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handle = $input->getArgument('handle');
        if (empty($handle)) {
            throw new Exception("Please provide a valid handle, e.g. 'production'.");
        }

        $url = $input->getArgument('url');
        if (empty($url)) {
            throw new Exception("Please provide a valid url, e.g. 'https://website.com'.");
        }

        $token = $input->getArgument('token');
        if (empty($token)) {
            throw new Exception("Please provide a valid token. This can be found on the remote website on the Settings page.");
        }

        $this->storeProfile(
            Profile::createByHandle($handle)
                ->setToken($token)
                ->setUrl($url)
        );

        $output->writeln('Profile has been created');
    }

    private function storeProfile(Profile $profile)
    {
        $app = Application::getFacadeApplication();

        /** @var ProfileRepository $profileRepository */
        $profileRepository = $app->make(ProfileRepository::class);

        // If you want to edit your profiles, go to
        // application/config/generated_overrides/migrate.php
        $profileRepository->store($profile);
    }
}
