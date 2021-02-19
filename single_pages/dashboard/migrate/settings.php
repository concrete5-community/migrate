<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.migrate.settings');
        ?>

        <div class="form-group">
            <label class="control-label launch-tooltip"
               title="<?php echo t("If disabled, the database can't be pulled to another machine.") ?>"
               for="allowPull">
                <?php
                /** @var bool $allowPull */
                echo $form->checkbox('allowPull', 1, $allowPull);
                ?>
                <?php echo t('Enable database pulls'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
               title="<?php echo t("You need this token to create a profile and to authorize with this concrete5 installation.") ?>"
               for="authToken">
                <?php echo t('Authorization token'); ?>
            </label>
            <?php
            /** @var string $authToken */
            echo $form->textarea('authToken', $authToken, [
                'readonly' => 'readonly',
            ]);
            ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
               title="<?php echo t("Generate a new authorization token. If you check this, the old token will become useless.") ?>"
               for="regenerateAuthToken">
                <?php
                /** @var string $authToken */
                echo $form->checkbox('regenerateAuthToken', 1, false);
                ?>
                <?php echo t('Regenerate authorization token'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
               title="<?php echo t("The records from the tables below won't be imported.") ?>"
               for="skipTables">
                <?php echo t('Skip data from these tables'); ?>
            </label>
            <?php
            /** @var array $skipTables */
            echo $form->textarea('skipTables', implode("\n", $skipTables), [
                'placeholder' => t('Use one database table per line'),
                'rows' => 6,
            ]);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit">
                    <?php echo t('Save') ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
$("#authToken").click(function() {
    $(this).select();
});
</script>
