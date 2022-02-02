<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Tipodeocorrencia as EntityTipodeocorrencia;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Db\Pagination;
//use \App\Http\Request;

const DIR_TIPOSDEOCORRENCIA = 'tipodeocorrencia';
const ROTA_TIPOSDEOCORRENCIA = 'tipodeocorrencias';
const ICON_TIPOSDEOCORRENCIA = 'bag-check';
const TITLE_TIPOSDEOCORRENCIA = 'Tipos de Ocorrências';
const TITLELOW_TIPOSDEOCORRENCIA = 'o tipo de ocorrência';

class Tipodeocorrencias extends Page{

  /**
   * Método responsável pela renderização da view de listagem de Tipos de Serviço
   * @param Request $request
   * @return string
   */
  public static function getListTipodeocorrencias($request,$errorMessage = null){

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
    $content = View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/index',[
      'icon' => ICON_TIPOSDEOCORRENCIA,
      'title' =>TITLE_TIPOSDEOCORRENCIA,
      'titlelow' => TITLELOW_TIPOSDEOCORRENCIA,
      'direntity' => ROTA_TIPOSDEOCORRENCIA,
      'itens' => self::getTipodeocorrenciaItens($request,$obPagination),
      'status' => $status
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel(TITLE_TIPOSDEOCORRENCIA.' - EMERJ',$content,ROTA_TIPOSDEOCORRENCIA,$currentDepartamento,$currentPerfil);

  }

