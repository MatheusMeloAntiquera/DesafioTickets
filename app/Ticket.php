<?php

namespace App;

use Moloquent\Eloquent\Model as Moloquent;

class Ticket extends Moloquent
{
    protected $collection = 'tickets';
    protected $connection = 'mongodb';

    protected $dates = ['DateCreate'];

    public $timestamps = false;

    //Define quais colunas podem ser ordenadas
    protected $colunasOrder = ['DateCreate','DateUpdate','Priority'];

    public function filtrar($filtro)
    {
        //Determina o limite de registros por página
        $limitePaginas = !empty($filtro->paginate) ? intval($filtro->paginate) : 3;


        //Ordenação
        //Define a ordenação default
        $coluna = '_id';
        $direcao = 'desc';

        //Se teve o parametro "order_by", então verifica se a coluna informada pode ser ordenada
        if ($filtro->has('order_by') && in_array($filtro->order_by, $this->colunasOrder)) {
                $coluna = $filtro->order_by;

                //Define a direção: asc ou desc. Default: desc
                if(isset($filtro->asc) && $filtro->asc == true){
                    $direcao = 'asc';
                }
        }

        //Define os "where"
        return $this->where(function ($query) use ($filtro) {

            // Necessário fazer a verificação dos campos,
            // pois os mesmos não são obrigátorios

            if ($filtro->has('start_at')) {
                $query->where('DateCreate', '>=', $filtro->start_at);
            }

            if ($filtro->has('end_at')) {
                $query->where('DateCreate', '<=', $filtro->end_at);
            }

            if ($filtro->has('Priority')) {
                $query->where('Priority', $filtro->priority);
            }

        })->orderBy($coluna, $direcao)
        ->paginate($limitePaginas);


    }
}
