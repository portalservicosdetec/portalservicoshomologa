<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Criticidade{

  /**
   * Identificador único da Criticidade
   * @var integer
   */
  public $criticidade_id;

  /**
   * Descrição da Criticidade (pode conter html)
   * @var string
   */
  public $criticidade_nm;

  /**
   * Data de publicação da Criticidade
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração da Criticidade
   * @var string
   */
  public $data_up;

    /**
   * Define se a Criticidade está ativa
   * @var string(s/n)
   */
  public $ativo_fl;

  /**
   * Método responsável por cadastrar um nova Criticidade no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');

    //INSERIR A Criticidade NO BANCO
    $obDatabase = new Database('tb_criticidade');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->criticidade_id = $obDatabase->insert([
                                      'criticidade_nm' => $this->criticidade_nm
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar a Criticidade no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_criticidade'))->update('criticidade_id = '.$this->criticidade_id,[
                                                        'criticidade_nm' => $this->criticidade_nm,
                                                        'ativo_fl'    => $this->ativo_fl,
                                                        'data_up'  => $this->data_up
                                                              ]);
  }

  /**
   * Método responsável por excluir uma Criticidade do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('tb_criticidade'))->delete('criticidade_id = '.$this->criticidade_id);
  }

  /**
   * Método responsável por obter as Criticidades do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getCriticidades($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_criticidade'))->select($where,$order,$limit,$fields);
  }

  /**
   * Método responsável por buscar uma Criticidade com base em seu ID
   * @param integer $id
   * @return Criticidade
   */
  public static function getCriticidadePorId($criticidade_id){
    return self::getCriticidades('criticidade_id = '.$criticidade_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de Criticidade com base em seu nome
   * @param string $nome
   * @return Criticidade
   */
  public static function getCriticidadePorNome($nome){
      return (new Database('tb_criticidade'))->select('criticidade_nm = "'.$nome.'"')->fetchObject(self::class);
  }


  /**
  * Método responsável por obter a quantdade de Criticidades do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeCriticidades($where = null){
    return (new Database('tb_criticidade'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
