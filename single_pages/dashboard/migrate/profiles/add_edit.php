<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var \A3020\Migrate\Profile\Profile $profile */

if ($profile->getHandle()) {
    ?>
    <div class="ccm-dashboard-header-buttons btn-group">
        <a class="btn btn-danger" href="<?php echo $this->action('delete', $profile->getHandle()); ?>">
            <?php echo t('Delete'); ?>
        </a>
    </div>
    <?php
}
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var \Concrete\Core\Validation\CSRF\Token $token */
        echo $token->output('a3020.migrate.profile');

        echo $form->hidden('oldHandle', $profile->getHandle());
        ?>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t('A profile handle is used in the CLI command. The handle is automatically transformed to lowercase and no spaces.') ?>"
                   for="handle">
                <?php
                echo t('Handle') .' *';
                ?>
            </label>
            <?php
            echo $form->text('handle', $profile->getHandle(), [
                'required' => 'required',
                'autofocus' => 'autofocus',
            ]);
            ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t('The base URL of the remote website you want to pull from.') ?>"
                   for="url">
                <?php echo t('URL') .' *'; ?>
            </label>
            <?php
            echo $form->text('url', $profile->getUrl(), [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t('Please find the token on the remote website on the settings page.') ?>"
                   for="token">
                <?php echo t('Token') .' *'; ?>
            </label>
            <?php
            echo $form->textarea('token', $profile->getToken(), [
                'required' => 'required',
            ]);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a class="btn btn-default" href="<?php echo $this->action(''); ?>">
                    <?php echo t('Cancel') ?>
                </a>

                <div class="pull-right">
                    <button class="btn btn-primary" name="save" type="submit">
                        <?php echo t('Save') ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
