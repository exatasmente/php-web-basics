<?php

namespace App\Requests\Contracts;

interface ValidatesRequestInterface
{
    public function validateRequest();

    public function getData();
}