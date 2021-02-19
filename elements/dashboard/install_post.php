<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>

<p><?php echo t("Congratulations, %s has been installed!", t('Migrate')); ?></p>
<br>

<p>
    <a class="btn btn-default" href="<?php echo Url::to('/dashboard/migrate') ?>">
        <?php
        echo t('System & Settings / Migrate');
        ?>
    </a>
</p>
