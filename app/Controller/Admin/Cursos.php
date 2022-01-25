<?php

namespace App\Controller\Admin;

use \DateTime;

use \App\Utils\View;
use \App\Model\Entity\Curso as EntityCurso;
use \App\Model\Entity\Areadecurso as EntityAreadecurso;
use \App\Model\Entity\Tipodecurso as EntityTipodecurso;
use \App\Model\Entity\Publicoalvo as EntityPublicoalvo;
use \App\Controller\Admin\Servicos as AdminServico;
use \App\Controller\Admin\Tipodecursos as AdminTipodecursos;
use \App\Controller\Admin\Areadecursos as AdminAreadecursos;
use \App\Controller\Admin\Publicoalvos as AdminPublicoalvos;
use \App\Model\Entity\Arquivo as EntityArquivo;
use \App\File\Upload;
use \App\Db\Pagination;

const DIR_CURSO = 'curso';
const FIELD_CURSO = 'curso';
const ROTA_CURSO = 'cursos';
const ICON_CURSO = 'book';
const TITLE_CURSO = 'Cursos';
const TITLELOW_CURSO = 'o Curso';

class Cursos extends Page{

  /**
   * Método responsável pela renderização da view de listagem de Localizacões
   * @param Request $request
   * @return string
   */
    public static function getListCursos($request,$errorMessage = null){

      $permissao = false;
      $status = self::getStatus($request);
      $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
      $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

      //STATUS
      if(!isset($currentDepartamento)) return $permissao = false;

     //MENSAGENS DE STATUS
     switch ($currentDepartamento) {
       case 'DECOM':
         $permissao = false;
         break;
       case 'DETEC':
         $permissao = true;
         break;
       case 'EMERJ':
         $permissao = false;
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
      $content = View::render('admin/modules/'.DIR_CURSO.'/index',[
        'icon' => ICON_CURSO,
        'title' =>TITLE_CURSO,
        'titlelow' => TITLELOW_CURSO,
        'direntity' => ROTA_CURSO,
        'itens' => self::getCursoItens($request,$obPagination),
        'status' => $status
      ]);
      //RETORNA A PÁGINA COMPLETA
      return parent::getPanel(TITLE_CURSO.' - EMERJ',$content,ROTA_CURSO,$currentDepartamento,$currentPerfil);
  }

  /**
   * Método responsável por obter a renderização das Conteúdos do Curso para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
   private static function getCursoItens($request,&$obPagination){

     $itens = '';
     $tipodeic = '';
     $idreferenciado = '';

     //RESONSÁVEL PELA RENDERIZAÇÃO DOS MODAIS DE EDIÇÃO NA PÁGINA DE LISTAGEM DE LOCALIZAÇÕES
     $strEditModal = View::render('admin/modules/'.DIR_CURSO.'/editmodal',[]);
     $strAddModal = View::render('admin/modules/'.DIR_CURSO.'/addmodal',[]);
     $strAtivaModal = View::render('admin/modules/'.DIR_CURSO.'/ativamodal',[]);
     $strDeleteModal = View::render('admin/modules/'.DIR_CURSO.'/deletemodal',[]);

     //RESULTADO DA PAGINA
     $results = EntityCurso::getCursos();

     //MONTA E RENDERIZA OS ITENS DE Curso
     while($obCurso = $results->fetchObject(EntityCurso::class)){
       $itens .= View::render('admin/modules/'.DIR_CURSO.'/item',[
        'id' => $obCurso->curso_id,
        'curso_nm' => $obCurso->curso_nm,
        'curso_informacoes'  => $obCurso->curso_informacoes,
        'curso_publico_alvo'  => EntityPublicoalvo::getPublicoalvoPorId($obCurso->curso_publico_alvo)->publico_alvo_nm,
        'curso_area'  => EntityAreadecurso::getAreadecursoPorId($obCurso->curso_area)->areadecurso_nm,
        'curso_tipo'  => EntityTipodecurso::getTipodecursoPorId($obCurso->curso_tipo)->tipodecurso_nm,
        'curso_titulo'  => $obCurso->curso_titulo,
        'curso_img_frente'  => $obCurso->curso_img_frente,
        'curso_img_tras'  => $obCurso->curso_img_tras,
        'curso_descricao'  => $obCurso->curso_descricao,
        'curso_obs'  => $obCurso->curso_obs,
        'pdf_edital'  => $obCurso->pdf_edital,

        'dt_ini' => $obCurso->data_inicio,
        'dt_fim' => $obCurso->data_fim,
        'datainicio' => date('d/m/Y', strtotime($obCurso->data_inicio)),
        'horainicio' => date('H', strtotime($obCurso->data_inicio)),
        'mininicio' => date('i', strtotime($obCurso->data_inicio)),
        'datafim' => date('d/m/Y', strtotime($obCurso->data_fim)),
        'horafim' => date('H', strtotime($obCurso->data_fim)),
        'minfim' => date('i', strtotime($obCurso->data_fim)),

        'datainicio_inscricao' => date('d/m/Y', strtotime($obCurso->data_inicio_inscricao)),
        'horainicio' => date('H', strtotime($obCurso->data_inicio)),
        'mininicio' => date('i', strtotime($obCurso->data_inicio)),
        'datafim_inscricao' => date('d/m/Y', strtotime($obCurso->data_fim_inscricao)),
        'horafim' => date('H', strtotime($obCurso->data_fim)),
        'minfim' => date('i', strtotime($obCurso->data_fim)),

        'texto_ativo' => ('s' == $obCurso->ativo_fl) ? 'Desativar' : 'Ativar',
        'class_ativo' => ('s' == $obCurso->ativo_fl) ? 'btn-warning' : 'btn-success',
        'style_ativo' => ('s' == $obCurso->ativo_fl) ? 'table-active' : 'table-danger',
        'id_usuário' => $obCurso->id_usuario,
        'icon' => ICON_CURSO,
        'title' =>TITLE_CURSO,
        'titlelow' => TITLELOW_CURSO,
        'direntity' => ROTA_CURSO
      ]);
    }
    $itens .= $strDeleteModal;
    $itens .= $strAtivaModal;
    $itens .= $strEditModal;
    $itens .= $strAddModal;
    return $itens;
  }



  /**
   * Método responsável por montar a renderização do select de Conteúdos do Curso para o formulário
   * @param Request $request
   * @param integer $id
   * @return string
   */
   public static function getCursoItensSelect($request,$id){
      $itensSelect = '';
      $resultsSelect = EntityCurso::getCursos(null,'curso_id ASC');

      while($obCurso = $resultsSelect->fetchObject(EntityCurso::class)){
        $itensSelect .= View::render('admin/modules/'.DIR_CURSO.'/itemselect',[
          'idSelect' => $obCurso->curso_id,
          'selecionado' => ($id == $obCurso->curso_id) ? 'selected' : '',
          'nomeSelect' => $obCurso->curso_titulo
        ]);
      }
      return $itensSelect;
    }

    public static function getTransformaData($data)
    {
        [$d, $m, $y] = explode('/', $data);
        return implode('-', [$y, $m, $d]);
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
      $arquivo = __DIR__.'/../../../files/'.ROTA_CURSO.'/tmp/'.$obArquivo->arquivo_temp;
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
    public static function redimensionaJPG($nome,$tamx,$tamy,$newJpg){

      $imagem_temporaria = imagecreatefromjpeg($nome);

			$largura_original = imagesx($imagem_temporaria);
			$altura_original = imagesy($imagem_temporaria);

			$imagem_redimensionada = imagecreatetruecolor($tamx,$tamy);
			imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $tamx, $tamy, $largura_original, $altura_original);

			imagejpeg($imagem_redimensionada, $newJpg);

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
        $strNomeTmp = $obUpload->tmp_name ?? '';
        $strSize = $obUpload->size ?? '';
        $strExtensao = strtolower($obUpload->extension) ?? '';

        //GERA UM NOME ALEATÓRIO PARA O ARQUIVO
        $obUpload->generateNewName();

        $allowed = array('jpg','jepg','png','gif');
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
          }
          $strErro = $obUpload->error ?? '';



          //MOVE OS ARQUIVOS DE UPLOAD
          $sucesso = $obUpload->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/tmp',false);

          if($sucesso){

            self::redimensionaJPG(__DIR__.'/../../../files/'.ROTA_CURSO.'/tmp/'.$obUpload->name.'.'.$strExtensao, 1150, 600, __DIR__.'/../../../files/'.ROTA_CURSO.'/fotos/2021' . $strNome . '.jpg');
            self::redimensionaJPG(__DIR__.'/../../../files/'.ROTA_CURSO.'/tmp/'.$obUpload->name.'.'.$strExtensao, 800, 419, __DIR__.'/../../../files/'.ROTA_CURSO.'/banner/' . $strNome . '.jpg');
            self::redimensionaJPG(__DIR__.'/../../../files/'.ROTA_CURSO.'/tmp/'.$obUpload->name.'.'.$strExtensao, 450, 236, __DIR__.'/../../../files/'.ROTA_CURSO.'/principal/' . $strNome . '.jpg');
            self::redimensionaJPG(__DIR__.'/../../../files/'.ROTA_CURSO.'/tmp/'.$obUpload->name.'.'.$strExtensao, 350, 183, __DIR__.'/../../../files/'.ROTA_CURSO.'/capa/' . $strNome . '.jpg');


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
     * Método responsável por retornar o formulário de cadastro de um novo Chamado
     * @param Request $request
     * @return string
     */
     public static function getNovoCurso($request){

       $tipodecurso_id = '';
       $areadecurso_id = '';
       $publico_alvo_id = '';

       $status = '';
       $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
       $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

       $optionsTipodecurso = AdminTipodecursos::getTipodecursoItensSelect($request,$tipodecurso_id);
       $optionsAreadecurso = AdminAreadecursos::getAreadecursoItensSelect($request,$areadecurso_id);
       $optionsPublicoalvo = AdminPublicoalvos::getPublicoalvoItensSelect($request,$publico_alvo_id);

       //CONTEÚDO DA NOTÍCIA
       $content = View::render('admin/modules/'.DIR_CURSO.'/form',[
         'icon' => ICON_CURSO,
         'title' =>TITLE_CURSO,
         'titlelow' => TITLELOW_CURSO,
         'direntity' => ROTA_CURSO,
         'itens' => self::getCursoItens($request,$obPagination),
         'curso_nm' => '',
         'curso_tipo' => $optionsTipodecurso,
         'curso_area' => $optionsAreadecurso,
         'curso_publico_alvo' => $optionsPublicoalvo,
         'curso_titulo' => '',
         'curso_descricao' => '',
         'curso_informacoes' => '',
         'descricao' => '',
         'curso_obs' => '',
         'datainicio' => '',
         'horainicio' => '',
         'mininicio' => '',
         'datafim' => '',
         'horafim' => '',
         'minfim' => '',
         'datainicio_inscricao' => '',
         'horainicio_inscricao' => '',
         'mininicio_inscricao' => '',
         'datafim_inscricao' => '',
         'horafim_inscricao' => '',
         'minfim_inscricao' => '',
         'id_usuario' => $_SESSION['admin']['usuario']['usuario_id'] ?? '',
         'status' => $status
       ]);

       //RETORNA A PÁGINA COMPLETA
       return parent::getPanel('Cadastrar Notícia - EMERJ',$content,'cursos',$currentDepartamento,$currentPerfil);
     }

   /**
    * Método responsável por cadastro de uma nova Localização no banco
    * @param Request $request
    * @return string
    */
    public static function setNovoCurso($request){

      date_default_timezone_set('America/Sao_Paulo');

      if (isset($_FILES['cursoUploadFrontal'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadF = new Upload($_FILES['cursoUploadFrontal']);

        $strNomeF = $obUploadF->name ?? '';
        $strNomeTmpF = $obUploadF->tmp_name ?? '';
        $strSizeF = $obUploadF->size ?? '';
        $strExtensaoF = strtolower($obUploadF->extension) ?? '';

        //GERA UM NOME ALEATÓRIO PARA O ARQUIVO
        $obUploadF->generateNewName().'_f';

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoF = $obUploadF->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/originais',false);
        $strNomeImgF = $obUploadF->getBasename();

      }

      if (isset($_FILES['cursoUploadTraseira'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadT = new Upload($_FILES['cursoUploadTraseira']);

        $strNomeT = $obUploadT->name ?? '';
        $strNomeTmpT = $obUploadT->tmp_name ?? '';
        $strSizeT = $obUploadT->size ?? '';
        $strExtensaoT = strtolower($obUploadT->extension) ?? '';

        //GERA UM NOME ALEATÓRIO PARA O ARQUIVO
        $obUploadT->generateNewName().'_t';

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoT = $obUploadT->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/originais',false);
        $strNomeImgT = $obUploadT->getBasename();

      }

      if (isset($_FILES['cursoUploadEditalPDF'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadE = new Upload($_FILES['cursoUploadEditalPDF']);

        $strNomeE = $obUploadE->name ?? '';
        $strNomeTmpE = $obUploadE->tmp_name ?? '';
        $strSizeE = $obUploadE->size ?? '';
        $strExtensaoE = strtolower($obUploadE->extension) ?? '';

        //GERA UM NOME ALEATÓRIO PARA O ARQUIVO
        $obUploadE->generateNewName();

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoE = $obUploadE->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/editais',false);
        $strNomeEdital = $obUploadE->getBasename();

      }

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $curso_informacoes = $posVars['informacoes'];
      $publicoalvo = filter_input(INPUT_POST, 'publicoalvo', FILTER_SANITIZE_NUMBER_INT);
      $area = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_NUMBER_INT);
      $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_NUMBER_INT);
      $curso_titulo = filter_input(INPUT_POST, 'curso_titulo', FILTER_SANITIZE_STRING);
      $curso_descricao = $posVars['curso_descricao'];

      $curso_imgalt_frontal = filter_input(INPUT_POST, 'curso_imgalt_frontal', FILTER_SANITIZE_STRING);
      $curso_imgtittle_frontal = filter_input(INPUT_POST, 'curso_imgtittle_frontal', FILTER_SANITIZE_STRING);

      $curso_imgalt_traseira = filter_input(INPUT_POST, 'curso_imgalt_traseira', FILTER_SANITIZE_STRING);
      $curso_imgtittle_traseira = filter_input(INPUT_POST, 'curso_imgtittle_traseira', FILTER_SANITIZE_STRING);

      $curso_obs = $posVars['curso_obs'];

      $datainicio = filter_input(INPUT_POST, 'datainicio', FILTER_SANITIZE_STRING);
      $horainicio = filter_input(INPUT_POST, 'horainicio', FILTER_SANITIZE_STRING);
      $mininicio = filter_input(INPUT_POST, 'mininicio', FILTER_SANITIZE_STRING);
      $datafim = filter_input(INPUT_POST, 'datafim', FILTER_SANITIZE_STRING);
      $horafim = filter_input(INPUT_POST, 'horafim', FILTER_SANITIZE_STRING);
      $minfim = filter_input(INPUT_POST, 'minfim', FILTER_SANITIZE_STRING);

      $datainicio_inscricao = filter_input(INPUT_POST, 'datainicio_inscricao', FILTER_SANITIZE_STRING);
      $horainicio_inscricao = filter_input(INPUT_POST, 'horainicio_inscricao', FILTER_SANITIZE_STRING);
      $mininicio_inscricao = filter_input(INPUT_POST, 'mininicio_inscricao', FILTER_SANITIZE_STRING);
      $datafim_inscricao = filter_input(INPUT_POST, 'datafim_inscricao', FILTER_SANITIZE_STRING);
      $horafim_inscricao = filter_input(INPUT_POST, 'horafim_inscricao', FILTER_SANITIZE_STRING);
      $minfim_inscricao = filter_input(INPUT_POST, 'minfim_inscricao', FILTER_SANITIZE_STRING);

      $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_NUMBER_INT);

      $data_inicio = self::getTransformaData($datainicio).' '.$horainicio.':'.$mininicio.':00';
      $data_fim = self::getTransformaData($datafim).' '.$horafim.':'.$minfim.':00';

      $data_inicio_inscricao = self::getTransformaData($datainicio_inscricao).' '.$horainicio_inscricao.':'.$mininicio_inscricao.':00';
      $data_fim_inscricao = self::getTransformaData($datafim_inscricao).' '.$horafim_inscricao.':'.$minfim_inscricao.':00';

      //NOVA ISNTANCIA DE DEPARTAMENTO
      $obCurso = new EntityCurso;

      $obCurso->curso_nm = $nome;
      $obCurso->curso_informacoes = html_entity_decode($curso_informacoes);
      $obCurso->curso_tipo = $tipo;
      $obCurso->curso_area = $area;
      $obCurso->curso_publico_alvo = $publicoalvo;
      $obCurso->curso_titulo = $curso_titulo;
      $obCurso->curso_descricao = html_entity_decode($curso_descricao);

      $obCurso->curso_img_frente = $strNomeImgF;
      $obCurso->curso_imgalt_frente = 'Imagem do banner principal do curso - '.$nome;
      $obCurso->curso_imgtittle_frente = $nome. '- imagem principal';

      $obCurso->curso_img_tras = $strNomeImgT;
      $obCurso->curso_imgalt_tras = 'Imagem do banner 02 do curso - '.$nome;
      $obCurso->curso_imgtittle_tras = $nome.' - imagem 02';

      $obCurso->curso_obs = $curso_obs;
      $obCurso->pdf_edital = $strNomeEdital;

      $obCurso->data_inicio = $data_inicio;
      $obCurso->data_fim = $data_fim;
      $obCurso->data_inicio_inscricao = $data_inicio_inscricao;
      $obCurso->data_fim_inscricao = $data_fim_inscricao;


      $obCurso->id_usuario = $id_usuario;
      $obCurso->cadastrar();

      $idCurso = $obCurso->curso_id;

      $dirOrigem = __DIR__.'/../../../files/'.ROTA_CURSO.'/originais';
      $dirImages = __DIR__.'/../../../files/'.ROTA_CURSO.'/images';
      $dirEditais = __DIR__.'/../../../files/'.ROTA_CURSO.'/editais';

      if($sucessoF){
        $imageUploadedF = $dirOrigem.'/'.$strNomeImgF;
        self::redimensionaJPG($imageUploadedF, 1080, 1351, $dirImages.'/1080_'.$idCurso.'_'.$strNomeImgF);
        self::redimensionaJPG($imageUploadedF, 200, 250, $dirImages.'/200_'.$idCurso.'_'.$strNomeImgF);
      }
      if($sucessoT){
        $imageUploadedT = $dirOrigem.'/'.$strNomeImgT;
        self::redimensionaJPG($imageUploadedT, 1080, 1351, $dirImages.'/1080_'.$idCurso.'_'.$strNomeImgT);
        self::redimensionaJPG($imageUploadedT, 200, 250, $dirImages.'/200_'.$idCurso.'_'.$strNomeImgT);
      }

      //REDIRECIONA O DEPARTAMENTO
      $request->getRouter()->redirect('/admin/cursos?status=gravado&nm='.$nome.'&acao=alter');
    }

    /**
     * Método responsável por gravar a edição de uma Localização
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditCurso($request,$id){

       $status = '';
       $currentDepartamento = $_SESSION['admin']['usuario']['departamento'];
       $currentPerfil = $_SESSION['admin']['usuario']['id_perfil'];

        $obCurso = EntityCurso::getCursoPorId($id);

       //CONTEÚDO DA NOTÍCIA
       $content = View::render('admin/modules/'.DIR_CURSO.'/form',[
         'icon' => ICON_CURSO,
         'title' => 'Alterar '.TITLE_CURSO,
         'titlelow' => TITLELOW_CURSO,
         'direntity' => ROTA_CURSO,
         'itens' => self::getCursoItens($request,$obPagination),
         'curso_id' => $obCurso->curso_id ?? '',
         'curso_nm' => $obCurso->curso_nm ?? '',

         'curso_informacoes'  => $obCurso->curso_informacoes ?? '',
         'curso_publico_alvo'  => AdminPublicoalvos::getPublicoalvoItensSelect($request,$obCurso->curso_publico_alvo) ?? '',
         'curso_area'  => AdminAreadecursos::getAreadecursoItensSelect($request,$obCurso->curso_area) ?? '',
         'curso_tipo'  => AdminTipodecursos::getTipodecursoItensSelect($request,$obCurso->curso_tipo) ?? '',
         'curso_titulo'  => $obCurso->curso_titulo ?? '',
         'curso_img_frente'  => '200_'.$obCurso->curso_id.'_'.$obCurso->curso_img_frente ?? '',
         'curso_imgalt_frente'  => $obCurso->curso_imgalt_frente ?? '',
         'curso_imgtittle_frente'  => $obCurso->curso_imgtittle_frente ?? '',
         'curso_img_tras'  => '200_'.$obCurso->curso_id.'_'.$obCurso->curso_img_tras ?? '',
         'curso_imgalt_tras'  => $obCurso->curso_imgalt_tras ?? '',
         'curso_imgtittle_tras'  => $obCurso->curso_imgtittle_tras ?? '',
         'curso_descricao'  => $obCurso->curso_descricao ?? '',
         'curso_obs'  => $obCurso->curso_obs ?? '',
         'curso_pdf_edital'  => $obCurso->curso_pdf_edital ?? '',

         'datainicio_curso' => date('d/m/Y', strtotime($obCurso->data_inicio)),
         'datafim_curso' => date('d/m/Y', strtotime($obCurso->data_fim)),

         'datainicio' => date('d/m/Y', strtotime($obCurso->data_inicio)),
         'horainicio' => date('H', strtotime($obCurso->data_inicio)),
         'mininicio' => date('i', strtotime($obCurso->data_inicio)),
         'datafim' => date('d/m/Y', strtotime($obCurso->data_fim)),

         'datainicio_inscricao' => date('d/m/Y', strtotime($obCurso->data_inicio_inscricao)),
         'horainicio' => date('H', strtotime($obCurso->data_inicio_inscricao)),
         'mininicio' => date('i', strtotime($obCurso->data_inicio_inscricao)),
         'datafim_inscricao' => date('d/m/Y', strtotime($obCurso->data_fim_inscricao)),
         'horafim' => date('H', strtotime($obCurso->data_fim)),
         'minfim' => date('i', strtotime($obCurso->data_fim)),

         'id_usuario' => $obCurso->id_usuario ?? '',
         'status' => $status
       ]);

       return parent::getPanel('Alterar Notícia - EMERJ',$content,'cursos',$currentDepartamento,$currentPerfil);


     }

   /**
    * Método responsável por gravar a edição de uma Localização
    * @param Request $request
    * @param integer $id
    * @return string
    */
    public static function setEditCurso($request,$id){

      date_default_timezone_set('America/Sao_Paulo');

      //DADOS DO POST
      $posVars = $request->getPostVars();
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $curso_informacoes = $posVars['informacoes'];
      $publicoalvo = filter_input(INPUT_POST, 'publicoalvo', FILTER_SANITIZE_NUMBER_INT);
      $area = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_NUMBER_INT);
      $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_NUMBER_INT);
      $curso_titulo = filter_input(INPUT_POST, 'curso_titulo', FILTER_SANITIZE_STRING);
      $curso_descricao = $posVars['curso_descricao'];

      $curso_imgalt_frontal = filter_input(INPUT_POST, 'curso_imgalt_frontal', FILTER_SANITIZE_STRING);
      $curso_imgtittle_frontal = filter_input(INPUT_POST, 'curso_imgtittle_frontal', FILTER_SANITIZE_STRING);

      $curso_imgalt_traseira = filter_input(INPUT_POST, 'curso_imgalt_traseira', FILTER_SANITIZE_STRING);
      $curso_imgtittle_traseira = filter_input(INPUT_POST, 'curso_imgtittle_traseira', FILTER_SANITIZE_STRING);

      $curso_obs = $posVars['curso_obs'];

      $datainicio = filter_input(INPUT_POST, 'datainicio', FILTER_SANITIZE_STRING);
      $horainicio = filter_input(INPUT_POST, 'horainicio', FILTER_SANITIZE_STRING);
      $mininicio = filter_input(INPUT_POST, 'mininicio', FILTER_SANITIZE_STRING);
      $datafim = filter_input(INPUT_POST, 'datafim', FILTER_SANITIZE_STRING);
      $horafim = filter_input(INPUT_POST, 'horafim', FILTER_SANITIZE_STRING);
      $minfim = filter_input(INPUT_POST, 'minfim', FILTER_SANITIZE_STRING);

      $datainicio_inscricao = filter_input(INPUT_POST, 'datainicio_inscricao', FILTER_SANITIZE_STRING);
      $horainicio_inscricao = filter_input(INPUT_POST, 'horainicio_inscricao', FILTER_SANITIZE_STRING);
      $mininicio_inscricao = filter_input(INPUT_POST, 'mininicio_inscricao', FILTER_SANITIZE_STRING);
      $datafim_inscricao = filter_input(INPUT_POST, 'datafim_inscricao', FILTER_SANITIZE_STRING);
      $horafim_inscricao = filter_input(INPUT_POST, 'horafim_inscricao', FILTER_SANITIZE_STRING);
      $minfim_inscricao = filter_input(INPUT_POST, 'minfim_inscricao', FILTER_SANITIZE_STRING);

      $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_NUMBER_INT);

      $data_inicio = self::getTransformaData($datainicio).' '.$horainicio.':'.$mininicio.':00';
      $data_fim = self::getTransformaData($datafim).' '.$horafim.':'.$minfim.':00';

      $data_inicio_inscricao = self::getTransformaData($datainicio_inscricao).' '.$horainicio_inscricao.':'.$mininicio_inscricao.':00';
      $data_fim_inscricao = self::getTransformaData($datafim_inscricao).' '.$horafim_inscricao.':'.$minfim_inscricao.':00';

      //OBTÉM O USUÁRIO DO BANCO DE DADOS
      $obCurso = EntityCurso::getCursoPorId($id);

      if(!$obCurso instanceof EntityCurso){
        $request->getRouter()->redirect('/admin/cursos?status=updatefail');
      }

      if (isset($_FILES['cursoUploadFrontal'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadF = new Upload($_FILES['cursoUploadFrontal']);

        $strNomeImgF = $obCurso->curso_img_frente;

        $strNomeF = $obUploadF->name;
        $strNomeTmpF = $obUploadF->tmp_name ?? '';
        $strSizeF = $obUploadF->size ?? '';
        $strExtensaoF = strtolower($obUploadF->extension) ?? '';
        $strNome = substr($strNomeImgF,0,-4) ?? '';

        $obUploadF->setName($strNome) ?? '';

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoF = $obUploadF->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/originais',true);
        $obUploadF->getBasename();

      }

      if (isset($_FILES['cursoUploadTraseira'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadT = new Upload($_FILES['cursoUploadTraseira']);

        $strNomeImgT = $obCurso->curso_img_tras;

        $strNomeT = $obUploadT->name;
        $strNomeTmpT = $obUploadT->tmp_name ?? '';
        $strSizeT = $obUploadT->size ?? '';
        $strExtensaoT = strtolower($obUploadT->extension) ?? '';
        $strNome = substr($strNomeImgT,0,-4) ?? '';

        $obUploadT->setName($strNome) ?? '';

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoT = $obUploadT->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/originais',true);
        $obUploadT->getBasename();

      }
      echo "<pre>"; print_r('$strNomeImgT = '.$strNomeImgT); echo "<pre>";

      if (isset($_FILES['cursoUploadEditalPDF'])) {

        //INSTÂNCIAS DO UPLOAD
        $obUploadE = new Upload($_FILES['cursoUploadEditalPDF']);

        $strNomeEdital = $obCurso->pdf_edital;

        $strNomeE = $obUploadE->name ?? '';
        $strNomeTmpE = $obUploadE->tmp_name ?? '';
        $strSizeE = $obUploadE->size ?? '';
        $strExtensaoE = strtolower($obUploadE->extension) ?? '';
        $strNome = substr($strNomeEdital,0,-4) ?? '';

        $obUploadE->setName($strNome) ?? '';

        //MOVE OS ARQUIVOS DE UPLOAD
        $sucessoE = $obUploadE->upload(__DIR__.'/../../../files/'.ROTA_CURSO.'/editais',true);
        $obUploadE->getBasename();

      }

      //ATUALIZA A INSTANCIA
      $obCurso->curso_nm = $nome;
      $obCurso->curso_informacoes = html_entity_decode($curso_informacoes);
      $obCurso->curso_tipo = $tipo;
      $obCurso->curso_area = $area;
      $obCurso->curso_publico_alvo = $publicoalvo;
      $obCurso->curso_titulo = $curso_titulo;
      $obCurso->curso_descricao = html_entity_decode($curso_descricao);
      if ($strSizeF > 0) {
        $obCurso->curso_img_frente = $strNomeImgF;
      }
      if ($strSizeT > 0) {
        $obCurso->curso_img_tras = $strNomeImgT;
      }
      if ($strSizeE > 0) {
        $obCurso->pdf_edital = $strNomeEdital;
      }

      $obCurso->curso_obs = $curso_obs;

      $obCurso->data_inicio = $data_inicio;
      $obCurso->data_fim = $data_fim;
      $obCurso->data_inicio_inscricao = $data_inicio_inscricao;
      $obCurso->data_fim_inscricao = $data_fim_inscricao;

      $obCurso->id_usuario = $id_usuario;
      $sucessoUpdate = $obCurso->atualizar();

      $dirOrigem = __DIR__.'/../../../files/'.ROTA_CURSO.'/originais';
      $dirImages = __DIR__.'/../../../files/'.ROTA_CURSO.'/images';
      $dirEditais = __DIR__.'/../../../files/'.ROTA_CURSO.'/editais';

      if ($sucessoUpdate) {
        if($sucessoF){
          $imageUploadedF = $dirOrigem.'/'.$strNomeImgF;
          self::redimensionaJPG($imageUploadedF, 1080, 1351, $dirImages.'/1080_'.$id.'_'.$strNomeImgF);
          self::redimensionaJPG($imageUploadedF, 200, 250, $dirImages.'/200_'.$id.'_'.$strNomeImgF);
        }
        if($sucessoT){
          $imageUploadedT = $dirOrigem.'/'.$strNomeImgT;
          self::redimensionaJPG($imageUploadedT, 1080, 1351, $dirImages.'/1080_'.$id.'_'.$strNomeImgT);
          self::redimensionaJPG($imageUploadedT, 200, 250, $dirImages.'/200_'.$id.'_'.$strNomeImgT);
        }
      }
      //REDIRECIONA O USUÁRIO PARA A PAGINA INICIAL DE LISTAR CATEGORIAS DE IC's
      $request->getRouter()->redirect('/admin/cursos?status=alterado&nm='.$nome.'&acao=alter');
    }

    /**
     * Método responsável por retornar o formulário de alteração de status de um usuário
     * @param Request $request
     * @param integer $id
     * @return string
     */
     public static function getAltStatusCurso($request,$id){

       //OBTÉM A CATEGORIA DO IC PELO SEU ID DO BANCO DE DADOS
       $obCurso = EntityCurso::getCursoPorId($id);
       $strNome = $obCurso->curso_nm;



       if(!$obCurso instanceof EntityCurso){
         $request->getRouter()->redirect('/admin/cursos?status=updatefail');
       }

       //OBTÉM O USUÁRIO DO BANCO DE DADOS
       if($obCurso->ativo_fl == 's'){
         $altStatus = 'n';
         $strMsn = ' DESATIVADO ';
       } elseif (($obCurso->ativo_fl == 'n') || ($obCurso->ativo_fl == '')) {
         $strMsn = ' ATIVADO ';
         $altStatus = 's';
       }

       //ATUALIZA A INSTANCIA (RESETA A SENHA DO USUÁRIO)
       $obCurso->ativo_fl = $altStatus;
       $obCurso->atualizar();

       //REDIRECIONA O USUÁRIO
       $request->getRouter()->redirect('/admin/cursos?status=statusupdate&nm='.$strNome.$strMsn);

     }

   /**
      * Método responsável por retornar o formulário de exclusão de um usuário atraves de um Modal
      * @param Request $request
      * @param integer $id
      * @return string
      */
      public static function getDeleteCurso($request,$id){

       //OBTÉM O LOCALIZAÇÃO DO BANCO DE DADOS
        $obCurso = EntityCurso::getCursoPorId($id);
        $strNome = $obCurso->curso_nm;

        if(!$obCurso instanceof EntityCurso){
          $request->getRouter()->redirect('/admin/cursos');
        }

       //EXCLUI O USUÁRIO
        $obCurso->excluir();
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/cursos?status=deletado&nm='.$strNome.$strMsn.'&acao=alter');
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
      if(!isset($status)) return '';

     //MENSAGENS DE STATUS
     switch ($status) {
       case 'gravado':
         return Alert::getSuccess('Dados d'.TITLE_CURSO.' <strong>'.$nm.'</strong> cadastrados com sucesso!');
         // code...
         break;
       case 'alterado':
         return Alert::getSuccess('Dados d'.TITLELOW_CURSO.' <strong>'.$nm.'</strong> alterados com sucesso!');
         // code...
         break;
       case 'deletado':
         return Alert::getSuccess('Registro d'.TITLE_CURSO.'  <strong>'.$nm.'</strong> deletado com sucesso!');
         // code...
         break;
       case 'updatefaildatediff':
         return Alert::getError('A diferença entre as datas fim e a início não pode ser inferior a um dia!');
         // code...
         break;
       case 'updatefaildateAgora':
         return Alert::getError('A data de início não pode ser anterior a data/hora de agora!');
         // code...
         break;

       case 'duplicado':
         return Alert::getError('Já existe '.TITLELOW_CURSO.' com este nome (<strong>'.$nm.'</strong>) para outr'.TITLELOW_CURSO.'!');
         // code...
         break;
       case 'statusupdate':
         return Alert::getSuccess('Status d'.TITLELOW_CURSO.' <strong>'.$nm.'</strong> com sucesso!');
         // code...
         break;
     }
   }
}
