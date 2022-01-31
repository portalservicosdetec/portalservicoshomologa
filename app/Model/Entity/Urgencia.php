<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Urgencia{

  /**
   * Identificador único da Urgência
   * @var integer
   */
  public $urgencia_id;

  /**
   * Nome da Urgência
   * @var string
   */
  public $urgencia_nm;

  /**
   * Data de publicação da Urgência
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração da Urgência
   * @var string
   */
  public $data_up;

    /**
   * Define se a Urgência está ativa
   * @var string(s/n)
   */
  public $ativo_fl;

  /**
   * Método responsável por cadastrar um nova Urgência no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');

    //INSERIR O IC NO BANCO
    $obDatabase = new Database('tb_urgencia');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->urgencia_id = $obDatabase->insert([
                                      'urgencia_nm' => $this->urgencia_nm
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar a Urgência no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_urgencia'))->update('urgencia_id = '.$this->urgencia_id,[
                                                        'urgencia_nm' => $this->urgencia_nm,
                                                        'ativo_fl'    => $this->ativo_fl,
                                                        'data_up'  => $this->data_up
                                                              ]);
  }

  /**
   * Método responsável por excluir uma Urgência do banco
   * @return boolean/
   */
  public function excluir(){
    return (new Database('tb_urgencia'))->delete('urgencia_id = '.$this->urgencia_id);
  }

  /**
   * Método responsável por obter as Urgências do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getUrgencias($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_urgencia'))->select($where,$order,$limit,$fields);
  }

  /**
   * Método responsável por buscar uma Urgência com base em seu ID
   * @param integer $id
   * @return Urgencia
   */
  public static function getUrgenciaPorId($urgencia_id){
    return self::getUrgencias('urgencia_id = '.$urgencia_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de Urgência com base em seu nome
   * @param string $nome
   * @return Urgencia
   */
  public static function getUrgenciaPorNome($nome){
      return (new Database('tb_urgencia'))->select('urgencia_nm = "'.$nome.'"')->fetchObject(self::class);
  }


  /**
  * Método responsável por obter a quantdade de Urgências do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeUrgencias($where = null){
    return (new Database('tb_urgencia'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
