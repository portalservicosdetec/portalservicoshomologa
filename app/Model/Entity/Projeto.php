<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Projeto{

  /**
   * Identificador único do usuário
   * @var integer
   */
  public $projetos_id;

  /**
   * Nome do usuário
   * @var string
   */
  public $projetos_nm;

  /**
   * E-mail do usuário chave única
   * @var string
   */
  public $projetos_link;

  /**
   * E-mail do usuário chave única
   * @var string
   */
  public $projetos_rota;

  /**
   * Código do cargo da lotação do usuário (chave estrangeira da tabela lotação)
   * @var string
   */
  public $rota_login;

  /**
   * Data de cadastro do usuário
   * @var string
   */
  public $data_add;

    /**
   * Data da última alteração nos dados do usuário
   * @var string
   */
  public $data_up;

    /**
   * Define se o usuário está ativo
   * @var string(s/n)
   */
  public $ativo_fl;

  /**
   * Método responsável por cadastrar um novo usuário no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');
//    echo "<pre>"; print_r("NAME_ALUMNI1 = "); echo "</pre>";

  //  echo "<pre>"; print_r("NAME_ALUMNI2 = ".NAME_ALUMNI); echo "</pre>";

    //INSERE UM USUÁRIO NO BANCO
    $obDatabase = new Database('tb_projetos','emerjco_portalservicos');

  //  echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->projeto_id = $obDatabase->insert([
                                      'projetos_nm' => $this->projetos_nm,
                                      'projetos_rota' => $this->projetos_rota
                                    ]);

    //RETORNAR SUCESSO
    return true;
  }

  /**
   * Método responsável por retornar uma instancia de usuário com base em seu e-mail
   * @param string $email
   * @return Projeto
   */
  public static function getProjetoPorRotaLogin($rota){
      return (new Database('tb_projetos','emerjco_portalservicos'))->select('rota_login = "'.$rota.'"')->fetchObject(self::class);
  }

  /**
   * Método responsável por retornar uma instancia de usuário com base em seu id
   * @param integer $id
   * @return Projeto
   */
  public static function getProjetoPorId($id){
      return self::getProjetos('projetos_id = '.$id)->fetchObject(self::class);
  }

  /**
   * Método responsável por retornar uma instancia de usuário com base em seu id
   * @param string $cpf
   * @return Projeto
   */
  public static function getProjetoPorCPF($cpf){
      return (new Database('tb_projetos','emerjco_portalservicos'))->select('projeto_cpf = "'.$cpf.'"')->fetchObject(self::class);
  }

  /**
   * Método responsável por atualizar os dados do usuário no banco
   * @return boolean
   */
  public function atualizar(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_projetos','emerjco_portalservicos'))->update('projeto_id = '.$this->projeto_id,[
                                                                'projeto_nome' => $this->projeto_nome,
                                                                'projeto_cpf' => $this->projeto_cpf,
                                                                'projeto_matricula' => $this->projeto_matricula,
                                                                'projeto_genero' => $this->projeto_genero,
                                                                'projeto_nascimento' => $this->projeto_nascimento,
                                                                'projeto_email' => $this->projeto_email,
                                                                'projeto_senha' => $this->projeto_senha,
                                                                'projeto_telefone' => $this->projeto_telefone,
                                                                'projeto_celular' => $this->projeto_celular,
                                                                'projeto_categoria' => $this->projeto_categoria,
                                                                'projeto_anoConclusao' => $this->projeto_anoConclusao,
                                                                'projeto_profissao' => $this->projeto_profissao,
                                                                'projeto_linkedin' => $this->projeto_linkedin,
                                                                'projeto_autorizacao' => $this->projeto_autorizacao,
                                                                'projeto_aceito' => $this->projeto_aceito,
                                                                'data_up'  => $this->data_up,
                                                                'validado_fl' => $this->ativo_fl,
                                                                'ativo_fl' => $this->ativo_fl
                                                              ]);
  }

  /**
   * Método responsável por atualizar os dados do usuário no banco
   * @return boolean
   */
  public function getAtivaCadastroProjeto($id){
    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->data_up = date('Y-m-d H:i:s');

    return (new Database('tb_projeto','emerjco_portalservicos'))->update('usuario_id = '.$id,[
                                                                'data_up'  => $this->data_up,
                                                                'validado_fl' => 's'
                                                              ]);

  }

  /**
   * Método responsável por atualizar os dados do usuário no banco
   * @return boolean
   */
  public function atualizadataultimoacesso(){

    //DEFINIR A DATA DE ATUALIZACAO date('Y-m-d H:i:s')
    date_default_timezone_set('America/Sao_Paulo');
    $this->dt_ulti_acesso = date('Y-m-d H:i:s');

    return (new Database('tb_projeto','emerjco_portalservicos'))->update('projeto_id = '.$this->projeto_id,[
                                                                'dt_ulti_acesso'  => $this->dt_ulti_acesso,
                                                              ]);
  }

  /**
   * Método responsável por excluir um usuário do banco
   * @return boolean
   */
  public function excluir(){
    //EXCLUI O USUÁRIO DO BANCO DE DADOS
    return (new Database('tb_projeto','emerjco_portalservicos'))->delete('projeto_id = '.$this->projeto_id);
  }



  /**
   * Método responsável por obter os usuário do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $limit
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getProjetos($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_projeto','emerjco_portalservicos'))->select($where,$order,$limit,$fields);
  }
     /**
   * Método responsável por obter a quantdade de Projetos do banco de dados
   * @param  string $where
   * @return integer
   */
  public static function getQuantidadeProjetos($where = null){
    return (new Database('tb_projeto','emerjco_portalservicos'))->select($where,null,null,'COUNT(*) as qtd')
                                  ->fetchObject()
                                  ->qtd;

  }


}
