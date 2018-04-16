<?php
namespace Kronofoto\Models;

abstract class Model
{
    public function validateSort($criteria)
    {
        if (!in_array($criteria, $this->getSortCriteria())) {
            throw new \Exception('Invalid sort criteria');
        }
        return true;
    }

    public function validateFilter($criteria)
    {
        if (!in_array($criteria, $this->getFilterCriteria())) {
            throw new \Exception('Invalid filter criteria');
        }
        return true;
    }

    protected abstract function getSortCriteria();

    protected abstract function getFilterCriteria();

    public abstract function getName();
}
