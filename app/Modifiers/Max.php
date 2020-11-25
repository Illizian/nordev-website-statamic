<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class Max extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        if ($param = array_get($params, 0)) {
            return min($value, array_get($context, $param, $param));
        }

        return $value;
    }
}
