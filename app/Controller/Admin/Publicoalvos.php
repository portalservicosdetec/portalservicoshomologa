<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Publicoalvo as EntityPublicoalvo;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Db\Pagination;
//use \App\Http\Request;

const DIR_PUBLICOALVO = 'publicoalvo';
const ROTA_PUBLICOALVO = 'publicoalvos';
const ICON_PUBLICOALVO = 'bag-check';
const TITLE_PUBLICOALVO = 'Público-Alvo';
const TITLELOW_PUBLICOALVO = 'o público-alvo';

class Publicoalvos extends Page{

  /**
   * Método responsável pela renderização da view de listagem de Tipos de Serviço
   * @param Request $request
   * @return string
   */
  public static function getListPublicoalvos($request,$errorMessage = null){

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
    $content = View::render('admin/modules/'.DIR_PUBLICOALVO.'/index',[
      'icon' => ICON_PUBLICOALVO,
      'title' =>TITLE_PUBLICOALVO,
      'titlelow' => TITLELOW_PUBLICOALVO,
      'direntity' => ROTA_PUBLICOALVO,
      'itens' => self::getPublicoalvoItens($request,$obPagination),
      'status' => $status
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel(TITLE_PUBLICOALVO.' - EMERJ',$content,ROTA_PUBLICOALVO,$currentDepartamento,$currentPerfil);

  }

  /**
   * Método responsável por obter a renderização dos itens de Tipos de Serviço para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getPublicoalvoItens($request,&$obPagination){

    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/'.DIR_PUBLICOALVO.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_PUBLICOALVO.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_PUBLICOALVO.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_PUBLICOALVO.'/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityPublicoalvo::getPublicoalvos();

    //MONTA E RENDERIZA OS ITENS DE Publicoalvo
    while($obPublicoalvo = $results->fetchObject(EntityPublicoalvo::class)){
      $itens .= View::render('admin/modules/'.DIR_PUBLICOALVO.'/item',[
        'id' => $obPublicoalvo->publico_alvo_id,
        'nome' => $obPublicoalvo->publico_alvo_nm,
        'texto_ativo' => ('s' == $obPublicoalvo->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obPublicoalvo->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obPublicoalvo->ativo_fl) ? 'table-active' : 'table-danger',
        'icon' => ICON_PUBLICOALVO,
        'title' =>TITLE_PUBLICOALVO,
        'titlelow' => TITLELOW_PUBLICOALVO,
        'direntity' => ROTA_PUBLICOALVO
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
  public static function getPublicoalvoItensSelect($request,$id){
    $itensSelect = '';
    $resultsSelect = EntityPublicoalvo::getPublicoalvos(null,'publico_alvo_nm ');
    //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

    while($obPublicoalvo = $resultsSelect->fetchObject(EntityPublicoalvo::class)){
      $itensSelect .= View::render('admin/modules/publicoalvo/itemselect',[
        'idSelect' => $obPublicoalvo->publico_alvo_id,
        'selecionado' => ($id == $obPublicoalvo->publico_alvo_id) ? 'selected' : '',
        'nomeSelect' => $obPublicoalvo->publico_alvo_nm
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
    public static function setNovaPublicoalvo($request){

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

      //VERIFICA SE JÁ EXISTE O TIPO de Serviço com mesmo nome CADASTRADO NO BANCO
      $obPublicoalvo = EntityPublicoalvo::getPublicoalvoPorNome($nome);

      if($obPublicoalvo instanceof EntityPublicoalvo){

        //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
        $request->getRouter()->redirect('/admin/publicoalvos?status=duplicado');
      }

      //NOVA ISNTANCIA DE Serviço
      $obPublicoalvo = new EntityPublicoalvo;

      ////$obPublicoalvo::getPublicoalvoPorEmail($posVars['email']);
      $obPublicoalvo->publico_alvo_nm = $nome;
      $obPublicoalvo->ativo_fl = 's';
      $obPublicoalvo->cadastrar();

      //REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/publicoalvos?status=gravado&nm='.$nome.'&acao=grava');

    }


     /**
      * Método responsável por gravar a edição de um Tipo de Serviço
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditPublicoalvo($request,$id){

      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);;
      $publico_alvo_nm = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obPublicoalvo = EntityPublicoalvo::getPublicoalvoPorId($id);

        if(!$obPublicoalvo instanceof EntityPublicoalvo){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/publicoalvos?status=updatefail');
        }

        //echo "<pre>"; print_r($id_publicoalvo); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obPublicoalvo->publico_alvo_nm = $publico_alvo_nm ?? $obPublicoalvo->publico_alvo_nm;
        $obPublicoalvo->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/publicoalvos?status=alterado&nm='.$publico_alvo_nm.'&acao=alter');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um Tipo de Serviço
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusPublicoalvoModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obPublicoalvo = EntityPublicoalvo::getPublicoalvoPorId($id);
         $strNome = $obPublicoalvo->publico_alvo_nm;


         if(!$obPublicoalvo instanceof EntityPublicoalvo){
           $request->getRouter()->redirect('/admin/publicoalvos?status=updatefail');
         }


         //OBTÉM O TIPO DE SERVIÇO DO BANCO DE DADOS
         if($obPublicoalvo->ativo_fl == 's'){
           $altStatus = 'n';
           $strMsn = ' DESATIVADO ';
         } elseif ($obPublicoalvo->ativo_fl == 'n') {
           $altStatus = 's';
           $strMsn = ' ATIVADO ';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obPublicoalvo->ativo_fl = $altStatus;
         $obPublicoalvo->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/publicoalvos?status=statusupdate&nm='.$strNome.$strMsn.'&acao=alter');

       }


    // Método responsável por retornar o formulário de exclusão de um Tipo de Serviço***** METODO XCLUIDO


     /**
      * Método responsável por retornar o formulário de exclusão de um Tipo de Serviço atraves de um Modal
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function getDeletePublicoalvoModal($request,$id){

        $obPublicoalvo = EntityPublicoalvo::getPublicoalvoPorId($id);
        $strNome = $obPublicoalvo->publico_alvo_nm;

        if(!$obPublicoalvo instanceof EntityPublicoalvo){
          $request->getRouter()->redirect('/admin/publicoalvos?status=updatefail');
        }

       //EXCLUI O USUÁRIO
        $obPublicoalvo->excluir();
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/publicoalvos?status=deletado&nm='.$strNome.$strMsn.'&acao=excluir');
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
       return Alert::getSuccess('Dados d'.TITLE_PUBLICOALVO .' <strong>'.$nm.'</strong> cadastrados com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados d'.TITLELOW_PUBLICOALVO .' <strong>'.$nm.'</strong> alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_PUBLICOALVO .'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe '.TITLELOW_PUBLICOALVO .' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_PUBLICOALVO.'!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status d'.TITLELOW_PUBLICOALVO .' <strong>'.$nm.'</strong> com sucesso!');
       // code...
       break;
   }
  }
}
