<?php

namespace App\WebServices\Printer\Base;


class Core {

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {

            return $this->$property;
        }
    }

}