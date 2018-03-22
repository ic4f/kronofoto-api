<?php
namespace Kronofoto\Models;

abstract class Model
{
    public abstract function validateSort($criteria);

    public abstract function validateFilter();
}
