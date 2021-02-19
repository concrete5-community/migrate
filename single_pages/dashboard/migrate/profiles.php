<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var \A3020\Migrate\Profile\Profile[] $profiles */
?>

<div class="ccm-dashboard-header-buttons">
    <a class="btn btn-default" href="<?php echo $this->action('add'); ?>">
        <i class="fa fa-plus"></i> <?php echo t('Add Profile'); ?>
    </a>
</div>

<div class="ccm-dashboard-content-inner">
    <?php
    if (count($profiles) === 0) {
        echo '<p>' . t('No profiles have been found.') . '</p>';
    } else {
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo t('Handle'); ?></th>
                    <th><?php echo t('URL'); ?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($profiles as $profile) {
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo $this->action('edit', $profile->getHandle()); ?>">
                                <?php echo e($profile->getHandle()); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo e($profile->getUrl()); ?>
                        </td>
                        <td>
                            <a class="btn btn-default btn-xs" href="<?php echo $this->action('edit', $profile->getHandle()); ?>">
                                <?php echo t('Edit'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    ?>
</div>
