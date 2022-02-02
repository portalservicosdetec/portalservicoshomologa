<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Chamado as EntityChamado;
use \App\Model\Entity\Servico as EntityServico;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Model\Entity\Andamento as EntityAndamento;
use \App\Model\Entity\Itensconf as EntityItensconf;
use \App\Model\Entity\Departamento as EntityDepartamento;
use \App\Model\Entity\Usuario as EntityUsuario;
use \App\Model\Entity\Tipodeservico as EntityTipodeservico;
use \App\Model\Entity\Tipodeocorrencia as EntityTipodeocorrencia;
use \App\Model\Entity\Tipodeic as EntityTipodeic;
use \App\Model\Entity\Status as EntityStatus;
use \App\Model\Entity\Localizacao as EntityLocalizacao;
use \App\Model\Entity\Arquivo as EntityArquivo;
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
use \App\File\Upload;
use \App\Db\Pagination;
use \App\Communication\Email;


const DIR_CHAMADO = 'chamado';
const ROTA_CHAMADO = 'chamados';
const ICON_CHAMADO = 'telephone-inbound';
const TITLE_CHAMADO = 'Chamados';
const TITLELOW_CHAMADO = 'o chamado';

class Chamados extends Page{


  /**
   * Método responsável pela renderização da view de listagem de Chamados
   * @param Request $request
   * @return string
   */
  public static function getJsonUsuariosPorID($request){

    $id = '';
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    $objUsuario = EntityUsuario::getUsuarioPorId($id);

    //MONTA E RENDERIZA OS ITENS DE Chamado
    $itens[] = array(
      'nome_contato' => $objUsuario->usuario_nm,
      'email_contato' => $objUsuario->email,
      'departamento_id' => $objUsuario->getIDdepartamentoPaiDousuarioPorID($objUsuario->id_departamento),
      'departamento_nm' => EntityDepartamento::getDepartamentoPorId($objUsuario->id_departamento)->departamento_nm,
      'departamento_sg' => EntityDepartamento::getDepartamentoPorId($objUsuario->id_departamento)->departamento_sg,
      'localizacao_id' => $objUsuario->sala,
      'localizacao_nm' => EntityLocalizacao::getLocalizacaoPorId($objUsuario->sala)->localizacao_nm,
      'usuario_fone' => $objUsuario->usuario_fone
    );
    echo(json_encode($itens));
  }

  /**
   * Método responsável pela renderização da view de listagem de Chamados
   * @param Request $request
   * @return string
   */
  public static function getListJson($request){

    $where = '';

    $id_tipodeservico = filter_input(INPUT_GET, 'tipodeservico', FILTER_SANITIZE_STRING) ?? 1;

    $where = 'id_tipodeservico = '.$id_tipodeservico;

    $results = EntityServico::getServicos($where,'servico_nm DESC');

    //MONTA E RENDERIZA OS ITENS DE Chamado
    while($obServico = $results->fetchObject(EntityServico::class)){
      $itens[] = array(
        'id' => $obServico->servico_id,
        'nome_do_servico' => $obServico->servico_nm);
      }
	     echo(json_encode($itens));
     }

   /**
    * Método responsável pela renderização da view de listagem de Chamados
    * @param Request $request
    * @return string
    */
   public static function getListJsonCategoria($request){

     $where = '';
     $id_categoria_ic = filter_input(INPUT_GET, 'categoriadeic', FILTER_SANITIZE_NUMBER_INT) ?? 1;
     $where = 'id_categoria_ic = '.$id_categoria_ic;
     $results = EntityTipodeic::getTipodeics($where,'tipodeic_nm ASC');

     //MONTA E RENDERIZA OS ITENS DE Chamado
     while($obTipodeic = $results->fetchObject(EntityTipodeic::class)){
       $itens[] = array(
         'id' => $obTipodeic->tipodeic_id,
         'tipodeic_nm' => $obTipodeic->tipodeic_nm);
       }
 	     echo json_encode($itens);
       exit;
      }

    /**
     * Método responsável pela renderização da view de listagem de Chamados
     * @param Request $request
     * @return string
     */
    public static function getListJsonServico($request){

      //FILTRAR FUTURAMENTO TAMBÉM PELO DEPARTAMENTO DO CHAMADO!!!!!!!!!!!!

      $where = '';
      $id_tipodeic = filter_input(INPUT_GET, 'tipodeic', FILTER_SANITIZE_NUMBER_INT) ?? 1;
      $where = 'id_tipodeic = '.$id_tipodeic;
      $results = EntityAtendimento::getAtendimentos($where,'atendimento_id ASC');

      //MONTA E RENDERIZA OS ITENS DE Chamado
      while($obAtendimento = $results->fetchObject(EntityAtendimento::class)){
        $itens[] = array(
          'id' => $obAtendimento->id_servico,
          'servico_nm' => EntityServico::getServicoPorId($obAtendimento->id_servico)->servico_nm.' - ('.EntityTipodeservico::getTipodeservicoPorId(EntityServico::getServicoPorId($obAtendimento->id_servico)->id_tipodeservico)->tipodeservico_nm.')');
        }
        echo json_encode($itens);
        exit;
       }

