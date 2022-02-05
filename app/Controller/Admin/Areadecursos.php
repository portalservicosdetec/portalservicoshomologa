<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Areadecurso as EntityAreadecurso;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Db\Pagination;
//use \App\Http\Request;

const DIR_AREADECURSO = 'areadecurso';
const ROTA_AREADECURSO = 'areadecursos';
const ICON_AREADECURSO = 'bag-check';
const TITLE_AREADECURSO = 'Área de Cursos';
const TITLELOW_AREADECURSO = 'a área de curso';

class Areadecursos extends Page{

  /**
   * Método responsável pela renderização da view de listagem de Tipos de Serviço
   * @param Request $request
   * @return string
   */
  public static function getListAreadecursos($request,$errorMessage = null){

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
    $content = View::render('admin/modules/'.DIR_AREADECURSO.'/index',[
      'icon' => ICON_AREADECURSO,
      'title' =>TITLE_AREADECURSO,
      'titlelow' => TITLELOW_AREADECURSO,
      'direntity' => ROTA_AREADECURSO,
      'itens' => self::getAreadecursoItens($request,$obPagination),
      'status' => $status
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel(TITLE_AREADECURSO.' - EMERJ',$content,ROTA_AREADECURSO,$currentDepartamento,$currentPerfil);

  }

  /**
   * Método responsável por obter a renderização dos itens de Tipos de Serviço para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getAreadecursoItens($request,&$obPagination){

    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/'.DIR_AREADECURSO.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_AREADECURSO.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_AREADECURSO.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_AREADECURSO.'/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityAreadecurso::getAreadecursos();

    //MONTA E RENDERIZA OS ITENS DE Areadecurso
    while($obAreadecurso = $results->fetchObject(EntityAreadecurso::class)){
      $itens .= View::render('admin/modules/'.DIR_AREADECURSO.'/item',[
        'id' => $obAreadecurso->areadecurso_id,
        'nome' => $obAreadecurso->areadecurso_nm,
        'descricao' => $obAreadecurso->areadecurso_des,
        'texto_ativo' => ('s' == $obAreadecurso->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obAreadecurso->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obAreadecurso->ativo_fl) ? 'table-active' : 'table-danger',
        'icon' => ICON_AREADECURSO,
        'title' =>TITLE_AREADECURSO,
        'titlelow' => TITLELOW_AREADECURSO,
        'direntity' => ROTA_AREADECURSO
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
  public static function getAreadecursoItensSelect($request,$id){
    $itensSelect = '';
    $resultsSelect = EntityAreadecurso::getAreadecursos(null,'areadecurso_nm ');
    //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

    while($obAreadecurso = $resultsSelect->fetchObject(EntityAreadecurso::class)){
      $itensSelect .= View::render('admin/modules/areadecurso/itemselect',[
        'idSelect' => $obAreadecurso->areadecurso_id,
        'selecionado' => ($id == $obAreadecurso->areadecurso_id) ? 'selected' : '',
        'nomeSelect' => $obAreadecurso->areadecurso_nm
      ]);
    }
    return $itensSelect;
  }

  //*** Método responsável por retornar o formulário de cadastro de um novo Tipo de Serviço *** EXCLUIDO

   /**
    * Método responsável por cadastro de um novo Tipo de Serviço no banco
    * @param Request $request
    * @return string
    */
    public static function setNovaAreadecurso($request){

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

      //VERIFICA SE JÁ EXISTE O TIPO de Serviço com mesmo nome CADASTRADO NO BANCO
      $obAreadecurso = EntityAreadecurso::getAreadecursoPorNome($nome);

      if($obAreadecurso instanceof EntityAreadecurso){

        //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
        $request->getRouter()->redirect('/admin/areadecursos?status=duplicado');
      }

      //NOVA ISNTANCIA DE Serviço
      $obAreadecurso = new EntityAreadecurso;

      ////$obAreadecurso::getAreadecursoPorEmail($posVars['email']);
      $obAreadecurso->areadecurso_nm = $nome;
      $obAreadecurso->areadecurso_des = $descricao;
      $obAreadecurso->ativo_fl = 's';
      $obAreadecurso->cadastrar();

      //REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/areadecursos?status=gravado&nm='.$nome.'&acao=grava');

    }


     /**
      * Método responsável por gravar a edição de um Tipo de Serviço
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditAreadecurso($request,$id){

      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);;
      $areadecurso_nm = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $areadecurso_des = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obAreadecurso = EntityAreadecurso::getAreadecursoPorId($id);

        if(!$obAreadecurso instanceof EntityAreadecurso){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/areadecursos?status=updatefail');
        }

        //echo "<pre>"; print_r($id_areadecurso); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obAreadecurso->areadecurso_nm = $areadecurso_nm ?? $obAreadecurso->areadecurso_nm;
        $obAreadecurso->areadecurso_des = $areadecurso_des  ?? $obAreadecurso->areadecurso_des;
        $obAreadecurso->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/areadecursos?status=alterado&nm='.$areadecurso_nm.'&acao=alter');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um Tipo de Serviço
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusAreadecursoModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obAreadecurso = EntityAreadecurso::getAreadecursoPorId($id);
         $strNome = $obAreadecurso->areadecurso_nm;


         if(!$obAreadecurso instanceof EntityAreadecurso){
           $request->getRouter()->redirect('/admin/areadecursos?status=updatefail');
         }


         //OBTÉM O TIPO DE SERVIÇO DO BANCO DE DADOS
         if($obAreadecurso->ativo_fl == 's'){
           $altStatus = 'n';
           $strMsn = ' DESATIVADO ';
         } elseif ($obAreadecurso->ativo_fl == 'n') {
           $altStatus = 's';
           $strMsn = ' ATIVADO ';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obAreadecurso->ativo_fl = $altStatus;
         $obAreadecurso->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/areadecursos?status=statusupdate&nm='.$strNome.$strMsn.'&acao=alter');

       }


    // Método responsável por retornar o formulário de exclusão de um Tipo de Serviço***** METODO XCLUIDO


     /**
      * Método responsável por retornar o formulário de exclusão de um Tipo de Serviço atraves de um Modal
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function getDeleteAreadecursoModal($request,$id){

        $obAreadecurso = EntityAreadecurso::getAreadecursoPorId($id);
        $strNome = $obAreadecurso->areadecurso_nm;

        if(!$obAreadecurso instanceof EntityAreadecurso){
          $request->getRouter()->redirect('/admin/areadecursos?status=updatefail');
        }

       //EXCLUI O USUÁRIO
        $obAreadecurso->excluir();
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/areadecursos?status=deletado&nm='.$strNome.$strMsn.'&acao=excluir');
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
       return Alert::getSuccess('Dados d'.TITLE_AREADECURSO .' <strong>'.$nm.'</strong> cadastrados com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados d'.TITLELOW_AREADECURSO .' <strong>'.$nm.'</strong> alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_AREADECURSO .'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe '.TITLELOW_AREADECURSO .' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_AREADECURSO.'!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status d'.TITLELOW_AREADECURSO .' <strong>'.$nm.'</strong> com sucesso!');
       // code...
       break;
   }
  }
}
