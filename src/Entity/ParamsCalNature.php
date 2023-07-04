<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Traits\ParametersEntityTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ParamsCalNature extends Parameter
{
    const HEADER = [
        'name', 'backgroudColor', 'borderColor', 'textColor'
    ];
    const PARAM_CLE = "calNature";

    private ParameterRepository $repository;

    public function __construct(private LifecycleEventArgs $events)
    {
        parent::__construct();
        $this->repository = $events->getObjectManager()->getRepository(Parameter::class);
    }

    use ParametersEntityTrait;

    public function getName(): mixed
    {
        return $this->getItem('name');
    }

    public function setName(string $name): mixed
    {
        return $this->setItem('name', $name);
    }

    public function getBackgroundColor(): string
    {
        return $this->getItem('backgroundColor');
    }

    public function setBackgroundColor(string $backgroundColor)
    {
        return $this->setItem('backgroundColor', $backgroundColor);
    }

    public function getBorderColor(): string
    {
        return $this->getItem('borderColor');
    }

    public function setBorderColor(string $borderColor)
    {
        return $this->setItem('borderColor', $borderColor);
    }

    public function getTextColor(): string
    {
        return $this->getItem('textColor');
    }

    public function setTextColor(string $textColor)
    {
        return $this->setItem('textColor', $textColor);
    }
}
