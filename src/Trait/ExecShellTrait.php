<?php

namespace Celtic34fr\ContactCore\Traits;

trait ExecShellTrait
{
    public function executeShellCommand(string $command): mixed
    {
        // Execute the command
        passthru($command, $result);
        return $result;        
    }
}