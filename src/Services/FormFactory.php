<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Services;

use Haste\Form\Form;

class FormFactory
{
    public function create($id, $method, $callback)
    {
        return new Form($id, $method, $callback);
    }
}