       /**
       * Método responsável pela renderização da view de listagem de Chamados
       * @param Request $request
       * @return string
       */
      public static function getListJsonIc($request){

        //FILTRAR FUTURAMENTO TAMBÉM PELO DEPARTAMENTO DO CHAMADO!!!!!!!!!!!!
        $where = '';
        $str_where_sala = '';
        $id_tipodeic = filter_input(INPUT_GET, 'tipodeic', FILTER_SANITIZE_NUMBER_INT) ?? 0;
        $id_servico = filter_input(INPUT_GET, 'servico', FILTER_SANITIZE_NUMBER_INT) ?? 0;
        $id_departamentoAtendido = filter_input(INPUT_GET, 'departatendido', FILTER_SANITIZE_NUMBER_INT) ?? 0;
        $id_departamento = filter_input(INPUT_GET, 'departamento', FILTER_SANITIZE_NUMBER_INT) ?? 0;
        $id_sala = filter_input(INPUT_GET, 'sala', FILTER_SANITIZE_NUMBER_INT) ?? 0;
        if ($id_sala > 0){
          $str_where_sala = ' AND ((tb_itemdeconfiguracao.id_localizacao='.$id_sala.') or (tb_itemdeconfiguracao.id_localizacao=0) or (tb_itemdeconfiguracao.id_departamento='.$id_departamentoAtendido.'))' ?? '';
        }

        $itensCheckbox = '';
        $where = 'tb_atendimento.id_departamento='.$id_departamento.' AND tb_atendimento.id_tipodeic='.$id_tipodeic.' AND tb_atendimento.id_tipodeic=tb_itemdeconfiguracao.id_tipodeic  AND tb_atendimento.id_servico='.$id_servico.$str_where_sala;

        $resultsCheckbox = EntityAtendimento::getAtendimentos2($where,' itemdeconfiguracao_nm ','tb_itemdeconfiguracao, tb_atendimento',null,' DISTINCT itemdeconfiguracao_id, itemdeconfiguracao_nm, dgtec_nr, patrimonio_nr ');

        //MONTA E RENDERIZA OS ITENS DE Chamado
        while($obItensconf = $resultsCheckbox->fetchObject(EntityAtendimento::class)){
          $itens[] = array(
            'id' => $obItensconf->itemdeconfiguracao_id,
            'titulo' => (strlen($obItensconf->dgtec_nr) > 0) ? 'Patrimônio | Item' : '',
            'descricao' => (strlen($obItensconf->dgtec_nr) > 0) ? 'Item: '.$obItensconf->dgtec_nr. ' | Patrimônio: '.$obItensconf->patrimonio_nr : '',
            'itemdeconf_nm' => $obItensconf->itemdeconfiguracao_nm);
          }
          echo json_encode($itens);
          exit;
         }


         /**
         * Método responsável pela exclusão dos arquivos temporários de upload dos Chamados
         * @param Request $request
         * @return string
         */
         public static function setRemoveUploadAjax($request){

           $id_arquivo = filter_input(INPUT_POST, 'id_arquivo', FILTER_SANITIZE_NUMBER_INT) ?? 0;

           $obArquivo = EntityArquivo::getArquivoPorId($id_arquivo);

           if(!$obArquivo instanceof EntityArquivo){
             echo "Arquivo não existe no banco.";
           }
           $arquivo = __DIR__.'/../../../files/chamados/tmp/'.$obArquivo->arquivo_temp;
           //EXCLUI O USUÁRIO
           $obArquivo->excluir();
           //REDIRECIONA O USUÁRIO
           if(file_exists($arquivo)){
              unlink($arquivo);
            } else {
              echo "Arquivo '.$arquivo.' não existe no diretório.";
            }
         }


       /**
       * Método responsável pela renderização da view de listagem de Chamados
       * @param Request $request
       * @return string
       */
       public static function setUploadAjax($request){

         $strMsnUpload = '';
         $strMsnUploadSucess = '';
         $strMsnUploadFail = '';
         $strMsnUploadError = '';
         if(isset($_FILES['arquivo'])){
           //INSTÂNCIAS DO UPLOAD
           $uploads = Upload::createMultiUpload($_FILES['arquivo']);
           foreach ($uploads as $obUpload) {

             $strIcon = '';
             $strNome = $obUpload->name ?? '';
             $strSize = $obUpload->size ?? '';
             $strExtensao = strtolower($obUpload->extension) ?? '';

             //GERA UM NOME ALEATÓRIO PARA O ARQUIVO
             $obUpload->generateNewName();

             $allowed = array('jpg','jepg','png','gif','doc','docx','xls','xlsx','cvs','txt','pdf','ppt','pptx');
             if(!in_array($strExtensao,$allowed)) {
               $strMsnUploadFail = $strMsnUploadFail.'
                 <div class="alert mb-0 pr-2 pl-3 pt-2 pb-2 alert-warning alert-dismissible fade show" role="alert">
                   <i class="bi-exclamation-octagon" style="font-size: 1.5rem; margin-right: 8px; vertical-align: bootom; padding-bootom: 0px; color: red;"></i>
                    Problemas ao enviar o arquivo <strong>'.$strNome.'.'.$strExtensao.'</strong>. Extensão <strong>'.strtoupper($strExtensao).'</strong> não permitida!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                   </button>
                 </div>';
             } else {
               $allowedIMG = array('jpg','jepg','png','gif');
                if(in_array($strExtensao,$allowedIMG)) {
                  $strIcon = 'file-image';
               } elseif ($strExtensao == 'doc' || $strExtensao == 'docx'){
                 $strIcon = 'file-earmark-word';
               } elseif ($strExtensao == 'xls' || $strExtensao == 'xlsx'){
                 $strIcon = 'file-earmark-excel';
               } elseif ($strExtensao == 'xls' || $strExtensao == 'xlsx' || $strExtensao == 'cvs'){
                 $strIcon = 'file-earmark-excel';
               } elseif ($strExtensao == 'ppt' || $strExtensao == 'pptx'){
                 $strIcon = 'file-ppt';
               } elseif ($strExtensao == 'pdf'){
                 $strIcon = 'file-earmark-pdf';
               } elseif ($strExtensao == 'txt'){
                 $strIcon = 'file-earmark-font';
               }
               $strErro = $obUpload->error ?? '';

               //MOVE OS ARQUIVOS DE UPLOAD
               $sucesso = $obUpload->upload(__DIR__.'/../../../files/chamados/tmp',false);

               if($sucesso){

                 //NOVA ISNTANCIA DE CHAMADO
                 $obArquivo = new EntityArquivo;
                 ////$obChamado::getChamadoPorEmail($posVars['email']);
                 $obArquivo->arquivo_nm = $strNome;
                 $obArquivo->arquivo_temp = $obUpload->getBasename();
                 $obArquivo->arquivo_tam = $strSize;
                 $obArquivo->arquivo_type = $strExtensao;
                 $obArquivo->arquivo_icon = $strIcon;
                 $obArquivo->id_usuario = $_SESSION['admin']['usuario']['usuario_id'];
                 $obArquivo->id_sessao = $_SESSION['admin']['usuario']['fileschamado'];
                 $obArquivo->cadastrar();

                 $strMsnUploadSucess = $strMsnUploadSucess.'
                        <div class="p-1 bg-light border" id="upload_arquivo_'.$obArquivo->arquivo_id.'">
                          <i class="bi-'.$strIcon.'" style="font-size: 1.5rem; margin: 8px; vertical-align: bootom; padding-bootom: 0px; color: cornflowerblue;"></i>
                          Arquivo <strong>'.$strNome.'.'.$strExtensao.'</strong> carregado com sucesso!
                          <a tabindex="0" id="'.$obArquivo->arquivo_id.'" onclick="file_remove('.$obArquivo->arquivo_id.');" class="btn btn-sm btn-danger float-sm-right role="button" title="Id='.$obArquivo->arquivo_id.'Clique para remover o arquivo '.$strNome.'.'.$strExtensao.' da sua lista de upload.">X</a>
                        </div>';

                 continue;
               }else{
                 $strMsnUploadError = $strMsnUploadError.'Problemas ao enviar o(s) arquivo(s) <strong>Erro: '.$strErro.'<br>';
               }
               echo $strMsnUploadError;
               exit;
             }
           }
           echo $strMsnUploadSucess.$strMsnUploadFail;
         }
       }

