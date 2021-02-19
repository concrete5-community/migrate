<?php

namespace A3020\Migrate\Handler\Steps;

final class EnableForeignKeys implements StepInterface
{
    public function run(StepContainer $stepContainer)
    {
        $stepContainer->shadowConnection
            ->executeQuery('SET FOREIGN_KEY_CHECKS=1;');
    }
}
