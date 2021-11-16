<?php

namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;

class HtmlEntitiesDecode extends AbstractFilter
{
    public function filter($value)
    {
        if (!is_scalar($value)) {
            return $value;
        }
        $value = (string) $value;

        return html_entity_decode($value);
    }
}
