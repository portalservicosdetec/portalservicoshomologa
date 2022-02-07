<?php

namespace App\Controller\Admin;

use \DateTime;

use \App\Utils\View;
use \App\Model\Entity\Requerimento as EntityRequerimento;
use \App\Model\Entity\Chamado as EntityChamado;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Tipodeic as EntityTipodeic;
use \App\Model\Entity\Itensconf as EntityItensconf;
use \App\Model\Entity\Departamento as EntityDepartamento;
use \App\Model\Entity\Localizacao as EntityLocalizacao;
use \App\Model\Entity\Criticidade as EntityCriticidade;
use \App\Model\Entity\Urgencia as EntityUrgencia;
use \App\Model\Entity\Status as EntityStatus;
use \App\Model\Entity\Usuario as EntityUsuario;
use \App\Controller\Pages\Departamento as PagesDepartamento;
use \App\Controller\Admin\Servicos as AdminServico;
use \App\Controller\Admin\Departamentos as AdminDepartamento;
use \App\Controller\Admin\Tipodeservicos as AdminTipodeServico;
use \App\Controller\Admin\Tipodeocorrencias as AdminTipodeocorrencia;
use \App\Controller\Admin\Tipodeics as AdminTipodeic;
use \App\Controller\Admin\Categoriadeics as AdminCategoriadeics;
use \App\Controller\Admin\Atendimentos as AdminAtendimento;
use \App\Controller\Admin\Itensconfs as AdminItensconfs;
use \App\Controller\Admin\Usuarios as AdminUsuario;
use \App\Controller\Admin\Status as AdminStatus;
use \App\Controller\Admin\Criticidades as AdminCriticidade;
use \App\Controller\Admin\Urgencias as AdminUrgencia;
use \App\Db\Pagination;

