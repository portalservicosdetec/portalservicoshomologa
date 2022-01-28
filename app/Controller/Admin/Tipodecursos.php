<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Tipodecurso as EntityTipodecurso;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Db\Pagination;
//use \App\Http\Request;

const DIR_TIPODECURSO = 'tipodecurso';
const ROTA_TIPODECURSO = 'tipodecursos';
const ICON_TIPODECURSO = 'bag-check';
const TITLE_TIPODECURSO = 'Tipo de Cursos';
const TITLELOW_TIPODECURSO = 'o tipo de curso';

class Tipodecursos extends Page{

  /**
   * Método responsável pela renderização da view de listagem de Tipos de Serviço
   * @param Request $request
   * @return string
   */
  public static function getListTipodecursos($request,$errorMessage = null){

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
    $content = View::render('admin/modules/'.DIR_TIPODECURSO.'/index',[
      'icon' => ICON_TIPODECURSO,
      'title' =>TITLE_TIPODECURSO,
      'titlelow' => TITLELOW_TIPODECURSO,
      'direntity' => ROTA_TIPODECURSO,
      'itens' => self::getTipodecursoItens($request,$obPagination),
      'status' => $status
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel(TITLE_TIPODECURSO.' - EMERJ',$content,ROTA_TIPODECURSO,$currentDepartamento,$currentPerfil);

  }

  /**
   * Método responsável por obter a renderização dos itens de Tipos de Serviço para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getTipodecursoItens($request,&$obPagination){

    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/'.DIR_TIPODECURSO.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_TIPODECURSO.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_TIPODECURSO.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_TIPODECURSO.'/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityTipodecurso::getTipodecursos();

    //MONTA E RENDERIZA OS ITENS DE Tipodecurso
    while($obTipodecurso = $results->fetchObject(EntityTipodecurso::class)){
      $itens .= View::render('admin/modules/'.DIR_TIPODECURSO.'/item',[
        'id' => $obTipodecurso->tipodecurso_id,
        'nome' => $obTipodecurso->tipodecurso_nm,
        'conteudo' => $obTipodecurso->tipodecurso_conteudo,
        'texto_ativo' => ('s' == $obTipodecurso->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obTipodecurso->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obTipodecurso->ativo_fl) ? 'table-active' : 'table-danger',
        'icon' => ICON_TIPODECURSO,
        'title' =>TITLE_TIPODECURSO,
        'titlelow' => TITLELOW_TIPODECURSO,
        'direntity' => ROTA_TIPODECURSO
      ]);
    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    return $itens;
  }

  /**
   * Método responsável por montar a renderização do select de Tipo de Serviço para os formulários
   * @param Request $request
   * @param integer $id
   * @return string
   */
  public static function getTipodecursoItensSelect($request,$id){
    $itensSelect = '';
    $resultsSelect = EntityTipodecurso::getTipodecursos(null,'tipodecurso_nm ');
    //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

    while($obTipodecurso = $resultsSelect->fetchObject(EntityTipodecurso::class)){
      $itensSelect .= View::render('admin/modules/tipodecurso/itemselect',[
        'idSelect' => $obTipodecurso->tipodecurso_id,
        'selecionado' => ($id == $obTipodecurso->tipodecurso_id) ? 'selected' : '',
        'nomeSelect' => $obTipodecurso->tipodecurso_nm
      ]);
    }
    return $itensSelect;
  }


  /**
   * Método responsável por retornar o formulário de cadastro de um novo Chamado
   * @param Request $request
   * @return string
   */
   public static function getNovoTipodecurso($request){

     $tipodecurso_id = '';
     $status = '';
     $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
     $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

     //CONTEÚDO DA NOTÍCIA
     $content = View::render('admin/modules/'.DIR_TIPODECURSO.'/form',[
       'icon' => ICON_TIPODECURSO,
       'title' =>TITLE_TIPODECURSO,
       'titlelow' => TITLELOW_TIPODECURSO,
       'direntity' => ROTA_TIPODECURSO,
       'itens' => self::getTipodecursoItens($request,$obPagination),
       'nome' => '',
       'tipocurso_conteudo' => '',
       'status' => $status
     ]);

     //RETORNA A PÁGINA COMPLETA
     return parent::getPanel('Cadastrar Tipo de Cursos - EMERJ',$content,'tipodecursos',$currentDepartamento,$currentPerfil);
   }

  //*** Método responsável por retornar o formulário de cadastro de um novo Tipo de Serviço *** EXCLUIDO

   /**
    * Método responsável por cadastro de um novo Tipo de Serviço no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoTipodecurso($request){

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $conteudo = $posVars['conteudo'];

      //VERIFICA SE JÁ EXISTE O TIPO de Serviço com mesmo nome CADASTRADO NO BANCO
      $obTipodecurso = EntityTipodecurso::getTipodecursoPorNome($nome);

      if($obTipodecurso instanceof EntityTipodecurso){

        //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
        $request->getRouter()->redirect('/admin/tipodecursos?status=duplicado');
      }

      //NOVA ISNTANCIA DE Serviço
      $obTipodecurso = new EntityTipodecurso;

      ////$obTipodecurso::getTipodecursoPorEmail($posVars['email']);
      $obTipodecurso->tipodecurso_nm = $nome;
      $obTipodecurso->tipodecurso_conteudo = $conteudo;
      $obTipodecurso->ativo_fl = 's';
      $obTipodecurso->cadastrar();

      //REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/tipodecursos?status=gravado&nm='.$nome.'&acao=grava');

    }


    /**
     * Método responsável por gravar a edição de uma Localização
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditTipodecurso($request,$id){

       $status = '';
       $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
       $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

       $obTipodecurso = EntityTipodecurso::getTipodecursoPorId($id);

       //CONTEÚDO DA NOTÍCIA
       $content = View::render('admin/modules/'.DIR_TIPODECURSO.'/form',[
         'icon' => ICON_TIPODECURSO,
         'title' => 'Alterar '.TITLE_TIPODECURSO,
         'titlelow' => TITLELOW_TIPODECURSO,
         'direntity' => ROTA_TIPODECURSO,
         'itens' => self::getTipodecursoItens($request,$obPagination),
         'id' => $obTipodecurso->tipodecurso_id ?? '',
         'nome' => $obTipodecurso->tipodecurso_nm ?? '',
         'tipocurso_conteudo'  => $obTipodecurso->tipodecurso_conteudo ?? '',
         'status' => $status
       ]);

       return parent::getPanel('Alterar Tipo de Curso - EMERJ',$content,'tipodecursos',$currentDepartamento,$currentPerfil);


     }


     /**
      * Método responsável por gravar a edição de um Tipo de Serviço
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditTipodecurso($request,$id){

      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);;
      $tipodecurso_nm = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $tipodecurso_conteudo = $posVars['conteudo'];

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obTipodecurso = EntityTipodecurso::getTipodecursoPorId($id);

        if(!$obTipodecurso instanceof EntityTipodecurso){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/tipodecursos?status=updatefail');
        }

        //echo "<pre>"; print_r($id_tipodecurso); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obTipodecurso->tipodecurso_nm = $tipodecurso_nm ?? $obTipodecurso->tipodecurso_nm;
        $obTipodecurso->tipodecurso_conteudo = $tipodecurso_conteudo  ?? $obTipodecurso->tipodecurso_conteudo;
        $obTipodecurso->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/tipodecursos?status=alterado&nm='.$tipodecurso_nm.'&acao=alter');
      }


      /**
       * Método responsável por retornar o formulário de exclusão de um Tipo de Serviço atraves de um Modal
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteTipodecurso($request,$id){

         $obTipodecurso = EntityTipodecurso::getTipodecursoPorId($id);
         $strNome = $obTipodecurso->tipodecurso_nm;

         if(!$obTipodecurso instanceof EntityTipodecurso){
           $request->getRouter()->redirect('/admin/tipodecursos?status=updatefail');
         }

        //EXCLUI O USUÁRIO
         $obTipodecurso->excluir();
         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/tipodecursos?status=deletado&nm='.$strNome.$strMsn.'&acao=excluir');
       }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function setAltStatusTipodecurso($request,$id){

         //OBTÉM A CATEGORIA DO IC PELO SEU ID DO BANCO DE DADOS
         $obTipodecurso = EntityTipodecurso::getTipodecursoPorId($id);
         $strNome = $obTipodecurso->tipodecurso_nm;



         if(!$obTipodecurso instanceof EntityTipodecurso){
           $request->getRouter()->redirect('/admin/tipodecursos?status=updatefail');
         }

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obTipodecurso->ativo_fl == 's'){
           $altStatus = 'n';
           $strMsn = ' DESATIVADO ';
         } elseif (($obTipodecurso->ativo_fl == 'n') || ($obTipodecurso->ativo_fl == '')) {
           $strMsn = ' ATIVADO ';
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obTipodecurso->ativo_fl = $altStatus;
         $obTipodecurso->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/tipodecursos?status=statusupdate&nm='.$strNome.$strMsn);

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
       return Alert::getSuccess('Dados d'.TITLE_TIPODECURSO .' <strong>'.$nm.'</strong> cadastrados com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados d'.TITLELOW_TIPODECURSO .' <strong>'.$nm.'</strong> alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_TIPODECURSO .'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe '.TITLELOW_TIPODECURSO .' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_TIPODECURSO.'!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status d'.TITLELOW_TIPODECURSO .' <strong>'.$nm.'</strong> com sucesso!');
       // code...
       break;
   }
  }
}
