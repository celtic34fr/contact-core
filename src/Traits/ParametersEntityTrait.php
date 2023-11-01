<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Traits;

use Bolt\Extension\Celtic34fr\ContactCore\Entity\Parameter;
use DateTimeImmutable;

trait ParametersEntityTrait
{
    public function getParam(): Parameter
    {
        return $this->param;
    }

    /**
     * Set the value of param
     * @return  self
     */
    public function setParam(Parameter $param)
    {
        $this->param = $param;
        return $this;
    }

    public function getId(): mixed
    {
        return $this->param->getId();
    }

    public function getOrd(): mixed
    {
        return $this->param->getOrd() ?? false;
    }

    /**
     * Set the ord of param
     * @return  self
     */
    public function setOrd(int $ord)
    {
        $this->param->setOrd($ord);
        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->param->getUpdatedAt();
    }

    public function isEmptyUpdatedAt()
    {
        return $this->param->isEmptyUpdatedAt();
    }

    /**
     * Set the value of updated_at
     * @return  self
     */
    public function setUpdatedAt(DateTimeImmutable $updated_at): self
    {
        $this->param->setUpdatedAt($updated_at);
        return $this;
    }

    public function getValues(): array
    {
        $datas = $this->param->getValeur() ?? [];
        if ($datas) {
            $datas = explode("|", $datas);
            return $this->array_combine(self::HEADER, $datas);
        }
        return [];
    }

    public function setValues(array $values)
    {
        $chaine = implode('|', $values);
        return $this->param->setValeur($chaine);
    }

    public function getItem(string $key): mixed
    {
        if (!array_key_exists($key, $this->getParamsListNames())) return false;
        return $this->getValues()[$key];
    }

    public function setItem(string $name, string $val): mixed
    {
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
        /** @var Parameter $data */
        foreach ($datas as $data) {
            $item = $data->getValeur();
            $item = explode("|", $item);
            $item = $this->array_combine(self::HEADER, $item);
            $natures[] = $item['name'];
        }
        return $natures;
    }

    private function array_combine(array $keys, array $values): array
    {
        $arrayComnined = [];
        $maxIdx = max(sizeof($keys), sizeof($values));
        for ($idx = 0; $idx < $maxIdx; $idx++) {
            $lkey = array_key_exists($idx, $keys) ? $keys[$idx] : $idx;
            $lvalue = array_key_exists($idx, $values) ? $values[$idx] : null;
            $arrayComnined[$lkey] = $lvalue;
        }
        return $arrayComnined;
    }
}