  /**
   * Método responsável pela renderização da view de listagem de Chamados
   * @param Request $request
   * @return string
   */
  public static function getListChamados($request,$errorMessage = null){

    $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
    $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];
    $idUsuarioLogado = $_SESSION['admin']['usuario']['usuario_id'];

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

    $busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
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

    $itemdeconfiguracaoSelecionado = AdminItensconfs::getItensconfItensSelect($request,$id_itemdeconfiguracao);
    $tipoDeServicoSelecionado = AdminTipodeServico::getTipodeservicoItensSelect($request,$id_tipodeservico);
    $tipodeicSelecionado = AdminTipodeic::getTipodeicItensSelect($request,$id_tipodeic);
    $categoriadeicSelecionado = AdminCategoriadeics::getCategoriadeicItensRadio($request,$id_categoria_ic);
    $servicoSelecionado = AdminServico::getServicoItensSelect($request,$id_servico);
    $departamentoSelecionado = PagesDepartamento::getDepartamentoItensSelect($request,$id_departamento);
    $usuarioSelecionado = AdminUsuario::getUsuarioItensSelect($request,$id_usuario);
    $atendimentoSelecionado  = AdminAtendimento::getAtendimentoItensSelect($request,$id_atendimento);
    $statusSelecionado = AdminStatus::getStatusItensSelect($request,$id_status);
    $tipodeocorrenciaSelecionado = AdminTipodeocorrencia::getTipodeocorrenciaItensSelect($request,$id_tipodeocorrencia);

    $criticidadeSelecionado = AdminCriticidade::getCriticidadeItensSelect($request,$id_criticidade);
    $urgenciaSelecionado = AdminUrgencia::getUrgenciaItensSelect($request,$id_urgencia);

    //PÁGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['pagina'] ?? 1;

    //CONTEÚDO DA HOME
    $content = View::render('admin/modules/chamado/index',[
      'icon' => ICON_CHAMADO,
      'title' =>TITLE_CHAMADO,
      'titlelow' => TITLELOW_CHAMADO,
      'direntity' => ROTA_CHAMADO,
      'itens' => self::getChamadoItens($request,$obPagination),
      'pagination' => parent::getPagination($request,$obPagination),
      'status' => self::getStatus($request),
      'idUsuarioLogado' => $idUsuarioLogado,
      //'optionsBuscaTipoDeServico' => $tipoDeServicoSelecionado,
      //'optionsBuscaTipodeic' => $tipodeicSelecionado,
      'optionsBuscaTipodeOcorrencia' => $tipodeocorrenciaSelecionado,
      'optionsBuscaServico' => $servicoSelecionado,
      'optionsBuscaAtendimento' => $atendimentoSelecionado,
      'optionsBuscaUsuario' => $usuarioSelecionado,
      'optionsBuscaStatus' => $statusSelecionado,
      'optionsBuscaCriticidade' => $criticidadeSelecionado,
      'optionsBuscaUrgencia' => $urgenciaSelecionado,
      //'optionsBuscaUsuario' => $usuarioSelecionado,
      'paginaAtual' => $paginaAtual,
      'busca' => $busca,
      'uri' => strstr(".$_SERVER[REQUEST_URI].", '?')
    ]);

