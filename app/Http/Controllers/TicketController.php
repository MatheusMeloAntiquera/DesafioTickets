<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Palavras;
use Illuminate\Http\Request;
use DateTime;
use Validator;
use Illuminate\Validation\Rule;


class TicketController extends Controller
{
    //Peso por dia de atraso. Exemplo: 2 dias de atraso = 1 ponto
    private $pesoPorAtraso = 0.5;

    //Pontuacao minima para classificar um ticket como prioridade alta
    private $pontuacaoMinima = 5;

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

        //Simples validação dos campos da requisição
       $validator = Validator::make($request->all(), [
            'paginate' => 'numeric',
            'start_at' => 'date|date_format:Y-m-d',
            'end_at' => 'date|date_format:Y-m-d|after:start_at',
            'order_by' => [
                 Rule::in(['DateCreate','DateUpdate','Priority'])
            ],
            'asc' => 'boolean',
            'priority' => [
                 Rule::in(['alta','normal'])
            ]
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400, array(), JSON_PRETTY_PRINT);
        }

        $tickets = $ticketModel->filtrar($request);

        return response()->json($tickets, 200, array(), JSON_PRETTY_PRINT);
    }

    public function classificarTickets(Ticket $ticketModel)
    {


        try{
        //Busca todos os tickets
        $tickets = $ticketModel->all();

        $tickets->each(function ($ticket, $chave) {

            $pontuacao = $this->calcularPontuacao($ticket);

            $ticket->Score = $pontuacao;

            //Verifica a prioridade
            if($pontuacao >= $this->pontuacaoMinima){
                $ticket->Priority = 'alta';
            }else{
                $ticket->Priority = 'normal';
            }

            $ticket->save();
        });

        return redirect('api/');

        }catch(\Exception $e){

            $retorno = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
            return response()->json($retorno , 500, array(), JSON_PRETTY_PRINT);
        }

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

        // Retorna a pontuação das mensagens, levando em consideração "Assunto" e "Mensagem"
        $mensagens = $this->verificaMensagens($ticket);

        //Verifica a diferença de dias entre a data atual com a data de criação do ticket
        $prazo = $this->verificaPrazo($ticket);

        //Somatória das pontuações
        return $mensagens + $prazo;
    }

    private function verificaMensagens(Ticket $ticket)
    {
        $mensagens = $ticket->Interactions;
        $pontuacao = 0;
        $palavrasInsatisfacao = Palavras::all();

        foreach ($mensagens as $msg) {

            //Apenas verifica as mensagens do cliente
            if ($msg['Sender'] == 'Customer') {

                foreach ($palavrasInsatisfacao->all() as $p) {

                    $assunto = mb_strtolower($msg['Subject'],'UTF-8');
                    $mensagem = mb_strtolower($msg['Message'],'UTF-8');

                    /* ########## VERIFICAÇÃO DO ASSUNTO ########### */

                    //Busca no assunto se contem as palavras de insatisfação e também elimina as resposta, ou seja,
                    // assuntos quem tem a palavra "RE:"
                    if (strpos($assunto, $p['desc']) !== false && strpos($assunto, 're:') === false) {
                        $pontuacao += 5;
                    }

                    /* ########## VERIFICAÇÃO DA MENSAGEM ########### */
                    if (strpos($mensagem, $p['desc']) !== false) {
                        $pontuacao += 5;
                    }

                }
            }

        }

        return $pontuacao;
    }

    /**
     * Calcula a pontuação em relação ao prazo de resolução do ticket
     *
     * Verifica a diferença de dias entre a data atual com a data de criação do ticket
     * e multiplica pelo um peso para cada dia passado
     *
     * @param Ticket $ticket collection com dados sobre o ticket
     * @return float
     *
     **/
    private function verificaPrazo(Ticket $ticket){


        $dataCriacao = new DateTime($ticket->DateCreate);
        $dataHoje = new DateTime();

        //Peso por dia de atraso
        $pesoAtraso = 0.5;

        $intervalo = $dataCriacao->diff($dataHoje);

        //Total de dias passados entre as datas
        $dias = $intervalo->format('%a');

        return $dias * $this->pesoPorAtraso;
    }
}
