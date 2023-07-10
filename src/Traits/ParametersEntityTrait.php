<?php

namespace Celtic34fr\ContactCore\Traits;

use Celtic34fr\ContactCore\Entity\Parameter;

trait ParametersEntityTrait
{
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

    public function persist($entity): void
    {
        $parameter = new Parameter();
        $parameter->setCle($entity->gerCle());
        $parameter->setOrd($entity->getOrd());
        $parameter->setValeur($entity->getValeur());
        $this->em->flush($parameter);
    }
}
