<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;

class TicketController extends Controller
{
    public function index(){
        $tickets = Ticket::all();

        /* return response()->json($tickets); */
        return response()->json($tickets, 200, array(), JSON_PRETTY_PRINT);

    }

    public function filtrar(Request $request){

    }


    public function classificarTickets(Ticket $ticketModel){
        
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

    private function calcularPontuacao(Ticket $ticket ){
       echo $ticket->CustomerName . "<br>";                  
    }
}
