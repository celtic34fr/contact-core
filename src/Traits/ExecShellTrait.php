<?php

namespace Celtic34fr\ContactCore\Trait;

trait ExecShellTrait
{
    public function executeShellCommand(string $command): mixed
    {
        // Execute the command
        passthru($command, $result);
        return $result;        
    }
}