<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Tipodeocorrencia{

  /**
   * Identificador único do Tipo de Ocorrência
   * @var integer
   */
  public $tipodeocorrencia_id;

  /**
   * Descrição do Tipo de Ocorrência
   * @var string
   */
  public $tipodeocorrencia_nm;

  /**
   * Descrição da vaga (pode conter html)
   * @var string
   */
  public $tipodeocorrencia_des;

  /**
   * Data de publicação do Tipo de Ocorrência
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração do Tipo de Ocorrência
   * @var string
   */
  public $data_up;

    /**
   * Define se o Tipo de Ocorrência ativo
   * @var string(s/n)
   */
  public $ativo_fl;

  /**
   * Método responsável por cadastrar um novo o Tipo de Ocorrência no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');

    //INSERIR O IC NO BANCO
    $obDatabase = new Database('tb_tipodeocorrencia');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->tipodeocorrencia_id = $obDatabase->insert([
                                      'tipodeocorrencia_nm' => $this->tipodeocorrencia_nm,
                                      'tipodeocorrencia_des' => $this->tipodeocorrencia_des
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar o Tipo de Ocorrência no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_tipodeocorrencia'))->update('tipodeocorrencia_id = '.$this->tipodeocorrencia_id,[
                                                                'tipodeocorrencia_nm'    => $this->tipodeocorrencia_nm,
                                                                'tipodeocorrencia_des' => $this->tipodeocorrencia_des,
                                                                'ativo_fl'    => $this->ativo_fl,
                                                                'data_up'  => $this->data_up
                                                              ]);
  }

  /**
   * Método responsável por excluir um Tipo de Ocorrência do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('tb_tipodeocorrencia'))->delete('tipodeocorrencia_id = '.$this->tipodeocorrencia_id);
  }

  /**
   * Método responsável por obter os Tipo de Ocorrências do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getTipodeocorrencias($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_tipodeocorrencia'))->select($where,$order,$limit,$fields);
  }


  /**
   * Método responsável por obter os Tipo de Ocorrências do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return Servico
   */
  public static function getTipodeServicoDeChamado2($where = null, $order = null, $from = null, $limit = null, $fields = '*'){
    return (new Database('tb_tipodeocorrencia'))->select2($where,$order,$from,$limit,$fields)->fetchObject(self::class);
  }


  /**
   * Método responsável por buscar um Tipo de Ocorrência com base em seu ID
   * @param integer $id
   * @return Tipodeocorrencia
   */
  public static function getTipodeocorrenciaPorId($tipodeocorrencia_id){
    return self::getTipodeocorrencias('tipodeocorrencia_id = '.$tipodeocorrencia_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de o Tipo de Ocorrência com base em seu nome
   * @param string $nome
   * @return Usuario
   */
  public static function getTipodeocorrenciaPorNome($nome){
      return (new Database('tb_tipodeocorrencia'))->select('tipodeocorrencia_nm = "'.$nome.'"')->fetchObject(self::class);
  }



  /**
  * Método responsável por obter a quantdade de o Tipo de Ocorrência do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeTipodeocorrencias($where = null){
    return (new Database('tb_tipodeocorrencia'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
