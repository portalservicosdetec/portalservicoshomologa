<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Licitacao{

  /**
   * Identificador único da Licitação
   * @var integer
   */
  public $codigo;

  /**
   * Número da Licitação
   * @var string
   */
  public $numerodalicitacao;

  /**
   * Número do processo da Licitação
   * @var string
   */
  public $numerodoprocesso;

  /**
   * Modalidade da Licitação
   * @var string
   */
  public $modalidade;

  /**
   * Tipo da Licitação
   * @var string
   */
  public $tipodalicitacao;

  /**
   * Tipo do julgamento da Licitação
   * @var string
   */
  public $tipodejulgamento;

  /**
   * Objeto da Licitação
   * @var string
   */
  public $objeto;

  /**
   * Situação da Licitação
   * @var string
   */
  public $situacao;

  /**
   * Dia da Licitação
   * @var string
   */
  public $diapublicacao;

  /**
   * Mês da Licitação
   * @var string
   */
  public $mespublicacao;

  /**
   * Ano da Licitação
   * @var string
   */
  public $anopublicacao;

  /**
   * Dia da Licitação
   * @var string
   */
  public $diajulgamento;

  /**
   * Mês da Licitação
   * @var string
   */
  public $mesjulgamento;

  /**
   * Ano da Licitação
   * @var string
   */
  public $anojulgamento;

  /**
   * Método responsável por cadastrar um nova localização no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');


    //INSERIR UMA NOVA EVENTO NO BANCO
    $obDatabase = new Database('licitacao',NAME_EVENTO);
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->codigo = $obDatabase->insert([
                                      'numerodalicitacao' => $this->numerodalicitacao,
                                      'numerodoprocesso' => $this->numerodoprocesso,
                                      'modalidade' => $this->modalidade,
                                      'tipodalicitacao' => $this->tipodalicitacao,
                                      'tipodejulgamento' => $this->tipodejulgamento,
                                      'objeto' => $this->objeto,
                                      'situacao' => $this->situacao,
                                      'diapublicacao' => $this->diapublicacao,
                                      'mespublicacao' => $this->mespublicacao,
                                      'anopublicacao' => $this->anopublicacao,
                                      'diajulgamento' => $this->diajulgamento,
                                      'mesjulgamento' => $this->mesjulgamento,
                                      'anojulgamento' => $this->anojulgamento
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar a localização no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('licitacao'))->update('codigo = '.$this->codigo,[
                                                                'numerodalicitacao' => $this->numerodalicitacao,
                                                                'numerodoprocesso' => $this->numerodoprocesso,
                                                                'modalidade' => $this->modalidade,
                                                                'tipodalicitacao' => $this->tipodalicitacao,
                                                                'tipodejulgamento' => $this->tipodejulgamento,
                                                                'objeto' => $this->objeto,
                                                                'situacao' => $this->situacao,
                                                                'diapublicacao' => $this->diapublicacao,
                                                                'mespublicacao' => $this->mespublicacao,
                                                                'anopublicacao' => $this->anopublicacao,
                                                                'diajulgamento' => $this->diajulgamento,
                                                                'mesjulgamento' => $this->mesjulgamento,
                                                                'anojulgamento' => $this->anojulgamento
                                                              ]);
  }

  /**
   * Método responsável por excluir uma localização do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('licitacao','emerjco_licitacao'))->delete('codigo = '.$this->codigo);
  }

  /**
   * Método responsável por obter as localizações do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getLicitacoes($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('licitacao','emerjco_licitacao'))->select($where,$order,$limit,$fields);
  }


  /**
   * Método responsável por buscar uma localização com base em seu ID
   * @param integer $id
   * @return Licitacao
   */
  public static function getLicitacaoPorId($codigo){
    return self::getLicitacoes('codigo = '.$codigo)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de tipo de IC com base em seu nome
   * @param string $nome
   * @return Usuario
   */
  public static function getLicitacaoPorSituacao($situacao){
      return (new Database('licitacao','emerjco_licitacao'))->select('situacao = "'.$situacao.'"')->fetchObject(self::class);
  }



  /**
  * Método responsável por obter a quantdade de IC's do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeLicitacoes($where = null){
    return (new Database('licitacao','emerjco_licitacao'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
