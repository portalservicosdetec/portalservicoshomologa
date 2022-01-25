<?php

namespace App\Model\Entity;

use \App\Db\Database;
use \PDO;

class Curso{

  /**
   * Identificador único do CURSO
   * @var integer
   */
  public $curso_id;

  /**
   * Nome do CURSO
   * @var string
   */
  public $curso_nm;

  /**
   * Informações do CURSO (pode conter html)
   * @var string
   */
  public $curso_informacoes;

  /**
   * Público Alvo do CURSO
   * @var string
   */
  public $curso_publico_alvo;

  /**
   * Área do CURSO
   * @var string
   */
  public $curso_area;

  /**
   * Tipo do CURSO
   * @var string
   */
  public $curso_tipo;

 /**
  * Título do CURSO
  * @var string
  */
 public $curso_titulo;

 /**
  * Imagem frontal do cartaz do CURSO
  * @var string
  */
 public $curso_img_frente;


 /**
  * Alt da imagem frontal do cartaz do CURSO
  * @var string
  */
 public $curso_imgalt_frente ;

  /**
   * Title da imagem frontal do cartaz CURSO
   * @var string
   */
  public $curso_imgtittle_frente;

  /**
   * Imagem traseira do cartaz do CURSO
   * @var string
   */
  public $curso_img_tras;


   /**
    * Alt da imagem traseira do cartaz do CURSO
    * @var string
    */
   public $curso_imgalt_tras;

    /**
     * Title da imagem traseira do cartaz CURSO
     * @var string
     */
    public $curso_imgtittle_tras;

  /**
   * Descrição do CURSO (pode conter html)
   * @var string
   */
  public $curso_descricao;

  /**
   * Observação do CURSO (pode conter html)
   * @var string
   */
  public $curso_obs;

  /**
   * Arquivo PDF do Edital do CURSO
   * @var string
   */
  public $pdf_edital;

  /**
   * Data início do CURSO
   * @var string
   */
  public $data_inicio;

  /**
   * data fim do CURSO
   * @var string
   */
  public $data_fim;

  /**
   * Data início das inscrições para o CURSO
   * @var string
   */
  public $data_inicio_inscricao;

  /**
   * Data fim das inscrições para o CURSO
   * @var string
   */
  public $data_fim_inscricao;

  /**
   * Data de publicação do CURSO
   * @var string
   */
  public $data_add;

    /**
   * Data de alteração do CURSO
   * @var string
   */
  public $data_up;

    /**
   * Define se o curso está ativo
   * @var string(s/n)
   */
  public $ativo_fl;

  /**
 * Id do Usuário responsável pelo cadastro
 * @var integer
 */
 public $id_usuario;


  /**
   * Método responsável por cadastrar um nova IC no banco
   * @return boolean
   */
  public function cadastrar(){
    //DEFINIR A DATA
    //$this->data_add = date('Y-m-d H:i:s');

    //INSERIR O IC NO BANCO
    $obDatabase = new Database('tb_curso');
     // echo "<pre>"; print_r($obDatabase); echo "</pre>"; exit;

    $this->curso_id = $obDatabase->insert([
                                      'curso_nm' => $this->curso_nm,
                                      'curso_informacoes'  => $this->curso_informacoes,
                                      'curso_publico_alvo'  => $this->curso_publico_alvo,
                                      'curso_area'  => $this->curso_area,
                                      'curso_tipo'  => $this->curso_tipo,
                                      'curso_titulo'  => $this->curso_titulo,
                                      'curso_img_frente'  => $this->curso_img_frente,
                                      'curso_imgalt_frente'  => $this->curso_imgalt_frente,
                                      'curso_imgtittle_frente'  => $this->curso_imgtittle_frente,
                                      'curso_img_tras'  => $this->curso_img_tras,
                                      'curso_imgalt_tras'  => $this->curso_imgalt_tras,
                                      'curso_imgtittle_tras'  => $this->curso_imgtittle_tras,
                                      'curso_descricao'  => $this->curso_descricao,
                                      'curso_obs'  => $this->curso_obs,
                                      'pdf_edital'  => $this->pdf_edital,
                                      'data_inicio' => $this->data_inicio,
                                      'data_fim' => $this->data_fim,
                                      'data_inicio_inscricao' => $this->data_inicio_inscricao,
                                      'data_fim_inscricao' => $this->data_fim_inscricao,
                                      'id_usuario' => $this->id_usuario
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

    return (new Database('tb_curso'))->update('curso_id = '.$this->curso_id,[
                                                        'curso_nm' => $this->curso_nm,
                                                        'curso_informacoes'  => $this->curso_informacoes,
                                                        'curso_publico_alvo'  => $this->curso_publico_alvo,
                                                        'curso_area'  => $this->curso_area,
                                                        'curso_tipo'  => $this->curso_tipo,
                                                        'curso_titulo'  => $this->curso_titulo,
                                                        'curso_img_frente'  => $this->curso_img_frente,
                                                        'curso_img_tras'  => $this->curso_img_tras,
                                                        'curso_descricao'  => $this->curso_descricao,
                                                        'curso_obs'  => $this->curso_obs,
                                                        'pdf_edital'  => $this->pdf_edital,
                                                        'data_inicio' => $this->data_inicio,
                                                        'data_fim' => $this->data_fim,
                                                        'data_inicio_inscricao' => $this->data_inicio_inscricao,
                                                        'data_fim_inscricao' => $this->data_fim_inscricao,
                                                        'ativo_fl'    => $this->ativo_fl,
                                                        'data_up'  => $this->data_up,
                                                        'id_usuario' => $this->id_usuario
                                                              ]);
  }

  /**
   * Método responsável por excluir um IC do banco
   * @return boolean
   */
  public function excluir(){
    return (new Database('tb_curso'))->delete('curso_id = '.$this->curso_id);
  }

  /**
   * Método responsável por obter os IC's do banco de dados
   * @param  string $where
   * @param  string $order
   * @param  string $fields
   * @return PDOStatement
   */
  public static function getCursos($where = null, $order = null, $limit = null, $fields = '*'){
    return (new Database('tb_curso'))->select($where,$order,$limit,$fields);
  }

  /**
   * Método responsável por buscar um IC com base em seu ID
   * @param integer $id
   * @return Curso
   */
  public static function getCursoPorId($curso_id){
    return self::getCursos('curso_id = '.$curso_id)->fetchObject(self::class);
  }


  /**
   * Método responsável por retornar uma instancia de tipo de IC com base em seu nome
   * @param string $nome
   * @return Usuario
   */
  public static function getCursoPorNome($nome){
      return (new Database('tb_curso'))->select('curso_nm = "'.$nome.'"')->fetchObject(self::class);
  }



  /**
  * Método responsável por obter a quantdade de IC's do banco de dados
  * @param  string $where
  * @return integer
  */
  public static function getQuantidadeCursos($where = null){
    return (new Database('tb_curso'))->select($where,null,null,'COUNT(*) as qtd')
                                 ->fetchObject()
                                 ->qtd;
  }
}
