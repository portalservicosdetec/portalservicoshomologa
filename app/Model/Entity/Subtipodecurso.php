<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Subtipodecurso{

  /**
   * Identificador único do subtipo de curso
   * @var integer
   */
  public $subtipodecurso_id;

  /**
   * Nome do subtipo de curso (pode conter html)
   * @var string
   */
  public $subtipodecurso_nm;

  /**
   * Descrição do Subtipo de Curso (pode conter html)
   * @var string
   */
  public $subtipodecurso_conteudo;

  /**
   * Tipo de curso (pode conter html)
   * @var string
   */
  public $tipo_curso;

  /**
   * Data de publicação do subtipo de curso
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração do subtipo de curso
   * @var string
   */
  public $data_up;

    /**
   * Define se o do subtipo de curso está ativa
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
    $obDatabase = new Database('tb_subtipodecurso');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->subtipodecurso_id = $obDatabase->insert([
                                      'subtipodecurso_nm' => $this->subtipodecurso_nm,
                                      'subtipodecurso_conteudo' => $this->subtipodecurso_conteudo,
                                      'tipo_curso' => $this->tipo_curso
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

    return (new Database('tb_subtipodecurso'))->update('subtipodecurso_id = '.$this->subtipodecurso_id,[
                                                                'subtipodecurso_nm'    => $this->subtipodecurso_nm,
                                                                'subtipodecurso_conteudo' => $this->subtipodecurso_conteudo,
                                                                'tipo_curso' => $this->tipo_curso,
                                                                'ativo_fl'    => $this->ativo_fl,
                                                                'data_up'  => $this->data_up
                                                              ]);
  }

  /**
   * Método responsável por excluir um IC do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('tb_subtipodecurso'))->delete('subtipodecurso_id = '.$this->subtipodecurso_id);
  }

  /**
   * Método responsável por obter os IC's do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getSubtipodecursos($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_subtipodecurso'))->select($where,$order,$limit,$fields);
  }

  /**
   * Método responsável por buscar um IC com base em seu ID
   * @param integer $id
   * @return Subtipodecurso
   */
  public static function getSubtipodecursoPorId($subtipodecurso_id){
    return self::getSubtipodecursos('subtipodecurso_id = '.$subtipodecurso_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de tipo de IC com base em seu nome
   * @param string $nome
   * @return Usuario
   */
  public static function getSubtipodecursoPorNome($nome){
      return (new Database('tb_subtipodecurso'))->select('subtipodecurso_nm = "'.$nome.'"')->fetchObject(self::class);
  }



  /**
  * Método responsável por obter a quantdade de IC's do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeSubtipodecursos($where = null){
    return (new Database('tb_subtipodecurso'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
