<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Status as EntityStatus;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Itensconf as EntityItensconf;
use \App\Model\Entity\Departamento as EntityDepartamento;
use \App\Controller\Pages\Departamento as PagesDepartamento;
use \App\Controller\Admin\Servicos as AdminServico;
use \App\Controller\Admin\Tipodeics as AdminTipodeics;
use \App\Controller\Admin\Usuarios as AdminUsuarios;
use \App\Db\Pagination;

class Status extends Page{

  /**
   * Método responsável por obter a renderização dos itens de Status para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */

  /**
   * Método responsável pela renderização da view de listagem de Status
   * @param Request $request
   * @return string
   */
  public static function getListStatus($request,$errorMessage = null){

    $id = '';
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

    $status = self::getStatus($request);

    $tipodeicSelecionado = AdminTipodeics::getTipodeicItensSelect($request,$id);
    $servicoSelecionado = AdminServicos::getServicoItensSelect($request,$id);
    $usuarioSelecionado = AdminUsuarios::getUsuarioItensSelect($request,$id);
    $statusSelecionado = AdminStatus::getStatusItensSelect($request,$id);

    //CONTEÚDO DA HOME
    $content = View::render('admin/modules/status/index',[
      'icon' => ICON_IC,
      'title' =>TITLE_IC,
      'titlelow' => TITLELOW_IC,
      'direntity' => ROTA_IC,
      'itens' => self::getStatusItens($request,$obPagination),
      'status' => self::getStatus($request),
      'optionsBuscaTipodeic' => $tipodeicSelecionado,
      'optionsBuscaServico' => $servicoSelecionado,
      'optionsBuscaUsuario' => $usuarioSelecionado,
      'optionsBuscaStatus' => $statusSelecionado
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Itens de Configuração - EMERJ',$content,'status',$currentDepartamento,$currentPerfil);
  }


  private static function getStatusItens($request,&$obPagination){
    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/status/editmodal',[]);
    $strAddModal = View::render('admin/modules/status/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/status/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/status/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityStatus::getStatus();

    //MONTA E RENDERIZA OS ITENS DE Status
    while($obStatus = $results->fetchObject(EntityStatus::class)){
      $itens .= View::render('admin/modules/status/item',[
        'id_chamado' => $obStatus->id_chamado,
        'id_atendimento' => $obStatus->id_atendimento,
        'id_itemdeconf' => $obStatus->id_itemdeconf,
        'texto_ativo' => ('s' == $obStatus->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obStatus->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obStatus->ativo_fl) ? 'table-active' : 'table-danger'
      ]);
    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    return $itens;
  }

  /**
   * Método responsável por retornar o formulário de cadastro de um novo Status
   * @param Request $request
   * @return string
   */
   public static function getNovoStatus($request){


   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoStatus($request){


    }


    /**
     * Método responsável por retornar o formulário de cadastro de um novo item de configuração
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditStatus($request,$id){


     }

     /**
      * Método responsável por montar a renderização do checkbox de Itens de configuração para os formulários
      * @param Request $request
      * @param integer $id
      * @return string
      */
     public static function getStatusItensCheckbox($request,$id){


     }

     /**
      * Método responsável por montar a renderização do checkbox de Itens de configuração para os formulários
      * @param Request $request
      * @param integer $id
      * @return string
      */
     public static function getStatusItensSelect($request,$id){
       $itensSelect = '';
       $resultsSelect = EntityStatus::getStatus(null,'status_nm ASC');

       while($obStatus = $resultsSelect->fetchObject(EntityStatus::class)){
         $itensSelect .= View::render('admin/modules/status/itemselect',[
           'idSelect' => $obStatus->status_id,
           'selecionado' => ($id == $obStatus->status_id) ? 'selected' : '',
           'nomeSelect' => $obStatus->status_nm
         ]);
       }
       return $itensSelect;
     }


     /**
      * Método responsável por gravar a edição de um item de configuração
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditStatus($request,$id){


      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

        //DADOS DO POST
        $posVars = $request->getPostVars();
        $servico_id = $posVars['servico_id'] ?? '';
        $itemdeconfiguracao_id = $posVars['itemdeconfiguracao_id'] ?? '';

        $where = " id_servico = ".$servico_id." AND id_itemdeconfiguracao = ".$itemdeconfiguracao_id;

        //echo "<pre>"; print_r($where); echo "<pre>"; exit;
        //VERIFICA SE JÁ EXISTE O IC com mesmo nome e tipo CADASTRADO NO BANCO
        $obStatus = EntityStatus::getStatus($where);

      //  echo "<pre>"; print_r($obStatus); echo "<pre>"; exit;
        if($obStatus instanceof EntityStatus){
          //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
          $request->getRouter()->redirect('/admin/status/novo?status=duplicado');
        }

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obStatus = EntityStatus::getStatusPorId($id);

        if(!$obStatus instanceof EntityStatus){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/status/'.$id.'/edit?status=updatefail');
        }

        //echo "<pre>"; print_r($id_servico); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obStatus->id_servico = $posVars['servico_id'];
        $obStatus->id_itemdeconfiguracao = $posVars['itemdeconfiguracao_id'];
        $obStatus->id_departamento = $posVars['departamento_id'];
        $obStatus->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/status/'.$obStatus->status_id.'/edit?status=alterado');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusStatusModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obStatus = EntityStatus::getStatusPorId($id);

         //PÁGINA ATUAL
         $uri=strstr("$_SERVER[REQUEST_URI]", '?');

         if(!$obStatus instanceof EntityStatus){
           $request->getRouter()->redirect('/admin/status?status=updatefail');
         }

         //CONTEÚDO DO FORMULÁRIO
         $content = View::render('admin/modules/status/alterastatus',[
           'status' => self::getStatus($request),
           'paginaAtual' => $paginaAtual

         ]);

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obStatus->ativo_fl == 's'){
           $altStatus = 'n';
         } elseif ($obStatus->ativo_fl == 'n') {
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obStatus->ativo_fl = $altStatus;
         $obStatus->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/status'.$uri.'&status=statusupdate');

       }


      /**
       * Método responsável por retornar o formulário de exclusão de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteStatus($request,$id){

      //   echo "<pre>BBBBB"; print_r($id); echo "<pre>"; exit;


         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obStatus = EntityStatus::getStatusPorId($id);

         if(!$obStatus instanceof EntityStatus){
           $request->getRouter()->redirect('/admin/status');
         }

       //CONTEÚDO DA FORMULÁRIO
         $content = View::render('admin/modules/status/delete',[
           'status_id' => $obStatus->status_id,
           'status' => self::getStatus($request)
         ]);


         //RETORNA A PÁGINA COMPLETA
         return parent::getPanel('Exclir IC',$content,'status');
       }

       /**
        * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function getDeleteStatusModal($request,$id){

        //  echo "<pre>ALOOIII"; print_r($id); echo "<pre>"; exit;

        $obStatus = EntityStatus::getStatusPorId($id);

        //CONTEÚDO DA FORMULÁRIO
          $content = View::render('admin/modules/status/delete',[
            'servico' => EntityServico::getServicoPorId($obStatus->id_servico)->servico_nm,
            'itemdeconfiguracao' => EntityItensconf::getItensconfPorId($obStatus->id_itemdeconfiguracao)->itemdeconfiguracao_nm,
            'departamento' => EntityDepartamento::getDepartamentoPorId($obStatus->id_departamento)->departamento_sg,
            'status' => self::getStatus($request)
          ]);

          //OBTÉM O USUÁRIO DO BANCO DE DADOS
          $obStatus = EntityStatus::getStatusPorId($id);

          $queryParams = $request->getQueryParams();
          $paginaAtual = $queryParams['pagina'] ?? 1;

          if(!$obStatus instanceof EntityStatus){
            $request->getRouter()->redirect('/admin/status');
          }

         //EXCLUI O USUÁRIO
          $obStatus->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/status?pagina='.$paginaAtual.'&status=deletado');
        }

       /**
        * Método responsável por excluir um usuário
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function setDeleteStatus($request,$id){



       }



  /**
   * Método responsável por retornar a mensagem de status
   * @param Request $request
   * @return string
   */
  private static function getStatus($request){
    //QUERY PARAMS
    $queryParams = $request->getQueryParams();

    //STATUS
    if(!isset($queryParams['status'])) return '';

   //MENSAGENS DE STATUS
   switch ($queryParams['status']) {
     case 'gravado':
       return Alert::getSuccess('Status cadastrado com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados do Status alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Status deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe um Status com este nome para o mesmo Departamento!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status do Status alterado com sucesso!');
       // code...
       break;
   }
  }
}