    //RETORNA A PÁGINA COMPLETA
    return parent::getPanel('Chamados - EMERJ',$content,'chamados',$currentDepartamento,$currentPerfil);
  }

  /**
   * Método responsável por obter a renderização dos itens de Chamados para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
  private static function getChamadoItens($request,&$obPagination){

    $itens = '';
    $UsuarioSolicitanteNome = null;

    //OBTEM OS GETS DA URL
    $busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
    $servico = filter_input(INPUT_GET, 'servico', FILTER_SANITIZE_STRING);
    //MONTA AS CONDICÕES DA BUSCA
    $condicoes = [
      strlen($busca) ? 'chamado_nm LIKE "%'.str_replace(' ','%',$busca).'%"' : null,
      strlen($servico) ? 'id_servico = '.$servico : null
    ];

    $condicoes = array_filter($condicoes);
    $where = implode(' AND ',$condicoes);

    //QUANTIDADE TOTAL DE REGISTROS
    $qtTotal = EntityChamado::getChamados($where,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

    //ULTIMO CHAMADO
    //$ultimoChamado = EntityChamado::nrSolicitacao();

    //PÁGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['pagina'] ?? 1;
    $pag = '?pagina='.$paginaAtual;

    //CAMINHO ATUAL
    $uri=strstr("$_SERVER[REQUEST_URI]", '?');
    if($uri == ''){
      $uri = $pag;
    }

    //INSTÂNCIA DE PAGINAÇÃO
    $obPagination = new Pagination($qtTotal,$paginaAtual,100);

    $strEditModal = View::render('admin/modules/'.DIR_CHAMADO.'/editmodal',[]);
    $strAddModal = View::render('admin/modules/'.DIR_CHAMADO.'/addmodal',[]);
    $strAtivaModal = View::render('admin/modules/'.DIR_CHAMADO.'/ativamodal',[]);
    $strDeleteModal = View::render('admin/modules/'.DIR_CHAMADO.'/deletemodal',[]);
    $strDetailModal = View::render('admin/modules/'.DIR_CHAMADO.'/detailmodal',[]);
    $strAddRequerimentoModal = View::render('admin/modules/'.DIR_CHAMADO.'/addrequerimentomodal',[]);

    //RESULTADO DA PAGINA
    $results = EntityChamado::getChamados($where,'chamado_id DESC',$obPagination->getLimit());

    //MONTA E RENDERIZA OS ITENS DE Chamado
    while($obChamado = $results->fetchObject(EntityChamado::class)){

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

      if ($obChamado->solicitado_por > 0) {
        $obUsuarioSolicitante = EntityUsuario::getUsuarioPorId($obChamado->solicitado_por);
        if($obUsuarioSolicitante instanceof EntityUsuario){
          $UsuarioSolicitanteId = $obUsuarioSolicitante->usuario_id;
          $UsuarioSolicitanteNome = $obUsuarioSolicitante->usuario_nm;
          $UsuarioSolicitanteEmail = $obUsuarioSolicitante->email;
          $UsuarioSolicitanteTelefone = $obUsuarioSolicitante->usuario_fone;
          $UsuarioSolicitanteSiglaDep = EntityDepartamento::getDepartamentoPorId($obUsuarioSolicitante->id_departamento)->departamento_sg;
          $UsuarioSolicitanteSala = EntityLocalizacao::getLocalizacaoPorId($obUsuarioSolicitante->sala)->localizacao_nm;
        }
      }

      if ($obChamado->aberto_para > 0) {
        $obUsuarioAtendido = EntityUsuario::getUsuarioPorId($obChamado->aberto_para) ?? '';
        if($obUsuarioAtendido instanceof EntityUsuario){
          $UsuarioAtendidoId = $obUsuarioAtendido->usuario_id;
          $UsuarioAtendidoNome = $obUsuarioAtendido->usuario_nm;
          $UsuarioAtendidoEmail = $obUsuarioAtendido->email;
          $UsuarioAtendidoTelefone = $obUsuarioAtendido->usuario_fone;
          $UsuarioAtendidoSiglaDep = EntityDepartamento::getDepartamentoPorId($obUsuarioAtendido->id_departamento)->departamento_sg;
          $UsuarioAtendidoSala = EntityLocalizacao::getLocalizacaoPorId($obUsuarioAtendido->sala)->localizacao_nm;
        }
      }

      if ($obChamado->atendido_por > 0) {
        $obUsuarioAtendente = EntityUsuario::getUsuarioPorId($obChamado->atendido_por) ?? '';
        if($obUsuarioAtendente instanceof EntityUsuario){
          $UsuarioAtendenteId = $obUsuarioAtendente->usuario_id ?? '-';
          $UsuarioAtendenteNome = $obUsuarioAtendente->usuario_nm ?? '-';
          $UsuarioAtendenteEmail = $obUsuarioAtendente->email ?? '-';
          $UsuarioAtendenteTelefone = $obUsuarioAtendente->usuario_fone ?? '-';
          $UsuarioAtendenteSiglaDep = EntityDepartamento::getDepartamentoPorId($obUsuarioAtendente->id_departamento)->departamento_sg ?? '-';
          $UsuarioAtendenteSala = EntityLocalizacao::getLocalizacaoPorId($obUsuarioAtendente->sala)->localizacao_nm ?? '-';
        }
      }

      $itens .= View::render('admin/modules/chamado/item',[
        'icon' => ICON_CHAMADO,
        'title' =>TITLE_CHAMADO,
        'titlelow' => TITLELOW_CHAMADO,
        'direntity' => ROTA_CHAMADO,
        'id' => $obChamado->chamado_id,
        'ticket' => $obChamado->nr_solicitacao,
        'titulo' => $obChamado->chamado_nm,
        'descricao' => $obChamado->chamado_des,
        'data_abertura' => $obChamado->data_add,

        "UsuarioContatoId" => $UsuarioContatoId ?? '-',
        "UsuarioContatoNome" => $UsuarioContatoNome ?? '-',
        "UsuarioContatoEmail" => $UsuarioContatoEmail ?? '-',
        "UsuarioContatoTelefone" => $UsuarioContatoTelefone ?? '-',
        "UsuarioContatoSiglaDep" =>$UsuarioContatoSiglaDep ?? '-',
        "UsuarioContatoSala" => $UsuarioContatoSala ?? '-',

        "UsuarioSolicitanteId" => $UsuarioSolicitanteId ?? '-',
        "UsuarioSolicitanteNome" => $UsuarioSolicitanteNome ?? '-',
        "UsuarioSolicitanteEmail" => $UsuarioSolicitanteEmail ?? '-',
        "UsuarioSolicitanteTelefone" => $UsuarioSolicitanteTelefone ?? '-',
        "UsuarioSolicitanteSiglaDep" => $UsuarioSolicitanteSiglaDep ?? '-',
        "UsuarioSolicitanteSala" => $UsuarioSolicitanteSala ?? '-',

        "UsuarioAtendidoId" => $UsuarioAtendidoId ?? '-',
        "UsuarioAtendidoNome" => $UsuarioAtendidoNome ?? '-',
        "UsuarioAtendidoEmail" => $UsuarioAtendidoEmail ?? '-',
        "UsuarioAtendidoTelefone" => $UsuarioAtendidoTelefone ?? '-',
        "UsuarioAtendidoSiglaDep" => $UsuarioAtendidoSiglaDep ?? '-',
        "UsuarioAtendidoSala" => $UsuarioAtendidoSala ?? '-',

        "Requisitante" => (($UsuarioAtendidoNome ?? $UsuarioSolicitanteNome) ?? $UsuarioContatoNome) ?? '-',

        'dataAtendimento' => $obChamado->dt_atendimento ?? '-',
        'nrSolicitacao' => $obChamado->nr_solicitacao ?? '-',
        'nrRequisicao' => $obChamado->nr_requisicao ?? '-',
        'chamadoObs' => $obChamado->chamado_obs ?? '-',
        'idStatus' => $obChamado->id_status ?? '-',
        'idChamadoPai' => $obChamado->id_chamado_pai ?? '-',
        'statusdoatendimento' => EntityStatus::getStatusPorId($obChamado->id_status)->status_nm ?? '',

        'texto_ativo' => (1 == $obChamado->id_status) ? 'Alterar Status' : 'Ativar',
        'class_ativo' => (2 == $obChamado->id_status) ? 'btn-warning' : 'btn-success',
        'style_ativo' => (1 == $obChamado->id_status) ? 'table-active' : 'table-danger',
        'andamentos' => (1 == $obChamado->id_status) ? 'table-active' : 'table-danger',
        'paginaAtual' => $paginaAtual,
        'uri' => $uri
      ]);

    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    $itens .= $strDetailModal;
    $itens .= $strAddRequerimentoModal;
    return $itens;
  }


  /**
   * Método responsável por retornar o formulário de cadastro de um novo item de configuração
   * @param Request $request
   * @param integer $id
   * @return string
   */
   public static function getDetailChamado($request,$id){

     $obChamado = EntityChamado::getChamadoPorId($id);

     //OBTÉM OS ANDAMENTOS DO CHAMADO
     $obAndamento = EntityAndamento::getAndamentosPorChamado($id);

     if(!$obChamado instanceof EntityChamado){
       $request->getRouter()->redirect('/admin/chamados?status=updatefail');
     }

     $obUsuarioContato = EntityUsuario::getUsuarioPorId($obChamado->id_usuario) ?? '';
     $obUsuarioSolicitante = EntityUsuario::getUsuarioPorId($obChamado->solicitado_por) ?? '';
     $obUsuarioAtendido = EntityUsuario::getUsuarioPorId($obChamado->aberto_para) ?? '';

     //CONTEÚDO DO FORMULÁRIO
     $content = View::render('admin/modules/chamado/item',[
       'title' => 'Detalhes do Chamado',
       'id' => $obChamado->nr_solicitacao,
       'titulo' => $obChamado->chamado_nm,
       'descricao' => $obChamado->chamado_des,
       'data_abertuta' => $obChamado->data_add,
       'aberto_por' => $obUsuarioContato->usuario_nm,
       'id_aberto_por' => $obUsuarioContato->usuario_id,
       'dep_aberto_por' => EntityDepartamento::getDepartamentoPorId($obUsuarioContato->id_departamento)->departamento_sg ?? '-',
       'solicitado_por' => $obUsuarioSolicitante->usuario_nm ?? '-',
       'id_solicitado_por' => $obUsuarioSolicitante->usuario_id ?? '-',
       'dep_solicitado_por' => EntityDepartamento::getDepartamentoPorId($obUsuarioSolicitante->id_departamento)->departamento_sg ?? '-',
       'aberto_para' => $obUsuarioAtendido->usuario_nm ?? '-',
       'id_aberto_para' => $obUsuarioAtendido->usuario_id ?? '-',
       'dep_aberto_para' => EntityDepartamento::getDepartamentoPorId($obUsuarioAtendido->id_departamento)->departamento_sg ?? '-',
       'data_atendimento' => $obChamado->dt_atendimento ?? '-',
       'nr_solicitacao' => $obChamado->nr_solicitacao ?? '-',
       'nr_requisicao' => $obChamado->nr_requisicao ?? '-',
       'chamado_obs' => $obChamado->chamado_obs ?? '-',
       'id_status' => $obChamado->id_status ?? '-',
       'id_chamado_pai' => $obChamado->id_chamado_pai ?? '-',
       'statusdoatendimento' => EntityStatus::getStatusPorId($obChamado->id_status)->status_nm ?? '',
       'status' => self::getStatus($request),
       'uri' => $uri ?? ''
     ]);

     //RETORNA A PÁGINA COMPLETA DO CHAMADO
     return parent::getPanel('Detalhes do chamado',$content,'chamados');
   }



  /**
   * Método responsável por retornar o formulário de cadastro de um novo Chamado
   * @param Request $request
   * @return string
   */
   public static function getNovoChamado($request){

     $currentIDDepartamentoPai = '';

     $departamento_id = $_SESSION['admin']['usuario']['id_departamento'];
     $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
     $currentIDDepartamentoPai = $_SESSION['admin']['usuario']['id_departamento_pai'];
     $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];
     $currentRamal = $_SESSION['admin']['usuario']['ramal'];

     $optionsUsuario = AdminUsuario::getUsuarioItensSelectEmail($request,null);
     $optionsServico = AdminServico::getServicoItensSelect($request,null);
     $optionsTipodeic = AdminTipodeic::getTipodeicItensSelect($request,null);
     $categoriadeicSelecionado = AdminCategoriadeics::getCategoriadeicItensRadio($request,null);

     //CONTEÚDO DO FORMULÁRIO
     $content = View::render('admin/modules/chamado/form',[
       'title' => 'Cadastrar Chamado',
       'nome' => $_SESSION['admin']['usuario']['usuario_nm'],
       'email' => $_SESSION['admin']['usuario']['email'],
       'usuario' => $_SESSION['admin']['usuario']['usuario_id'],
       'usuariodepartamento' => EntityDepartamento::getDepartamentoPorId($departamento_id)->departamento_sg,
       'departamento_id' => $currentIDDepartamentoPai,
       'salaid' => $_SESSION['admin']['usuario']['sala'] ?? '',
       'sala' => EntityLocalizacao::getLocalizacaoPorId($_SESSION['admin']['usuario']['sala'])->localizacao_nm ?? '',
       'ramal' => $currentRamal,
       'optionsUsuario' => $optionsUsuario,
       'optionsServico' => $optionsServico,
       'optionsTipodeic' => $optionsTipodeic,
       'optionsCategoriadeic' => $categoriadeicSelecionado,
       'status' => self::getStatus($request)
     ]);

     $uri = '?token=abrirchamado';

     //RETORNA A PÁGINA COMPLETA
     return parent::getPanel('Cadastrar Chamado - EMERJ',$content,'chamados',$currentDepartamento,$currentPerfil);
   }

   /**
    * Método responsável por cadastro de um novo item de configuração no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoChamado($request){

      //PÁGINA ATUAL
       $queryParams = $request->getQueryParams();
       $paginaAtual = $queryParams['pagina'] ?? 1;

      //echo "<pre>"; print_r($request); echo "<pre>";
      $emailusuario = filter_input(INPUT_POST, 'email_usuario', FILTER_SANITIZE_STRING) ?? '';
      $emailatendimento = filter_input(INPUT_POST, 'email_atendimento', FILTER_SANITIZE_STRING) ?? '';
      $chamado_nm = filter_input(INPUT_POST, 'chamado_nm', FILTER_SANITIZE_STRING) ?? '';
      $itens_checados = filter_input(INPUT_POST, 'itens_checados', FILTER_SANITIZE_STRING) ?? '';
      $nome_chamado = $chamado_nm.' itens do atendimento: '.$itens_checados;
      $nome = $nome_chamado;

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id_servico = filter_input(INPUT_POST, 'servico', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $id_departamento = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_NUMBER_INT) ?? '';

      $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING) ?? '';
      $chamado_obs = filter_input(INPUT_POST, 'chamado_obs', FILTER_SANITIZE_STRING) ?? '';
      $tipodeic = filter_input(INPUT_POST, 'tipodeic', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_NUMBER_INT) ?? '';
      $solicitado_por = filter_input(INPUT_POST, 'id_contato', FILTER_SANITIZE_NUMBER_INT) ?? null;
      $aberto_para = filter_input(INPUT_POST, 'id_atendimento', FILTER_SANITIZE_NUMBER_INT) ?? null;

      try {
        //NOVA ISNTANCIA DE CHAMADO
        $obChamado = new EntityChamado;
        $ticket = $obChamado::nrSolicitacao()[1];

        if (!$ticket) {
          throw new \Exception('Falha na criação do Ticket.');
        }

        $obChamado->id_usuario = $id_usuario;
        $obChamado->solicitado_por = $solicitado_por;
        $obChamado->chamado_nm = $nome;
        $obChamado->chamado_des = $descricao;
        $obChamado->aberto_para = $aberto_para;
        $obChamado->chamado_obs = $chamado_obs;
        $obChamado->nr_solicitacao = $ticket;
        $obChamado->id_status = 1;

        $sucessoInsert = $obChamado->cadastrar();

        if (!$sucessoInsert) {
          throw new \Exception(' Erro na gravação do chamado.');
        }
        $idChamado = $obChamado->chamado_id;
        $dataChamado = date("d/m/Y H:i:s");

        $corpoEmail = '';

        $assuntoEmail = 'Solicitação de atendimento criada -'.$ticket;

        //echo "<pre>"; print_r($emailcc); echo "<pre>"; exit;

        $corpoEmail = $corpoEmail.'<h1>Escola da Magistratura do Estado do Rio de Janeiro</h1>';
        $corpoEmail = $corpoEmail.'<h2>Departamento de Tecnologia de Informação - DETEC</h2><hr>';
        $corpoEmail = $corpoEmail.'<h3>Prezado(a) ANDRE RODRIGUES RIBEIRO ({NOME_USUARIO}),</h3><BR>';
        $corpoEmail = $corpoEmail.'<p>Recebemos a solicitação de número '.$ticket.'</p><BR>';
        $corpoEmail = $corpoEmail.'Data Abertura: '.$dataChamado.'<BR>';
        $corpoEmail = $corpoEmail.'Descrição do chamado: '.$descricao.'<BR><BR>';
        $corpoEmail = $corpoEmail.'E-mail: '.$emailusuario.'<BR><BR>';
        $corpoEmail = $corpoEmail.'<p>Acompanhe o andamento da solicitação pela web, <a href="{{URL}}">clicando aqui</a>.</p><BR>';
        $corpoEmail = $corpoEmail.'<p>Atenciosamente,</p>';
        $corpoEmail = $corpoEmail.'<p><strong>DETEC – Departamento de Tecnologia de Informação</strong></p><BR>';
        $corpoEmail = $corpoEmail.'<p>Enviado em: '.$dataChamado.' pelo Portal de Serviços DETEC. Não responda a este e-mail.</p>';

        $obEmail = new Email;

        $email = 'a.tangy@gmail.com';
        $emailcc = 'andrerribeiro@tjrj.jus.br';




        $dirTemp = __DIR__.'/../../../files/chamados/tmp';
        $dirChamado = __DIR__.'/../../../files/chamados/'.$idChamado;

        $whereArq = ' id_sessao = "'.$_SESSION['admin']['usuario']['fileschamado'].'"';



        $results = EntityArquivo::getArquivos($whereArq,'arquivo_id ASC');

        while($obArquivo = $results->fetchObject(EntityArquivo::class)){
          if(!is_dir($dirChamado)){
            mkdir($dirChamado,0777);
          }
          $nm = $obArquivo->arquivo_temp;
          rename($dirTemp.'/'.$nm, $dirChamado.'/'.$nm);
          $obArquivo->id_chamado = $idChamado;
          $obArquivo->atualizar();
        }

        if(isset($_SESSION['admin']['usuario']['fileschamado'])){
            unset($_SESSION['admin']['usuario']['fileschamado']);
            session_start();
            $_SESSION['admin']['usuario']['fileschamado'] = uniqid();
        } else {
          session_start();
          $_SESSION['admin']['usuario']['fileschamado'] = uniqid();
        }

        $sucessoEmail = $obEmail->sendEmail($email,$assuntoEmail,$corpoEmail,$emailcc);

        if(!$sucessoEmail){
          throw new \Exception(' Falha no envio do e-mail. '.$obEmail->getError());
        }

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
     public static function getEditChamado($request,$id){


       $chamadoObs = '';


       $obChamado = EntityChamado::getChamadoPorId($id);

       //echo "<pre>"; print_r($obChamado->id_servico); echo "<pre>"; exit;
       //OBTÉM O USUÁRIO DO BANCO DE DADOS
       $obAndamento = EntityAndamento::getAndamentosPorChamado($id);

       //echo "<pre>"; print_r($obAndamento->id_chamado); echo "<pre>"; exit;

       if(!$obChamado instanceof EntityChamado){
         $request->getRouter()->redirect('/admin/chamados/novo?status=updatefail');
       }

       $obTipodeServico = EntityTipodeservico::getTipodeServicoDeChamado2('id_servico = servico_id AND chamado_id = id_chamado AND id_atendimento = atendimento_id AND id_chamado = '.$id,null,'tb_chamado, tb_andamento, tb_atendimento, tb_servico',null,'DISTINCT id_tipodeservico');
       $obServico = EntityServico::getServicoDeChamado2('chamado_id = id_chamado AND id_atendimento = atendimento_id AND id_chamado = '.$id,null,'tb_chamado, tb_andamento, tb_atendimento',null,'DISTINCT id_servico');
       $obItemdeconfiguracao = EntityItensconf::getItemdeconfiguracaoDeChamado2('chamado_id = id_chamado AND id_atendimento = atendimento_id AND id_chamado = '.$id,null,'tb_chamado, tb_andamento, tb_atendimento',null,'DISTINCT id_servico');
       $obDepartamento = EntityDepartamento::getDepartamentoDeChamado2('chamado_id = id_chamado AND id_atendimento = atendimento_id AND id_chamado = '.$id,null,'tb_chamado, tb_andamento, tb_atendimento',null,'DISTINCT id_departamento');

       $optionsTipoDeServico = AdminTipodeServico::getTipodeservicoItensSelect($request,$obTipodeServico->id_tipodeservico) ?? '';
       $optionsServico = AdminServico::getServicoItensSelect($request,$obServico->id_servico) ?? '';
       $optionsItemdeconfiguracao = AdminAtendimento::getAtendimentoItensCheckboxChamado($request,$id,$obServico->id_servico,$obDepartamento->id_departamento);
       $optionsDepartamento = PagesDepartamento::getDepartamentoItensSelect($request,$obDepartamento->id_departamento) ?? '';




       $obUsuarioContato = EntityUsuario::getUsuarioPorId($obChamado->solicitado_por) ?? '';

       if ($obUsuarioContato) {
          $obUsuarioDepartamentoContato = EntityDepartamento::getDepartamentoPorId($obUsuarioContato->id_departamento)->departamento_sg ?? '';
       }
       $obUsuarioAtendimento = EntityUsuario::getUsuarioPorId($obChamado->aberto_para) ?? '';
       if (!$obUsuarioAtendimento){
         $strhide = 'hide';
         $btchecked = 'unchecked';

       } else{
         $btchecked = 'checked';
         $strhide = '';
        $obUsuarioDepartamentoAtendimento = EntityDepartamento::getDepartamentoPorId($obUsuarioAtendimento->id_departamento)->departamento_sg ?? '';
       }



       //CONTEÚDO DO FORMULÁRIO
       $content = View::render('admin/modules/chamado/form',[
         'title' => 'Editar Chamado',
         'nome' => $_SESSION['admin']['usuario']['usuario_nm'],
         'email' => $_SESSION['admin']['usuario']['email'],
         'usuario' => $_SESSION['admin']['usuario']['usuario_id'],
         'usuariodepartamento' => EntityDepartamento::getDepartamentoPorId($_SESSION['admin']['usuario']['id_departamento'])->departamento_sg,
         'sala' => $_SESSION['admin']['usuario']['sala'],
         'emailcontato' => $obUsuarioContato->email ?? '',
         'nomecontato' => $obUsuarioContato->usuario_nm ?? '',
         'departamentocontato' => $obUsuarioDepartamentoContato ?? '',
         'idcontato' => $obChamado->solicitado_por ?? '',
         'salacontato' => $obUsuarioContato->sala ?? '',
         'emailatendimento' => $obUsuarioAtendimento->email ?? '',
         'nomeatendimento' => $obUsuarioAtendimento->usuario_nm ?? '',
         'departamentoatendimento' => $obUsuarioDepartamentoAtendimento ?? '',
         'idatendimento' => $obChamado->aberto_para ?? '',
         'salaatendimento' => $obUsuarioAtendimento->sala ?? '',
         'chamado_nm' => $obChamado->chamado_nm ?? '',
         'optionsTipoDeServico' => $optionsTipoDeServico,
         'optionsServico' => $optionsServico,
         'optionsDepartamento' => $optionsDepartamento,
         'optionsItemdeconfiguracao' => $optionsItemdeconfiguracao,
         'chamadoObs' => $obChamado->chamado_obs,
         'status' => self::getStatus($request),
         'uri' => $uri ?? '',
         'strhide' => $strhide ?? '',
         'btchecked' => $btchecked ?? '',
         'strhideItens' => $strhideItens ?? '',
         'chamadoObs' => $obChamado->chamado_obs,
       ]);

       //RETORNA A PÁGINA COMPLETA
       return parent::getPanel('Editar Chamado',$content,'chamados');
     }

     /**
      * Método responsável por gravar a edição de um item de configuração
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function setEditChamado($request,$id){


      //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

        //DADOS DO POST
        $posVars = $request->getPostVars();
        $servico_id = $posVars['servico_id'] ?? '';
        $itemdeconfiguracao_id = $posVars['itemdeconfiguracao_id'] ?? '';

        $where = " id_servico = ".$servico_id." AND id_itemdeconfiguracao = ".$itemdeconfiguracao_id;

        //echo "<pre>"; print_r($where); echo "<pre>"; exit;
        //VERIFICA SE JÁ EXISTE O IC com mesmo nome e tipo CADASTRADO NO BANCO
        $obChamado = EntityChamado::getChamados($where);

      //  echo "<pre>"; print_r($obChamado); echo "<pre>"; exit;
        if($obChamado instanceof EntityChamado){
          //REDIRECIONA SE O E-MAIL JÁ FOR CADASTRADO NO BANCO
          $request->getRouter()->redirect('/admin/chamados/novo?status=duplicado');
        }

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obChamado = EntityChamado::getChamadoPorId($id);

        if(!$obChamado instanceof EntityChamado){

          //echo "<pre>"; print_r($id); echo "<pre>"; exit;
          $request->getRouter()->redirect('/admin/chamados/'.$id.'/edit?status=updatefail');
        }

        //echo "<pre>"; print_r($id_servico); echo "<pre>"; exit;

        //ATUALIZA A INSTANCIA
        $obChamado->id_servico = $posVars['servico_id'];
        $obChamado->id_itemdeconfiguracao = $posVars['itemdeconfiguracao_id'];
        $obChamado->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/chamados/'.$obChamado->chamado_id.'/edit?status=alterado');
      }

      /**
       * Método responsável por retornar o formulário de alteração de status de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getAltStatusChamadoModal($request,$id){

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obChamado = EntityChamado::getChamadoPorId($id);

         //PÁGINA ATUAL
         $uri=strstr("$_SERVER[REQUEST_URI]", '?');

         if(!$obChamado instanceof EntityChamado){
           $request->getRouter()->redirect('/admin/chamados?status=updatefail');
         }

         //CONTEÚDO DO FORMULÁRIO
         $content = View::render('admin/modules/chamado/alterastatus',[
           'status' => self::getStatus($request),
           'paginaAtual' => $paginaAtual

         ]);

         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         if($obChamado->ativo_fl == 's'){
           $altStatus = 'n';
         } elseif ($obChamado->ativo_fl == 'n') {
           $altStatus = 's';
         }

         //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
         $obChamado->ativo_fl = $altStatus;
         $obChamado->atualizar();

         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/admin/chamados'.$uri.'&status=statusupdate');

       }


      /**
       * Método responsável por retornar o formulário de exclusão de um usuário
       * @param Request $request
       * @param integer $id
       * @return string
       */
       public static function getDeleteChamado($request,$id){

      //   echo "<pre>BBBBB"; print_r($id); echo "<pre>"; exit;


         //OBTÉM O USUÁRIO DO BANCO DE DADOS
         $obChamado = EntityChamado::getChamadoPorId($id);

         if(!$obChamado instanceof EntityChamado){
           $request->getRouter()->redirect('/admin/chamado');
         }

       //CONTEÚDO DA FORMULÁRIO
         $content = View::render('admin/modules/chamado/delete',[
           'nome' => $obChamado->chamado_nm,
           'descricao' => $obChamado->chamado_des,
           'chamado_id' => $obChamado->id_servico,
           'status' => self::getStatus($request)
         ]);


         //RETORNA A PÁGINA COMPLETA
         return parent::getPanel('Exclir IC',$content,'chamados');
       }

       /**
        * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function getDeleteChamadoModal($request,$id){

          //OBTÉM O USUÁRIO DO BANCO DE DADOS
          $obChamado = EntityChamado::getChamadoPorId($id);
          $strNome = $obChamado->chamado_nm;

          if(!$obChamado instanceof EntityChamado){
            $request->getRouter()->redirect('/admin/chamados');
          }

         //EXCLUI O USUÁRIO
          $obChamado->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/chamados?status=deletado&nm='.$strNome);
        }

       /**
        * Método responsável por excluir um usuário
        * @param Request $request
        * @param integer $id
        * @return string
        */
        public static function setDeleteChamado($request,$id){

        //  echo "<pre>AQUIII"; print_r($id); echo "<pre>"; exit;

          //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
          $obChamado = EntityChamado::getChamadoPorId($id);

        //  echo "<pre>"; print_r($id); echo "<pre>"; exit;

          if(!$obChamado instanceof EntityChamado){
            $request->getRouter()->redirect('/admin/chamados');
          }
          //EXCLUI O USUÁRIO
          $obChamado->excluir();
          //REDIRECIONA O USUÁRIO
          $request->getRouter()->redirect('/admin/chamados?status=deletado');

       }



  /**
   * Método responsável por retornar a mensagem de status
   * @param Request $request
   * @return string
   */
  private static function getStatus($request){
    //QUERY PARAMS
    $queryParams = $request->getQueryParams();
    $strMsn = $queryParams['strMsn'] ?? '';
    $nm = filter_input(INPUT_GET, 'nm', FILTER_SANITIZE_STRING) ?? '';
    $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

    //STATUS
    if(!isset($queryParams['status'])) return '';

   //MENSAGENS DE STATUS
   switch ($queryParams['status']) {
     case 'gravado':
       return Alert::getSuccess('Chamado cadastrado com sucesso!'.$strMsn);
       // code...
       break;
     case 'alterado':
       return Alert::getSuccess('Dados do Chamado alterados com sucesso!');
       // code...
       break;
     case 'deletado':
       return Alert::getSuccess('Registro d'.TITLE_CHAMADO.'  <strong>'.$nm.'</strong> deletado com sucesso!');
       // code...
       break;
     case 'duplicado':
       return Alert::getError('Já existe um Chamado com este nome para o mesmo Departamento!');
       // code...
       break;
     case 'emailfail':
       return Alert::getError('Nenhum usuário encontrado com este e-mail!');
       // code...
       break;
     case 'uploadfail':
       return Alert::getError($strMsn);
       // code...
       break;
     case 'statusupdate':
       return Alert::getSuccess('Status do Chamado alterado com sucesso!');
       // code...
       break;
   }
  }
}
