<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Criticidade as EntityCriticidade;
use \App\Db\Pagination;

const DIR_CRITICIDADE = 'criticidade';
const ROTA_CRITICIDADE = 'criticidades';
const ICON_CRITICIDADE = 'telephone-inbound';
const TITLE_CRITICIDADE = 'Criticidades';
const TITLELOW_CRITICIDADE = 'a criticidade';

class Criticidades extends Page{

  /**
   * Método responsável por obter a renderização dos itens de Criticidades para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getCriticidadeItens($request,&$obPagination){
    $itens = '';

  }

  /**
   * Método responsável pela renderização da view de listagem de Criticidades
   * @param Request $request
   * @return string
   */
  public static function getListCriticidades($request,$errorMessage = null){

  }

  /**
   * Método responsável por retornar o formulário de cadastro de um novo Criticidade
   * @param Request $request
   * @return string
   */
   public static function getNovoCriticidade($request){


   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoCriticidade($request){


    }


    /**
     * Método responsável por retornar o formulário de cadastro de um novo item de configuração
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditCriticidade($request,$id){


     }

     /**
      * Método responsável por montar a renderização do checkbox de Itens de configuração para os formulários
      * @param Request $request
      * @param integer $id
      * @return string
      */
     public static function getCriticidadeItensSelect($request,$id){
       $itensSelect = '';
       $resultsSelect = EntityCriticidade::getCriticidades(null,'criticidade_id ');
       //echo "<pre>"; print_r($resultsSelect); echo "<pre>"; exit;

       while($obCriticidade = $resultsSelect->fetchObject(EntityCriticidade::class)){
         $itensSelect .= View::render('admin/modules/'.DIR_CRITICIDADE.'/itemselect',[
           'idSelect' => $obCriticidade->criticidade_id,
           'selecionado' => ($id == $obCriticidade->criticidade_id) ? 'selected' : '',
           'nomeSelect' => $obCriticidade->criticidade_nm
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
      public static function setEditCriticidade($request,$id){


        //DADOS DO POST
        $posVars = $request->getPostVars();
        $servico_id = $posVars['servico_id'] ?? '';
        $itemdeconfiguracao_id = $posVars['itemdeconfiguracao_id'] ?? '';

        $where = " id_servico = ".$servico_id." AND id_itemdeconfiguracao = ".$itemdeconfiguracao_id;

        //echo "<pre>"; print_r($where); echo "<pre>"; exit;
        //VERIFICA SE JÁ EXISTE O IC com mesmo nome e tipo CADASTRADO NO BANCO
        $obCriticidade = EntityCriticidade::getCriticidades($where);

      //  echo "<pre>"; print_r($obCriticidade); echo "<pre>"; exit;
        if($obCriticidade instanceof EntityCriticidade){
          //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
          $request->getRouter()->redirect('/admin/criticidades/novo?status=duplicado');
        }

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obCriticidade = EntityCriticidade::getCriticidadePorId($id);

        if(!$obCriticidade instanceof EntityCriticidade){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/criticidades/'.$id.'/edit?status=updatefail');
        }

        //echo "<pre>"; print_r($id_servico); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obCriticidade->id_servico = $posVars['servico_id'];
        $obCriticidade->id_itemdeconfiguracao = $posVars['itemdeconfiguracao_id'];
        $obCriticidade->id_departamento = $posVars['departamento_id'];
        $obCriticidade->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/criticidades/'.$obCriticidade->criticidade_id.'/edit?status=alterado');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusCriticidadeModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obCriticidade = EntityCriticidade::getCriticidadePorId($id);

         //PÁGINA ATUAL
         $uri=strstr("$_SERVER[REQUEST_URI]", '?');

         if(!$obCriticidade instanceof EntityCriticidade){
           $request->getRouter()->redirect('/admin/criticidades?status=updatefail');
         }

         //CONTEÚDO DO FORMULÁRIO
         $content = View::render('admin/modules/criticidade/alterastatus',[
           'status' => self::getStatus($request),
           'paginaAtual' => $paginaAtual

         ]);

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obCriticidade->ativo_fl == 's'){
           $altStatus = 'n';
         } elseif ($obCriticidade->ativo_fl == 'n') {
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obCriticidade->ativo_fl = $altStatus;
         $obCriticidade->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/criticidades'.$uri.'&status=statusupdate');

       }


      /**
       * Método responsável por retornar o formulário de exclusão de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteCriticidade($request,$id){

      //   echo "<pre>BBBBB"; print_r($id); echo "<pre>"; exit;

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obCriticidade = EntityCriticidade::getCriticidadePorId($id);

         if(!$obCriticidade instanceof EntityCriticidade){
           $request->getRouter()->redirect('/admin/criticidade');
         }

       //CONTEÚDO DA FORMULÁRIO
         $content = View::render('admin/modules/criticidade/delete',[
           'criticidade_id' => $obCriticidade->criticidade_id,
           'status' => self::getStatus($request)
         ]);


         //RETORNA A PÁGINA COMPLETA
         return parent::getPanel('Exclir IC',$content,'criticidades');
       }

       /**
        * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function getDeleteCriticidadeModal($request,$id){

        //  echo "<pre>ALOOIII"; print_r($id); echo "<pre>"; exit;

        $obCriticidade = EntityCriticidade::getCriticidadePorId($id);

        //CONTEÚDO DA FORMULÁRIO
          $content = View::render('admin/modules/criticidade/delete',[
            'servico' => EntityServico::getServicoPorId($obCriticidade->id_servico)->servico_nm,
            'itemdeconfiguracao' => EntityItensconf::getItensconfPorId($obCriticidade->id_itemdeconfiguracao)->itemdeconfiguracao_nm,
            'departamento' => EntityDepartamento::getDepartamentoPorId($obCriticidade->id_departamento)->departamento_sg,
            'status' => self::getStatus($request)
          ]);

          //OBTÉM O USUÁRIO DO BANCO DE DADOS
          $obCriticidade = EntityCriticidade::getCriticidadePorId($id);

          $queryParams = $request->getQueryParams();
          $paginaAtual = $queryParams['pagina'] ?? 1;

          if(!$obCriticidade instanceof EntityCriticidade){
            $request->getRouter()->redirect('/admin/criticidades');
          }

         //EXCLUI O USUÁRIO
          $obCriticidade->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/criticidades?pagina='.$paginaAtual.'&status=deletado');
        }

       /**
        * Método responsável por excluir um usuário
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function setDeleteCriticidade($request,$id){



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
       return Alert::getSuccess('Criticidade cadastrado com sucesso!');
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados do Criticidade alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Criticidade deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe um Criticidade com este nome para o mesmo Departamento!');
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status do Criticidade alterado com sucesso!');
       // code...
       break;
   }
  }
}
