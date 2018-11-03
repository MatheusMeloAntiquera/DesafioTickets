<?php

namespace App;

use Moloquent\Eloquent\Model as Moloquent;

class Palavras extends Moloquent
{
    protected $collection = 'palavras';
    protected $connection = 'mongodb';
}
