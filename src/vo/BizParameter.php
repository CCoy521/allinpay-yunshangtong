<?php

namespace vo;

/**
 * 业务参数
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class BizParameter implements \JsonSerializable
{
    private $parameters = [];

    public function addParam(string $paramName, $paramValue)
    {
        $this->parameters[$paramName] = $paramValue;
    }

//    public function addParam(string $paramName, int $paramValue)
//    {
//        $this->parameters[$paramName] = $paramValue;
//    }

    public function addMapParam(string $paramName, array $paramMap)
    {
        $this->parameters[$paramName] = $paramMap;
    }

    public function addListParam(string $paramName, array $paramValue)
    {
        $this->parameters[$paramName] = $paramValue;
    }

    public function jsonSerialize(): array
    {
        return $this->parameters;
    }

    public function toString()
    {
        return json_encode($this);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
