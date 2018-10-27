<?php

namespace App;

use Moloquent\Eloquent\Model as Moloquent;

class Ticket extends Moloquent
{
    protected $collection = 'tickets';
    protected $connection = 'mongodb';

    protected $dates = ['DateCreate'];

    public function filtrar($filtro)
    {
        return $this->where(function ($query) use ($filtro) {

            // Necessário fazer a verificação dos campos,
            // pois os mesmos não são obrigátorios

            if ($filtro->has('start_at')) {
                $query->where('DateCreate', '>=', $filtro->start_at);
            }

            if ($filtro->has('end_at')) {
                $query->where('DateCreate', '<=', $filtro->end_at);
            }

            if ($filtro->has('priority')) {
                $query->where('priority', $filtro->priority);
            }

        })->get();
    }
}
