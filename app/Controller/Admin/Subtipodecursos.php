<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Subtipodecurso as EntitySubtipodecurso;
use \App\Model\Entity\Tipodecurso as EntityTipodecurso;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Controller\Admin\Tipodecursos as AdminTipodecursos;
use \App\Db\Pagination;
//use \App\Http\Request;

const DIR_SUBTIPODECURSO = 'subtipodecurso';
const ROTA_SUBTIPODECURSO = 'subtipodecursos';
const ICON_SUBTIPODECURSO = 'bag-check';
const TITLE_SUBTIPODECURSO = 'SubTipo de Cursos';
const TITLELOW_SUBTIPODECURSO = 'o tipo de curso';

class Subtipodecursos extends Page{

  /**
   * Método responsável pela renderização da view de listagem de SubTipos de Serviço
   * @param Request $request
   * @return string
   */
  public static function getListSubtipodecursos($request,$errorMessage = null){

    $status = self::getStatus($request);
    $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
    $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

    //STATUS
    if(!isset($currentDepartamento)) return $permissao = false;

   //MENSAGENS DE STATUS
   switch ($currentDepartamento) {
     case 'EMERJ':
       $permissao = true;
       break;
     case 'DETEC':
       $permissao = true;
       break;
   }
   //STATUS
   if(!isset($currentPerfil)) return $permissao = false;

  //MENSAGENS DE STATUS
  switch ($currentPerfil) {
    case 1:
      $permissao = true;
      break;
    case 2:
      $permissao = true;
      break;
  }

    if (!$permissao) {
      $request->getRouter()->redirect('/?status=sempermissao');
    }

    //CONTEÚDO DA HOME
    $content = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/index',[
      'icon' => ICON_SUBTIPODECURSO,
      'title' =>TITLE_SUBTIPODECURSO,
      'titlelow' => TITLELOW_SUBTIPODECURSO,
      'direntity' => ROTA_SUBTIPODECURSO,
      'itens' => self::getSubtipodecursoItens($request,$obPagination),
      'status' => $status
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel(TITLE_SUBTIPODECURSO.' - EMERJ',$content,ROTA_SUBTIPODECURSO,$currentDepartamento,$currentPerfil);

  }

  /**
   * Método responsável por obter a renderização dos itens de SubTipos de Serviço para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getSubtipodecursoItens($request,&$obPagination){

    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntitySubtipodecurso::getSubtipodecursos();

    //MONTA E RENDERIZA OS ITENS DE Subtipodecurso
    while($obSubtipodecurso = $results->fetchObject(EntitySubtipodecurso::class)){
      $itens .= View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/item',[
        'id' => $obSubtipodecurso->subtipodecurso_id,
        'nome' => $obSubtipodecurso->subtipodecurso_nm,
        'conteudo' => $obSubtipodecurso->subtipodecurso_conteudo,
        'tipo_curso' => EntityTipodecurso::getTipodecursoPorId($obSubtipodecurso->tipo_curso)->tipodecurso_nm,
        'texto_ativo' => ('s' == $obSubtipodecurso->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obSubtipodecurso->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obSubtipodecurso->ativo_fl) ? 'table-active' : 'table-danger',
        'icon' => ICON_SUBTIPODECURSO,
        'title' =>TITLE_SUBTIPODECURSO,
        'titlelow' => TITLELOW_SUBTIPODECURSO,
        'direntity' => ROTA_SUBTIPODECURSO
      ]);
    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    return $itens;
  }

  /**
   * Método responsável por montar a renderização do select de SubTipo de Serviço para os formulários
   * @param Request $request
   * @param integer $id
   * @return string
   */
  public static function getSubtipodecursoItensSelect($request,$id){
    $itensSelect = '';
    $resultsSelect = EntitySubtipodecurso::getSubtipodecursos(null,'subtipodecurso_nm ');
    //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

    while($obSubtipodecurso = $resultsSelect->fetchObject(EntitySubtipodecurso::class)){
      $itensSelect .= View::render('admin/modules/subtipodecurso/itemselect',[
        'idSelect' => $obSubtipodecurso->subtipodecurso_id,
        'selecionado' => ($id == $obSubtipodecurso->subtipodecurso_id) ? 'selected' : '',
        'nomeSelect' => $obSubtipodecurso->subtipodecurso_nm
      ]);
    }
    return $itensSelect;
  }


