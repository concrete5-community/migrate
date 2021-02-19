<?php

namespace A3020\Migrate\Handler\Steps;

final class DisableForeignKeys implements StepInterface
{
    /**
     * @param StepContainer $stepContainer
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run(StepContainer $stepContainer)
    {
        // The tables are imported from A-Z.
        // Foreign keys checks need to be disabled to prevent constraint errors.
        // It's not needed to set it back afterwards, because it's session based.
        $stepContainer->shadowConnection
            ->executeQuery('SET FOREIGN_KEY_CHECKS=0;');
    }
}
