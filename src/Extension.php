<?php

namespace Celtic34fr\ContactCore;

use Bolt\Extension\BaseExtension;

class Extension extends BaseExtension
{
    public function getName(): string
    {
        return 'Celtic34fr\ContactCore';
    }

    public function initialize(): void
    {
        dump("its working");
    }
}