<?php

namespace A3020\Migrate\Handler\Steps;

interface StepInterface
{
    public function run(StepContainer $stepContainer);
}
