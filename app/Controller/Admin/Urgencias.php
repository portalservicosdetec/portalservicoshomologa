<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Urgencia as EntityUrgencia;
use \App\Db\Pagination;

const DIR_URGENCIA = 'urgencia';
const ROTA_URGENCIA = 'urgencias';
const ICON_URGENCIA = 'telephone-inbound';
const TITLE_URGENCIA = 'Urgencias';
const TITLELOW_URGENCIA = 'a urgencia';

class Urgencias extends Page{

  /**
   * Método responsável por obter a renderização dos itens de Urgencias para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getUrgenciaItens($request,&$obPagination){
    $itens = '';

  }

  /**
   * Método responsável pela renderização da view de listagem de Urgencias
   * @param Request $request
   * @return string
   */
  public static function getListUrgencias($request,$errorMessage = null){

  }

  /**
   * Método responsável por retornar o formulário de cadastro de um novo Urgencia
   * @param Request $request
   * @return string
   */
   public static function getNovoUrgencia($request){


   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoUrgencia($request){


    }


    /**
     * Método responsável por retornar o formulário de cadastro de um novo item de configuração
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditUrgencia($request,$id){


     }

     /**
      * Método responsável por montar a renderização do checkbox de Itens de configuração para os formulários
      * @param Request $request
      * @param integer $id
      * @return string
      */
     public static function getUrgenciaItensSelect($request,$id){
       $itensSelect = '';
       $resultsSelect = EntityUrgencia::getUrgencias(null,'urgencia_id ');
       //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

       while($obUrgencia = $resultsSelect->fetchObject(EntityUrgencia::class)){
         $itensSelect .= View::render('admin/modules/'.DIR_URGENCIA.'/itemselect',[
           'idSelect' => $obUrgencia->urgencia_id,
           'selecionado' => ($id == $obUrgencia->urgencia_id) ? 'selected' : '',
           'nomeSelect' => $obUrgencia->urgencia_nm
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
      public static function setEditUrgencia($request,$id){


        //DADOS DO POST
        $posVars = $request->getPostVars();
        $servico_id = $posVars['servico_id'] ?? '';
        $itemdeconfiguracao_id = $posVars['itemdeconfiguracao_id'] ?? '';

        $where = " id_servico = ".$servico_id." AND id_itemdeconfiguracao = ".$itemdeconfiguracao_id;

        //echo "<pre>"; print_r($where); echo "<pre>"; exit;
        //VERIFICA SE JÁ EXISTE O IC com mesmo nome e tipo CADASTRADO NO BANCO
        $obUrgencia = EntityUrgencia::getUrgencias($where);

      //  echo "<pre>"; print_r($obUrgencia); echo "<pre>"; exit;
        if($obUrgencia instanceof EntityUrgencia){
          //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
          $request->getRouter()->redirect('/admin/urgencias/novo?status=duplicado');
        }

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUrgencia = EntityUrgencia::getUrgenciaPorId($id);

        if(!$obUrgencia instanceof EntityUrgencia){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/urgencias/'.$id.'/edit?status=updatefail');
        }

        //echo "<pre>"; print_r($id_servico); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obUrgencia->id_servico = $posVars['servico_id'];
        $obUrgencia->id_itemdeconfiguracao = $posVars['itemdeconfiguracao_id'];
        $obUrgencia->id_departamento = $posVars['departamento_id'];
        $obUrgencia->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/urgencias/'.$obUrgencia->urgencia_id.'/edit?status=alterado');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusUrgenciaModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obUrgencia = EntityUrgencia::getUrgenciaPorId($id);

         //PÁGINA ATUAL
         $uri=strstr("$_SERVER[REQUEST_URI]", '?');

         if(!$obUrgencia instanceof EntityUrgencia){
           $request->getRouter()->redirect('/admin/urgencias?status=updatefail');
         }

         //CONTEÚDO DO FORMULÁRIO
         $content = View::render('admin/modules/urgencia/alterastatus',[
           'status' => self::getStatus($request),
           'paginaAtual' => $paginaAtual

         ]);

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obUrgencia->ativo_fl == 's'){
           $altStatus = 'n';
         } elseif ($obUrgencia->ativo_fl == 'n') {
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obUrgencia->ativo_fl = $altStatus;
         $obUrgencia->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/urgencias'.$uri.'&status=statusupdate');

       }


      /**
       * Método responsável por retornar o formulário de exclusão de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteUrgencia($request,$id){

      //   echo "<pre>BBBBB"; print_r($id); echo "<pre>"; exit;

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obUrgencia = EntityUrgencia::getUrgenciaPorId($id);

         if(!$obUrgencia instanceof EntityUrgencia){
           $request->getRouter()->redirect('/admin/urgencia');
         }

       //CONTEÚDO DA FORMULÁRIO
         $content = View::render('admin/modules/urgencia/delete',[
           'urgencia_id' => $obUrgencia->urgencia_id,
           'status' => self::getStatus($request)
         ]);


         //RETORNA A PÁGINA COMPLETA
         return parent::getPanel('Exclir IC',$content,'urgencias');
       }

       /**
        * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function getDeleteUrgenciaModal($request,$id){

        //  echo "<pre>ALOOIII"; print_r($id); echo "<pre>"; exit;

        $obUrgencia = EntityUrgencia::getUrgenciaPorId($id);

        //CONTEÚDO DA FORMULÁRIO
          $content = View::render('admin/modules/urgencia/delete',[
            'servico' => EntityServico::getServicoPorId($obUrgencia->id_servico)->servico_nm,
            'itemdeconfiguracao' => EntityItensconf::getItensconfPorId($obUrgencia->id_itemdeconfiguracao)->itemdeconfiguracao_nm,
            'departamento' => EntityDepartamento::getDepartamentoPorId($obUrgencia->id_departamento)->departamento_sg,
            'status' => self::getStatus($request)
          ]);

          //OBTÉM O USUÁRIO DO BANCO DE DADOS
          $obUrgencia = EntityUrgencia::getUrgenciaPorId($id);

          $queryParams = $request->getQueryParams();
          $paginaAtual = $queryParams['pagina'] ?? 1;

          if(!$obUrgencia instanceof EntityUrgencia){
            $request->getRouter()->redirect('/admin/urgencias');
          }

         //EXCLUI O USUÁRIO
          $obUrgencia->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/urgencias?pagina='.$paginaAtual.'&status=deletado');
        }

       /**
        * Método responsável por excluir um usuário
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function setDeleteUrgencia($request,$id){



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
       return Alert::getSuccess('Urgencia cadastrado com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados do Urgencia alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Urgencia deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe um Urgencia com este nome para o mesmo Departamento!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status do Urgencia alterado com sucesso!');
       // code...
       break;
   }
  }
}
