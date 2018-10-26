<?php

namespace App;

use Moloquent\Eloquent\Model as Moloquent;

class Ticket extends Moloquent
{
    protected $collection = 'tickets';
    protected $connection = 'mongodb';
}
