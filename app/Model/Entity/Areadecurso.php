<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Areadecurso{

  /**
   * Identificador único da vaga
   * @var integer
   */
  public $areadecurso_id;

  /**
   * Descrição da vaga (pode conter html)
   * @var string
   */
  public $areadecurso_nm;


  /**
   * Data de publicação da vaga
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração da vaga
   * @var string
   */
  public $data_up;

    /**
   * Define se o Tipode IC está ativa
   * @var string(s/n)
   */
  public $ativo_fl;


  /**
   * Método responsável por cadastrar um nova IC no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');

    //INSERIR O IC NO BANCO
    $obDatabase = new Database('tb_areadecurso');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->areadecurso_id = $obDatabase->insert([
                                      'areadecurso_nm' => $this->areadecurso_nm
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por atualizar o IC no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_areadecurso'))->update('areadecurso_id = '.$this->areadecurso_id,[
                                                                'areadecurso_nm'    => $this->areadecurso_nm,
                                                                'ativo_fl'    => $this->ativo_fl,
                                                                'data_up'  => $this->data_up
                                                              ]);
  }

  /**
   * Método responsável por excluir um IC do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('tb_areadecurso'))->delete('areadecurso_id = '.$this->areadecurso_id);
  }

  /**
   * Método responsável por obter os IC's do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getAreadecursos($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_areadecurso'))->select($where,$order,$limit,$fields);
  }

  /**
   * Método responsável por buscar um IC com base em seu ID
   * @param integer $id
   * @return Areadecurso
   */
  public static function getAreadecursoPorId($areadecurso_id){
    return self::getAreadecursos('areadecurso_id = '.$areadecurso_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de tipo de IC com base em seu nome
   * @param string $nome
   * @return Usuario
   */
  public static function getAreadecursoPorNome($nome){
      return (new Database('tb_areadecurso'))->select('areadecurso_nm = "'.$nome.'"')->fetchObject(self::class);
  }



  /**
  * Método responsável por obter a quantdade de IC's do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeAreadecursos($where = null){
    return (new Database('tb_areadecurso'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