  /**
   * Método responsável por obter a renderização dos itens de Tipos de Serviço para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getTipodeocorrenciaItens($request,&$obPagination){

    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityTipodeocorrencia::getTipodeocorrencias();

    //MONTA E RENDERIZA OS ITENS DE Tipodeocorrencia
    while($obTipodeocorrencia = $results->fetchObject(EntityTipodeocorrencia::class)){
      $itens .= View::render('admin/modules/'.DIR_TIPOSDEOCORRENCIA.'/item',[
        'id' => $obTipodeocorrencia->tipodeocorrencia_id,
        'nome' => $obTipodeocorrencia->tipodeocorrencia_nm,
        'descricao' => $obTipodeocorrencia->tipodeocorrencia_des,
        'texto_ativo' => ('s' == $obTipodeocorrencia->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obTipodeocorrencia->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obTipodeocorrencia->ativo_fl) ? 'table-active' : 'table-danger',
        'icon' => ICON_TIPOSDEOCORRENCIA,
        'title' =>TITLE_TIPOSDEOCORRENCIA,
        'titlelow' => TITLELOW_TIPOSDEOCORRENCIA,
        'direntity' => ROTA_TIPOSDEOCORRENCIA
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
  public static function getTipodeocorrenciaItensSelect($request,$id){
    $itensSelect = '';
    $resultsSelect = EntityTipodeocorrencia::getTipodeocorrencias(null,'tipodeocorrencia_id');
    //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

    while($obTipodeocorrencia = $resultsSelect->fetchObject(EntityTipodeocorrencia::class)){
      $itensSelect .= View::render('admin/modules/tipodeocorrencia/itemselect',[
        'idSelect' => $obTipodeocorrencia->tipodeocorrencia_id,
        'selecionado' => ($id == $obTipodeocorrencia->tipodeocorrencia_id) ? 'selected' : '',
        'nomeSelect' => $obTipodeocorrencia->tipodeocorrencia_nm
      ]);
    }
    return $itensSelect;
  }

  /**
   * Método responsável por montar a renderização do select de Tipo de Serviço para os formulários
   * @param Request $request
   * @param integer $id
   * @return string
   */
  public static function getTipodeocorrenciaItensSelectChamados($request,$id){
    $itensSelect = '';
    $where = '';
    $departamento = '';
    $servico = '';

    $queryParams = $request->getQueryParams();
    $id_departamento = $queryParams['departamento'] ?? 0;
    $id_servico = $queryParams['servico'] ?? 0;
    $id_tipodeocorrencia = $queryParams['tipodeocorrencia'] ?? 0;

    if($id_servico <> 0){
      $where = 'id_servico = '.$id_servico;
      if($id_departamento <> 0){
        $where = $where .' AND id_departamento = '.$id_departamento;
      }
    } else {
      if($id_departamento <> 0){
        $where = 'id_servico = servico_id AND id_departamento = '.$id_departamento;
      }
    }

    $resultsSelect = EntityAtendimento::getAtendimentos2($where,'atendimento_id ','tb_servico, tb_atendimento',null,'DISTINCT id_tipodeocorrencia');

    //$resultsSelect = EntityTipodeocorrencia::getTipodeocorrencias(null,'tipodeocorrencia_nm ');

    while($obTipodeocorrencia = $resultsSelect->fetchObject(EntityAtendimento::class)){
      $itensSelect .= View::render('admin/modules/tipodeocorrencia/itemselect',[
        'idSelect' => $obTipodeocorrencia->id_tipodeocorrencia,
        'selecionado' => ($id_tipodeocorrencia == $obTipodeocorrencia->id_tipodeocorrencia) ? 'selected' : '',
        'nomeSelect' => EntityTipodeocorrencia::getTipodeocorrenciaPorId($obTipodeocorrencia->id_tipodeocorrencia)->tipodeocorrencia_nm
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
    public static function setNovoTipodeocorrencia($request){

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

      //VERIFICA SE JÁ EXISTE O TIPO de Serviço com mesmo nome CADASTRADO NO BANCO
      $obTipodeocorrencia = EntityTipodeocorrencia::getTipodeocorrenciaPorNome($nome);

      if($obTipodeocorrencia instanceof EntityTipodeocorrencia){

        //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
        $request->getRouter()->redirect('/admin/tipodeocorrencias?status=duplicado');
      }

      //NOVA ISNTANCIA DE Serviço
      $obTipodeocorrencia = new EntityTipodeocorrencia;

      ////$obTipodeocorrencia::getTipodeocorrenciaPorEmail($posVars['email']);
      $obTipodeocorrencia->tipodeocorrencia_nm = $nome;
      $obTipodeocorrencia->tipodeocorrencia_des = $descricao;
      $obTipodeocorrencia->ativo_fl = 's';
      $obTipodeocorrencia->cadastrar();

      //REDIRECIONA O USUÁRIO
      $request->getRouter()->redirect('/admin/tipodeocorrencias?status=gravado&nm='.$nome.'&acao=grava');

    }


     /**
      * Método responsável por gravar a edição de um Tipo de Serviço
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditTipodeocorrencia($request,$id){

      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);;
      $tipodeocorrencia_nm = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $tipodeocorrencia_des = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obTipodeocorrencia = EntityTipodeocorrencia::getTipodeocorrenciaPorId($id);

        if(!$obTipodeocorrencia instanceof EntityTipodeocorrencia){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/tipodeocorrencias?status=updatefail');
        }

        //echo "<pre>"; print_r($id_tipodeocorrencia); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obTipodeocorrencia->tipodeocorrencia_nm = $tipodeocorrencia_nm ?? $obTipodeocorrencia->tipodeocorrencia_nm;
        $obTipodeocorrencia->tipodeocorrencia_des = $tipodeocorrencia_des  ?? $obTipodeocorrencia->tipodeocorrencia_des;
        $obTipodeocorrencia->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/tipodeocorrencias?status=alterado&nm='.$tipodeocorrencia_nm.'&acao=alter');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um Tipo de Serviço
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusTipodeocorrenciaModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obTipodeocorrencia = EntityTipodeocorrencia::getTipodeocorrenciaPorId($id);
         $strNome = $obTipodeocorrencia->tipodeocorrencia_nm;


         if(!$obTipodeocorrencia instanceof EntityTipodeocorrencia){
           $request->getRouter()->redirect('/admin/tipodeocorrencias?status=updatefail');
         }


         //OBTÉM O TIPO DE SERVIÇO DO BANCO DE DADOS
         if($obTipodeocorrencia->ativo_fl == 's'){
           $altStatus = 'n';
           $strMsn = ' DESATIVADO ';
         } elseif ($obTipodeocorrencia->ativo_fl == 'n') {
           $altStatus = 's';
           $strMsn = ' ATIVADO ';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obTipodeocorrencia->ativo_fl = $altStatus;
         $obTipodeocorrencia->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/tipodeocorrencias?status=statusupdate&nm='.$strNome.$strMsn.'&acao=alter');

       }


    // Método responsável por retornar o formulário de exclusão de um Tipo de Serviço***** METODO XCLUIDO


     /**
      * Método responsável por retornar o formulário de exclusão de um Tipo de Serviço atraves de um Modal
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function getDeleteTipodeocorrenciaModal($request,$id){

        $obTipodeocorrencia = EntityTipodeocorrencia::getTipodeocorrenciaPorId($id);
        $strNome = $obTipodeocorrencia->tipodeocorrencia_nm;

        if(!$obTipodeocorrencia instanceof EntityTipodeocorrencia){
          $request->getRouter()->redirect('/admin/tipodeocorrencias?status=updatefail');
        }

       //EXCLUI O USUÁRIO
        $obTipodeocorrencia->excluir();
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/tipodeocorrencias?status=deletado&nm='.$strNome.$strMsn.'&acao=excluir');
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
       return Alert::getSuccess('Dados d'.TITLE_TIPOSDEOCORRENCIA .' <strong>'.$nm.'</strong> cadastrados com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados d'.TITLELOW_TIPOSDEOCORRENCIA .' <strong>'.$nm.'</strong> alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_TIPOSDEOCORRENCIA .'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe '.TITLELOW_TIPOSDEOCORRENCIA .' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_TIPOSDEOCORRENCIA.'!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status d'.TITLELOW_TIPOSDEOCORRENCIA .' <strong>'.$nm.'</strong> com sucesso!');
       // code...
       break;
   }
  }
}
