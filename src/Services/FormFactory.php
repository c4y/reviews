<?php

/*
 * This file is part of c4y/reviews.
 *
 * (c) Oliver Lohoff
 *
 * @license MIT License
 */

namespace C4Y\Reviews\Services;

use Codefog\HasteBundle\Form\Form;

class FormFactory
{
    /**
     * @param $id
     * @param $method
     * @param $callback
     * @return Form
     */
    public function create($id, $method, $callback): Form
    {
        return new Form($id, $method, $callback);
    }
}
