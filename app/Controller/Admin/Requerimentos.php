<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Requerimento as EntityRequerimento;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Itensconf as EntityItensconf;
use \App\Model\Entity\Departamento as EntityDepartamento;
use \App\Controller\Pages\Departamento as PagesDepartamento;
use \App\Controller\Admin\Servicos as AdminServico;
use \App\Controller\Admin\Tipodeics as AdminTipodeics;
use \App\Controller\Admin\Usuarios as AdminUsuarios;
use \App\Db\Pagination;

class Requerimentos extends Page{

  /**
   * Método responsável por obter a renderização dos itens de Requerimentos para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */

  /**
   * Método responsável pela renderização da view de listagem de Requerimentos
   * @param Request $request
   * @return string
   */
  public static function getListRequerimentos($request,$errorMessage = null){

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
    $content = View::render('admin/modules/requerimento/index',[
      'icon' => ICON_IC,
      'title' =>TITLE_IC,
      'titlelow' => TITLELOW_IC,
      'direntity' => ROTA_IC,
      'itens' => self::getRequerimentoItens($request,$obPagination),
      'status' => self::getStatus($request),
      'optionsBuscaTipodeic' => $tipodeicSelecionado,
      'optionsBuscaServico' => $servicoSelecionado,
      'optionsBuscaUsuario' => $usuarioSelecionado,
      'optionsBuscaStatus' => $statusSelecionado
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Itens de Configuração - EMERJ',$content,'requerimentos',$currentDepartamento,$currentPerfil);
  }


  private static function getRequerimentoItens($request,&$obPagination){
    $itens = '';

    //RESONSÁVEL PELA RENDERIZAÇÃO DO MODAL DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
    $strEditModal = View::render('admin/modules/requerimento/editmodal',[]);
    $strAddModal = View::render('admin/modules/requerimento/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/requerimento/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/requerimento/deletemodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityRequerimento::getRequerimentos();

    //MONTA E RENDERIZA OS ITENS DE Requerimento
    while($obRequerimento = $results->fetchObject(EntityRequerimento::class)){
      $itens .= View::render('admin/modules/requerimento/item',[
        'id_chamado' => $obRequerimento->id_chamado,
        'id_atendimento' => $obRequerimento->id_atendimento,
        'id_itemdeconf' => $obRequerimento->id_itemdeconf,
        'texto_ativo' => ('s' == $obRequerimento->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obRequerimento->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obRequerimento->ativo_fl) ? 'table-active' : 'table-danger'
      ]);
    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    return $itens;
  }

  /**
   * Método responsável por retornar o formulário de cadastro de um novo Requerimento
   * @param Request $request
   * @return string
   */
   public static function getNovoRequerimento($request){


   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoRequerimento($request){


    }


    /**
     * Método responsável por retornar o formulário de cadastro de um novo item de configuração
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditRequerimento($request,$id){


     }

     /**
      * Método responsável por montar a renderização do checkbox de Itens de configuração para os formulários
      * @param Request $request
      * @param integer $id
      * @return string
      */
     public static function getRequerimentoItensCheckbox($request,$id){


     }


     /**
      * Método responsável por gravar a edição de um item de configuração
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditRequerimento($request,$id){


      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

        //DADOS DO POST
        $posVars = $request->getPostVars();
        $servico_id = $posVars['servico_id'] ?? '';
        $itemdeconfiguracao_id = $posVars['itemdeconfiguracao_id'] ?? '';

        $where = " id_servico = ".$servico_id." AND id_itemdeconfiguracao = ".$itemdeconfiguracao_id;

        //echo "<pre>"; print_r($where); echo "<pre>"; exit;
        //VERIFICA SE JÁ EXISTE O IC com mesmo nome e tipo CADASTRADO NO BANCO
        $obRequerimento = EntityRequerimento::getRequerimentos($where);

      //  echo "<pre>"; print_r($obRequerimento); echo "<pre>"; exit;
        if($obRequerimento instanceof EntityRequerimento){
          //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
          $request->getRouter()->redirect('/admin/requerimentos/novo?status=duplicado');
        }

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obRequerimento = EntityRequerimento::getRequerimentoPorId($id);

        if(!$obRequerimento instanceof EntityRequerimento){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/requerimentos/'.$id.'/edit?status=updatefail');
        }

        //echo "<pre>"; print_r($id_servico); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obRequerimento->id_servico = $posVars['servico_id'];
        $obRequerimento->id_itemdeconfiguracao = $posVars['itemdeconfiguracao_id'];
        $obRequerimento->id_departamento = $posVars['departamento_id'];
        $obRequerimento->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/requerimentos/'.$obRequerimento->requerimento_id.'/edit?status=alterado');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusRequerimentoModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obRequerimento = EntityRequerimento::getRequerimentoPorId($id);

         //PÁGINA ATUAL
         $uri=strstr("$_SERVER[REQUEST_URI]", '?');

         if(!$obRequerimento instanceof EntityRequerimento){
           $request->getRouter()->redirect('/admin/requerimentos?status=updatefail');
         }

         //CONTEÚDO DO FORMULÁRIO
         $content = View::render('admin/modules/requerimento/alterastatus',[
           'status' => self::getStatus($request),
           'paginaAtual' => $paginaAtual

         ]);

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obRequerimento->ativo_fl == 's'){
           $altStatus = 'n';
         } elseif ($obRequerimento->ativo_fl == 'n') {
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obRequerimento->ativo_fl = $altStatus;
         $obRequerimento->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/requerimentos'.$uri.'&status=statusupdate');

       }


      /**
       * Método responsável por retornar o formulário de exclusão de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteRequerimento($request,$id){

      //   echo "<pre>BBBBB"; print_r($id); echo "<pre>"; exit;


         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obRequerimento = EntityRequerimento::getRequerimentoPorId($id);

         if(!$obRequerimento instanceof EntityRequerimento){
           $request->getRouter()->redirect('/admin/requerimento');
         }

       //CONTEÚDO DA FORMULÁRIO
         $content = View::render('admin/modules/requerimento/delete',[
           'requerimento_id' => $obRequerimento->requerimento_id,
           'status' => self::getStatus($request)
         ]);


         //RETORNA A PÁGINA COMPLETA
         return parent::getPanel('Exclir IC',$content,'requerimentos');
       }

       /**
        * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function getDeleteRequerimentoModal($request,$id){

        //  echo "<pre>ALOOIII"; print_r($id); echo "<pre>"; exit;

        $obRequerimento = EntityRequerimento::getRequerimentoPorId($id);

        //CONTEÚDO DA FORMULÁRIO
          $content = View::render('admin/modules/requerimento/delete',[
            'servico' => EntityServico::getServicoPorId($obRequerimento->id_servico)->servico_nm,
            'itemdeconfiguracao' => EntityItensconf::getItensconfPorId($obRequerimento->id_itemdeconfiguracao)->itemdeconfiguracao_nm,
            'departamento' => EntityDepartamento::getDepartamentoPorId($obRequerimento->id_departamento)->departamento_sg,
            'status' => self::getStatus($request)
          ]);

          //OBTÉM O USUÁRIO DO BANCO DE DADOS
          $obRequerimento = EntityRequerimento::getRequerimentoPorId($id);

          $queryParams = $request->getQueryParams();
          $paginaAtual = $queryParams['pagina'] ?? 1;

          if(!$obRequerimento instanceof EntityRequerimento){
            $request->getRouter()->redirect('/admin/requerimentos');
          }

         //EXCLUI O USUÁRIO
          $obRequerimento->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/requerimentos?pagina='.$paginaAtual.'&status=deletado');
        }

       /**
        * Método responsável por excluir um usuário
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function setDeleteRequerimento($request,$id){



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
       return Alert::getSuccess('Requerimento cadastrado com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados do Requerimento alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Requerimento deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe um Requerimento com este nome para o mesmo Departamento!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status do Requerimento alterado com sucesso!');
       // code...
       break;
   }
  }
}