  /**
   * Método responsável por retornar o formulário de cadastro de um novo Chamado
   * @param Request $request
   * @return string
   */
   public static function getNovoSubtipodecurso($request){

     $subtipodecurso_id = '';
     $status = '';
     $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
     $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

     $optionsTipodecurso = AdminTipodecursos::getTipodecursoItensSelect($request,$subtipodecurso_id);

     //CONTEÚDO DA NOTÍCIA
     $content = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/form',[
       'icon' => ICON_SUBTIPODECURSO,
       'title' =>TITLE_SUBTIPODECURSO,
       'titlelow' => TITLELOW_SUBTIPODECURSO,
       'direntity' => ROTA_SUBTIPODECURSO,
       'itens' => self::getSubtipodecursoItens($request,$obPagination),
       'nome' => '',
       'subtipocurso_conteudo' => '',
       'curso_tipo' => $optionsTipodecurso,
       'status' => $status
     ]);

     //RETORNA A PÁGINA COMPLETA
     return parent::getPanel('Cadastrar SubTipo de Cursos - EMERJ',$content,'subtipodecursos',$currentDepartamento,$currentPerfil);
   }

  //*** Método responsável por retornar o formulário de cadastro de um novo SubTipo de Serviço *** EXCLUIDO

   /**
    * Método responsável por cadastro de um novo SubTipo de Serviço no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoSubtipodecurso($request){

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $conteudo = $posVars['conteudo'];
      $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_NUMBER_INT);

      //VERIFICA SE JÁ EXISTE O TIPO de Serviço com mesmo nome CADASTRADO NO BANCO
      $obSubtipodecurso = EntitySubtipodecurso::getSubtipodecursoPorNome($nome);

      if($obSubtipodecurso instanceof EntitySubtipodecurso){

        //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
        $request->getRouter()->redirect('/admin/subtipodecursos?status=duplicado');
      }

      //NOVA ISNTANCIA DE Serviço
      $obSubtipodecurso = new EntitySubtipodecurso;

      ////$obSubtipodecurso::getSubtipodecursoPorEmail($posVars['email']);

      $obSubtipodecurso->subtipodecurso_nm = $nome;
      $obSubtipodecurso->subtipodecurso_conteudo = $conteudo;
      $obSubtipodecurso->tipo_curso = $tipo;
      $obSubtipodecurso->ativo_fl = 's';
      $obSubtipodecurso->cadastrar();

      //REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/subtipodecursos?status=gravado&nm='.$nome.'&acao=grava');

    }


    /**
     * Método responsável por gravar a edição de uma Localização
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditSubtipodecurso($request,$id){

       $status = '';
       $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
       $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

       $obSubtipodecurso = EntitySubtipodecurso::getSubtipodecursoPorId($id);

       //CONTEÚDO DA NOTÍCIA
       $content = View::render('admin/modules/'.DIR_SUBTIPODECURSO.'/form',[
         'icon' => ICON_SUBTIPODECURSO,
         'title' => 'Alterar '.TITLE_SUBTIPODECURSO,
         'titlelow' => TITLELOW_SUBTIPODECURSO,
         'direntity' => ROTA_SUBTIPODECURSO,
         'itens' => self::getSubtipodecursoItens($request,$obPagination),
         'id' => $obSubtipodecurso->subtipodecurso_id ?? '',
         'nome' => $obSubtipodecurso->subtipodecurso_nm ?? '',
         'subtipocurso_conteudo'  => $obSubtipodecurso->subtipodecurso_conteudo ?? '',
         'curso_tipo' => $optionsTipodecurso,
         'status' => $status
       ]);

       return parent::getPanel('Alterar SubTipo de Curso - EMERJ',$content,'subtipodecursos',$currentDepartamento,$currentPerfil);


     }


     /**
      * Método responsável por gravar a edição de um SubTipo de Serviço
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditSubtipodecurso($request,$id){

      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);;
      $subtipodecurso_nm = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $subtipodecurso_conteudo = $posVars['conteudo'];
      $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_NUMBER_INT);

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obSubtipodecurso = EntitySubtipodecurso::getSubtipodecursoPorId($id);

        if(!$obSubtipodecurso instanceof EntitySubtipodecurso){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/subtipodecursos?status=updatefail');
        }

        //echo "<pre>"; print_r($id_subtipodecurso); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obSubtipodecurso->subtipodecurso_nm = $subtipodecurso_nm ?? $obSubtipodecurso->subtipodecurso_nm;
        $obSubtipodecurso->subtipodecurso_conteudo = $subtipodecurso_conteudo  ?? $obSubtipodecurso->subtipodecurso_conteudo;
        $obSubtipodecurso->tipo_curso = $tipo ?? $obSubtipodecurso->tipo_curso;
        $obSubtipodecurso->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/subtipodecursos?status=alterado&nm='.$subtipodecurso_nm.'&acao=alter');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function setAltStatusSubtipodecurso($request,$id){

         //OBTÉM A CATEGORIA DO IC PELO SEU ID DO BANCO DE DADOS
         $obSubtipodecurso = EntitySubtipodecurso::getSubtipodecursoPorId($id);
         $strNome = $obSubtipodecurso->subtipodecurso_nm;



         if(!$obSubtipodecurso instanceof EntitySubtipodecurso){
           $request->getRouter()->redirect('/admin/subtipodecursos?status=updatefail');
         }

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obSubtipodecurso->ativo_fl == 's'){
           $altStatus = 'n';
           $strMsn = ' DESATIVADO ';
         } elseif (($obSubtipodecurso->ativo_fl == 'n') || ($obSubtipodecurso->ativo_fl == '')) {
           $strMsn = ' ATIVADO ';
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obSubtipodecurso->ativo_fl = $altStatus;
         $obSubtipodecurso->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/subtipodecursos?status=statusupdate&nm='.$strNome.$strMsn);

       }


  /**
   * Método responsável por retornar a mensagem de status
   * @param Request $request
   * @return string
   */
  private static function getStatus($request){
    //QUERY PARAMS
    $queryParams = $request->getQueryParams();
    $nm = filter_input(INPUT_GET, 'nm', FILTER_SANITIZE_STRING) ?? '';
    $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

    //STATUS
    if(!isset($queryParams['status'])) return '';

   //MENSAGENS DE STATUS
   switch ($queryParams['status']) {
     case 'gravado':
       return Alert::getSuccess('Dados d'.TITLE_SUBTIPODECURSO .' <strong>'.$nm.'</strong> cadastrados com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados d'.TITLELOW_SUBTIPODECURSO .' <strong>'.$nm.'</strong> alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_SUBTIPODECURSO .'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe '.TITLELOW_SUBTIPODECURSO .' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_SUBTIPODECURSO.'!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status d'.TITLELOW_SUBTIPODECURSO .' <strong>'.$nm.'</strong> com sucesso!');
       // code...
       break;
   }
  }
}
