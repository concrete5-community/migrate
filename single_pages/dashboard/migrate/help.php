<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/** @var string $executablePath */
/** @var array $profileHandles */
?>

<div class="ccm-dashboard-content-inner">
    <div class="migrate-section">
        <h3><?php echo t('Workflow'); ?></h3>

        <ol>
            <li><?php echo t('Install the add-on on both websites.'); ?></li>
            <li>
                <?php
                echo t(/*i18n: Where %s are anchor tags */ '%sCreate a profile%s on the website you want to pull to.',
                    '<a href="' . Url::to('/dashboard/migrate/profiles/add') . '">',
                    '</a>');
                ?>
            </li>
            <li><?php echo t('Open a terminal and pull the database via a CLI command.'); ?></li>
        </ol>
    </div>
    <br>
    <div class="migrate-section">
        <h3><?php echo t('Command Line Interface'); ?></h3>
        <div class="migrate-help">
            <strong><?php echo t('List available commands'); ?></strong><br>
            <code>
                <?php echo $executablePath ?> migrate
            </code>
        </div>

        <div class="migrate-help">
            <strong><?php echo t('Create a profile'); ?></strong><br>
            <code>
                <?php echo $executablePath ?> migrate:create-profile production https://website.com longtoken345345345
            </code><br>
            <?php echo t('The three arguments are the profile handle, the target website, and the access token.'); ?>
        </div>

        <div class="migrate-help">
            <strong><?php echo t('List profiles'); ?></strong><br>
            <code>
                <?php echo $executablePath ?> migrate:list-profiles
            </code>
        </div>

        <?php
        if (count($profileHandles)) {
            ?>
            <div class="migrate-help">
                <strong><?php echo t('Pull a database'); ?></strong><br>
                The <i>pull</i> command is an alias for <i>migrate:db:pull</i>.<br>
                <?php
                foreach ($profileHandles as $profile) {
                    ?>
                    <code>
                        <?php echo $executablePath ?> pull <?php echo h($profile) ?>
                    </code><br>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>

</div>

<style>
.migrate-section {
    padding: 0 20px;
    border: 1px solid #ddd;
}

.migrate-help {
    margin-bottom: 20px;
}
</style>