const DIR_REQUERIMENTO = 'requerimento';
const ROTA_REQUERIMENTO = 'requerimentos';
const ICON_REQUERIMENTO = 'box-arrow-in-down';
const TITLE_REQUERIMENTO = 'Requisições';
const TITLELOW_REQUERIMENTO = 'a requisição';

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

   //CONTEÚDO DA HOME
    $content = View::render('admin/modules/requerimento/index',[
      'icon' => ICON_REQUERIMENTO,
      'title' =>TITLE_REQUERIMENTO,
      'titlelow' => TITLELOW_REQUERIMENTO,
      'direntity' => ROTA_REQUERIMENTO,
      'itens' => self::getRequerimentoItens($request,$obPagination),
      'status' => $status
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
        'id' => $obRequerimento->requerimento_id,
        'descricao' => $obRequerimento->requerimento_desc,
        'nrdgtec' => $obRequerimento->nrdgtec,

        'ticket' => EntityChamado::getChamadoPorId($obRequerimento->id_chamado)->nr_solicitacao,
        'atendimento' => EntityServico::getServicoPorId(EntityAtendimento::getAtendimentoPorId($obRequerimento->id_atendimento)->id_servico)->servico_nm.' - '.EntityTipodeic::getTipodeicPorId(EntityAtendimento::getAtendimentoPorId($obRequerimento->id_atendimento)->id_tipodeic)->tipodeic_nm,
        'criticidade' => $obRequerimento->id_criticidade,
        'urgencia' => $obRequerimento->id_urgencia,
        'status' => EntityStatus::getStatusPorId($obRequerimento->id_status)->status_nm,
        'atendente' => $obRequerimento->id_atendente,
        'requisitante' => EntityUsuario::getUsuarioPorId($obRequerimento->id_atendido)->usuario_nm,
        'autorizador' => $obRequerimento->id_autorizador,

      //  'ticket' => EntityChamado::getChamadoPorId($obRequerimento->id_chamado)->nr_solicitacao,
      //  'atendimento' => EntityServico::getServicoPorId(EntityAtendimento::getAtendimentoPorId($obRequerimento->id_atendimento)->id_servico)->servico_nm.' - '.EntityTipodeic::getTipodeicPorId(EntityAtendimento::getAtendimentoPorId($obRequerimento->id_atendimento)->id_tipodeic)->tipodeic_nm,
      //  'criticidade' => EntityCriticidade::getCriticidadePorId($obRequerimento->id_criticidade)->criticidade_nm,
      //  'urgencia' => EntityUrgencia::getUrgenciaPorId($obRequerimento->id_urgencia)->urgencia_nm,
      //  'status' => EntityStatus::getStatusPorId($obRequerimento->id_status)->status_nm,
      //  'atendente' => EntityUsuario::getUsuarioPorId($obRequerimento->id_atendente)->usuario_nm,
      //  'requisitante' => EntityUsuario::getUsuarioPorId($obRequerimento->id_atendido)->usuario_nm,
      //  'autorizador' => EntityUsuario::getUsuarioPorId($obRequerimento->id_autorizador)->usuario_nm,

        'data_add' => $obRequerimento->data_add,
        'data_alteracao' => $obRequerimento->data_up,
        'ativo_fl' => $obRequerimento->ativo_fl,
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
   public static function getNovoRequerimento($request,$id_chamado){

     date_default_timezone_set('America/Sao_Paulo');

     $agora = date("Y-m-d");
     $dateNow = new DateTime($agora);

     $UsuarioSolicitanteNome = '';
     $UsuarioSolicitanteSiglaDep = '';

     $id_tipodeservico = filter_input(INPUT_GET, 'tipodeservico', FILTER_SANITIZE_NUMBER_INT);
     $id_servico = filter_input(INPUT_GET, 'servico', FILTER_SANITIZE_NUMBER_INT);
     $id_itemdeconfiguracao = filter_input(INPUT_GET, 'itemdeconfiguracao', FILTER_SANITIZE_NUMBER_INT);
     $id_tipodeic = filter_input(INPUT_GET, 'tipodeic', FILTER_SANITIZE_NUMBER_INT);
     $id_departamento = filter_input(INPUT_GET, 'departamento', FILTER_SANITIZE_NUMBER_INT);
     $id_categoria_ic = filter_input(INPUT_GET, 'categoriadeic', FILTER_SANITIZE_NUMBER_INT);
     $id_atendimento = filter_input(INPUT_GET, 'atendimento', FILTER_SANITIZE_NUMBER_INT);
     $id_usuario = filter_input(INPUT_GET, 'usuario', FILTER_SANITIZE_NUMBER_INT);
     $id_status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT);
     $id_criticidade = filter_input(INPUT_GET, 'criticidade', FILTER_SANITIZE_NUMBER_INT);
     $id_urgencia = filter_input(INPUT_GET, 'urgencia', FILTER_SANITIZE_NUMBER_INT);
     $id_tipodeocorrencia = filter_input(INPUT_GET, 'tipodeocorrencia', FILTER_SANITIZE_NUMBER_INT);

     $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
     $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

     $optionsUsuario = AdminUsuario::getUsuarioItensSelectEmail($request,null);
     $obChamado = EntityChamado::getChamadoPorId(View::crypt('decrypt',$id_chamado));

     $tipodeocorrenciaSelecionado = AdminTipodeocorrencia::getTipodeocorrenciaItensSelect($request,$id_tipodeocorrencia);
     $servicoSelecionado = AdminServico::getServicoItensSelect($request,$id_servico);
     $atendimentoSelecionado  = AdminAtendimento::getAtendimentoItensSelect($request,$id_atendimento);
     $usuarioSelecionado = AdminUsuario::getUsuarioItensSelect($request,$id_usuario);
     $statusSelecionado = AdminStatus::getStatusItensSelect($request,$id_status);
     $criticidadeSelecionado = AdminCriticidade::getCriticidadeItensSelect($request,$id_criticidade);
     $urgenciaSelecionado = AdminUrgencia::getUrgenciaItensSelect($request,$id_urgencia);

     if ($obChamado->id_usuario > 0) {
       $obUsuarioContato = EntityUsuario::getUsuarioPorId($obChamado->id_usuario);
       if($obUsuarioContato instanceof EntityUsuario){
         $UsuarioContatoId = $obUsuarioContato->usuario_id;
         $UsuarioContatoNome = $obUsuarioContato->usuario_nm;
         $UsuarioContatoEmail = $obUsuarioContato->email;
         $UsuarioContatoTelefone = $obUsuarioContato->usuario_fone;
         $UsuarioContatoSiglaDep = EntityDepartamento::getDepartamentoPorId($obUsuarioContato->id_departamento)->departamento_sg;
         $UsuarioContatoSala = EntityLocalizacao::getLocalizacaoPorId($obUsuarioContato->sala)->localizacao_nm;
       }
     }

   //  echo "<pre>BBBBB"; print_r($obChamado->solicitado_por); echo "<pre>";

     if ($obChamado->solicitado_por > 0) {
       $obUsuarioSolicitante = EntityUsuario::getUsuarioPorId($obChamado->solicitado_por);
       if($obUsuarioSolicitante instanceof EntityUsuario){
         $UsuarioSolicitanteId = $obUsuarioSolicitante->usuario_id ?? '';
         $UsuarioSolicitanteNome = $obUsuarioSolicitante->usuario_nm;
         $UsuarioSolicitanteEmail = $obUsuarioSolicitante->email;
         $UsuarioSolicitanteTelefone = $obUsuarioSolicitante->usuario_fone;
         $UsuarioSolicitanteSiglaDep = EntityDepartamento::getDepartamentoPorId($obUsuarioSolicitante->id_departamento)->departamento_sg;
         $UsuarioSolicitanteSala = EntityLocalizacao::getLocalizacaoPorId($obUsuarioSolicitante->sala)->localizacao_nm;
       }
     }

     $dataChamado = new DateTime($obChamado->data_add);
     $intervalo = $dataChamado->diff($dateNow);

     //CONTEÚDO DO FORMULÁRIO
     $content = View::render('admin/modules/requerimento/form',[
       'id_chamado' => $id_chamado,
       'ticket' => $obChamado->nr_solicitacao,
       'titulo' => $obChamado->chamado_nm,
       'descricao' => View::limitCharacter($obChamado->chamado_des,'\S',1,30) ?? '',
       'descricaoFull' => $obChamado->chamado_des ?? '',
       'data_abertura' => date('d/m/y', strtotime($obChamado->data_add)).' às '.date('H:i', strtotime($obChamado->data_add)).' ('.$intervalo->format('%R%a dias').')',

       "UsuarioContatoId" => $UsuarioContatoId,
       "UsuarioContatoNome" => $UsuarioContatoNome,
       "UsuarioContatoPrimeiroNome" => View::firstName($UsuarioContatoNome).' ('.$UsuarioContatoSiglaDep.')',
       "UsuarioContatoEmail" => $UsuarioContatoEmail,
       "UsuarioContatoTelefone" => $UsuarioContatoTelefone,
       "UsuarioContatoSiglaDep" =>$UsuarioContatoSiglaDep,
       "UsuarioContatoSala" => $UsuarioContatoSala,

       "UsuarioSolicitanteId" => $UsuarioSolicitanteId ?? '',
       "UsuarioSolicitanteNome" => $UsuarioSolicitanteNome ?? '',
       "UsuarioSolicitantePrimeiroNome" => View::firstName($UsuarioSolicitanteNome).' ('.$UsuarioSolicitanteSiglaDep.')' ?? '',
       "UsuarioSolicitanteEmail" => $UsuarioSolicitanteEmail ?? '',
       "UsuarioSolicitanteTelefone" => $UsuarioSolicitanteTelefone ?? '',
       "UsuarioSolicitanteSiglaDep" => $UsuarioSolicitanteSiglaDep ?? '',
       "UsuarioSolicitanteSala" => $UsuarioSolicitanteSala ?? '',

       'dataAtendimento' => $obChamado->dt_atendimento ?? '-',
       'nrSolicitacao' => $obChamado->nr_solicitacao ?? '-',
       'nrRequisicao' => $obChamado->nr_requisicao ?? '-',
       'chamadoObs' => $obChamado->chamado_obs ?? '-',
       'idStatus' => $obChamado->id_status ?? '-',
       'idChamadoPai' => $obChamado->id_chamado_pai ?? '-',
       'statusdoatendimento' => EntityStatus::getStatusPorId($obChamado->id_status)->status_nm ?? '',

       'optionsBuscaTipodeOcorrencia' => $tipodeocorrenciaSelecionado,
       'optionsBuscaServico' => $servicoSelecionado,
       'optionsBuscaAtendimento' => $atendimentoSelecionado,
       'optionsBuscaUsuario' => $usuarioSelecionado,
       'optionsBuscaStatus' => $statusSelecionado,
       'optionsBuscaCriticidade' => $criticidadeSelecionado,
       'optionsBuscaUrgencia' => $urgenciaSelecionado,

       'texto_ativo' => (1 == $obChamado->id_status) ? 'Alterar Status' : 'Ativar',
       'class_ativo' => (2 == $obChamado->id_status) ? 'btn-warning' : 'btn-success',
       'style_ativo' => (1 == $obChamado->id_status) ? 'table-active' : 'table-danger',
       'andamentos' => (1 == $obChamado->id_status) ? 'table-active' : 'table-danger',

       'title' => 'Cadastrar Requisição',
       'nome' => $_SESSION['admin']['usuario']['usuario_nm'],
       'email' => $_SESSION['admin']['usuario']['email'],
       'usuario' => $_SESSION['admin']['usuario']['usuario_id'],
       'optionsUsuario' => $optionsUsuario,
       'icon' => ICON_REQUERIMENTO,
       'status' => self::getStatus($request)
     ]);

     //RETORNA A PÁGINA COMPLETA
     return parent::getPanel('Cadastrar Chamado - EMERJ',$content,'chamados',$currentDepartamento,$currentPerfil);


   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoRequerimento($request,$id_requerimento){

      //PÁGINA ATUAL
       $queryParams = $request->getQueryParams();
       $paginaAtual = $queryParams['pagina'] ?? 1;

      //echo "<pre>"; print_r($request); echo "<pre>";
      $emailusuario = filter_input(INPUT_POST, 'email_usuario', FILTER_SANITIZE_STRING) ?? '';
      $emailatendimento = filter_input(INPUT_POST, 'email_atendimento', FILTER_SANITIZE_STRING) ?? '';
      $requerimento_nm = filter_input(INPUT_POST, 'requerimento_nm', FILTER_SANITIZE_STRING) ?? '';

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id_chamado = filter_input(INPUT_POST, 'chamado', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $atendimento = filter_input(INPUT_POST, 'atendimento', FILTER_SANITIZE_NUMBER_INT) ?? '';

      $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING) ?? '';
      $usuario_atendente = filter_input(INPUT_POST, 'atendente', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $usuario_atendido = filter_input(INPUT_POST, 'usuario-atendido', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $usuario_autorizador = filter_input(INPUT_POST, 'usuario-autorizador', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $urgencia = filter_input(INPUT_POST, 'urgencia', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $criticidade = filter_input(INPUT_POST, 'criticidade', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $tipodeocorrencia = filter_input(INPUT_POST, 'tipodeocorrencia', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $nr_dgtec = filter_input(INPUT_POST, 'nrdgtec', FILTER_SANITIZE_STRING) ?? '';
      $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT) ?? '';

      try {
        //NOVA ISNTANCIA DE CHAMADO
        $obRequerimento = new EntityRequerimento;

        $obRequerimento->requerimento_desc = $descricao;
        $obRequerimento->nrdgtec = $nr_dgtec;
        $obRequerimento->id_chamado = $id_chamado;
        $obRequerimento->id_atendimento = $atendimento;
        $obRequerimento->id_criticidade = $criticidade;
        $obRequerimento->id_tipodeocorrencia = $tipodeocorrencia;
        $obRequerimento->id_urgencia = $urgencia;
        $obRequerimento->id_status = $status;
        $obRequerimento->id_atendente = $usuario_atendente;
        $obRequerimento->id_atendido = $usuario_atendido;
        $obRequerimento->id_autorizador = $usuario_autorizador;

        $sucessoInsert = $obRequerimento->cadastrar();

        if (!$sucessoInsert) {
          throw new \Exception(' Erro na gravação do requerimento.');
        }

          /*
        $idRequerimento = $obRequerimento->requerimento_id;
        $dataRequerimento = date("d/m/Y H:i:s");

        $corpoEmail = '';

        $assuntoEmail = 'Solicitação de atendimento criada -'.$ticket;

        //echo "<pre>"; print_r($emailcc); echo "<pre>"; exit;

        $corpoEmail = $corpoEmail.'<h1>Escola da Magistratura do Estado do Rio de Janeiro</h1>';
        $corpoEmail = $corpoEmail.'<h2>Departamento de Tecnologia de Informação - DETEC</h2><hr>';
        $corpoEmail = $corpoEmail.'<h3>Prezado(a) ANDRE RODRIGUES RIBEIRO ({NOME_USUARIO}),</h3><BR>';
        $corpoEmail = $corpoEmail.'<p>Recebemos a solicitação de número '.$ticket.'</p><BR>';
        $corpoEmail = $corpoEmail.'Data Abertura: '.$dataRequerimento.'<BR>';
        $corpoEmail = $corpoEmail.'Descrição do requerimento: '.$descricao.'<BR><BR>';
        $corpoEmail = $corpoEmail.'E-mail: '.$emailusuario.'<BR><BR>';
        $corpoEmail = $corpoEmail.'<p>Acompanhe o andamento da solicitação pela web, <a href="{{URL}}">clicando aqui</a>.</p><BR>';
        $corpoEmail = $corpoEmail.'<p>Atenciosamente,</p>';
        $corpoEmail = $corpoEmail.'<p><strong>DETEC – Departamento de Tecnologia de Informação</strong></p><BR>';
        $corpoEmail = $corpoEmail.'<p>Enviado em: '.$dataRequerimento.' pelo Portal de Serviços DETEC. Não responda a este e-mail.</p>';

        $obEmail = new Email;

        $email = 'a.tangy@gmail.com';
        $emailcc = 'andrerribeiro@tjrj.jus.br';

        $dirTemp = __DIR__.'/../../../files/requerimentos/tmp';
        $dirRequerimento = __DIR__.'/../../../files/requerimentos/'.$idRequerimento;

        $whereArq = ' id_sessao = "'.$_SESSION['admin']['usuario']['filesrequerimento'].'"';

        $results = EntityArquivo::getArquivos($whereArq,'arquivo_id ASC');

        while($obArquivo = $results->fetchObject(EntityArquivo::class)){
          if(!is_dir($dirRequerimento)){
            mkdir($dirRequerimento,0777);
          }
          $nm = $obArquivo->arquivo_temp;
          rename($dirTemp.'/'.$nm, $dirRequerimento.'/'.$nm);
          $obArquivo->id_requerimento = $idRequerimento;
          $obArquivo->atualizar();
        }

        if(isset($_SESSION['admin']['usuario']['filesrequerimento'])){
            unset($_SESSION['admin']['usuario']['filesrequerimento']);
            session_start();
            $_SESSION['admin']['usuario']['filesrequerimento'] = uniqid();
        } else {
          session_start();
          $_SESSION['admin']['usuario']['filesrequerimento'] = uniqid();
        }



        $sucessoEmail = $obEmail->sendEmail($email,$assuntoEmail,$corpoEmail,$emailcc);

        if(!$sucessoEmail){
          throw new \Exception(' Falha no envio do e-mail. '.$obEmail->getError());
        }

          */

      } catch (\Exception $e) {

        $strmsn = $e->getMessage();

      } finally {

        $request->getRouter()->redirect('/admin/chamados?pagina='.$paginaAtual.'&status=gravado&nm='.$nome.'&strMsn='.$strmsn.'&acao=alter');

      }
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
        //VERIFICA SE JÁ EXISTE O REQUERIMENTO com mesmo nome e tipo CADASTRADO NO BANCO
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
         return parent::getPanel('Exclir REQUERIMENTO',$content,'requerimentos');
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
