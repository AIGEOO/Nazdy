<?php

declare(strict_types=1);

class Generator
{
    public function refreshValue(array $values)
    {
        $results = [];

        foreach ($values as $value)
        {
            $results[] = call_user_func($value);
        } 

        return $results;
    }
}