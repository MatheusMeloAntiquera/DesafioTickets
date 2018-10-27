<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use DB;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();

        return response()->json($tickets, 200, array(), JSON_PRETTY_PRINT);
    }

     /**
     * Realiza o filtro de acordo com os paramêtros passados
     *
     *
     * @param Ticket $ticketModel objeto da class Ticket (Model)
     * @param Request $request dados da requisição
     * @return response
     *
     **/

    public function filtrar(Ticket $ticketModel, Request $request)
    {
        $tickets = $ticketModel->filtrar($request);
               
        return response()->json($tickets, 200, array(), JSON_PRETTY_PRINT);
    }

    public function classificarTickets(Ticket $ticketModel)
    {

        //Busca todos os tickets
        $tickets = $ticketModel->all();

        $tickets->each(function ($ticket, $chave) {

            $pontuacao = $this->calcularPontuacao($ticket);

        });

    }
    /**
     * Calcula a pontuação de cada Ticket
     *
     * Após o calculo é retornado o valor para que o ticket
     * possa ser classificado
     *
     * @param Ticket $ticket collection com dados sobre o ticket
     * @return float
     *
     **/

    private function calcularPontuacao(Ticket $ticket)
    {
        echo $ticket->CustomerName . "<br>";
    }
}
