<?php

namespace Celtic34fr\ContactCore\Traits;

trait ParametersEntityTrait
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

    public function getValues(): array
    {
        $datas = explode("|", $this->getValeur());
        return array_combine(self::HEADER, $datas);
    }

    public function setValues(array $values)
    {
        $chaine = implode('|', $values);
        return $this->setValeur($chaine);
    }

    public function getItem(string $key): mixed
    {
        if (!array_key_exists($key, $this->getParamsListNames())) return false;
        return $this->getValues()[$key];
    }

    public function setItem(string $name, string $val): mixed
    {
        if (!array_key_exists($name, $this->getParamsListNames())) return false;
        $datas = $this->getValues();
        $datas[$name] = $val;
        return $this->setValues($datas);
    }

    public function getParamsListValues()
    {
        return $this->repository->getParamtersList(self::PARAM_CLE);
    }

    public function getParamsListNames()
    {
        $datas = $this->getParamsListValues(self::PARAM_CLE);
        $natures = [];
        foreach ($datas as $data) {
            $item = $data->getValeur();
            $natures[] = $item['name'];
        }
        return $natures;
    }
}
