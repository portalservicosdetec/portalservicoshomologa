<?php

namespace App\Controller\Pages;

use \DateTime;

use \App\Utils\View;
use \App\Model\Entity\Pagina as EntityPagina;
use \App\Model\Entity\Sessao as EntitySessao;
use \App\Model\Entity\Subsessao as EntitySubsessao;
use \App\Model\Entity\Noticia as EntityNoticia;
use \App\Model\Entity\Evento as EntityEvento;
use \App\Model\Entity\Curso as EntityCurso;
use \App\Model\Entity\Tipodecurso as EntityTipodecurso;
use \App\Controller\Admin\Paginas as AdminPaginas;
use \App\Controller\Admin\Sessoes as AdminSessoes;
use \App\Controller\Admin\Noticias as AdminNoticias;

class Site extends Page{

  /**
   * Método responsável por retornar o conteúdo (view) da nossa home
   * @param string
   */
  public static function getSite(){



    $content = View::render('/pages/home',[
      'titulo' => 'Título da Página Principal',
      'descricao' => '',
      'conteudo' => 'Conteúdo da página principal',
      'menu' => 'menulateral',
      'indicators' => self::getNoticiasIndicatorCarrousel() ?? '',
      'slider' => self::getNoticiasSliderCarrousel() ?? '',
      'calendario' => View::render('pages/calendario/index') ?? ''
    ]);
    //RETORNA A VIEW DA PÁGINA
    return parent::getPrincipal('EMERJ - Novo Site',$content,'ESCOLA');
  }

  /**
   * Método responsável por retornar o conteúdo (view) da nossa home
   * @param string
   */
  public static function getConteudoSubsessao($pagina,$sessao,$subsessao){

    $objPagina = EntityPagina::getPaginaPorId($pagina);
    $objSessao = EntitySessao::getSessaoPorId($sessao);
    $objSubSessao = EntitySubsessao::getSubsessaoPorId($subsessao);

    $content = View::render('/pages/site',[
      'titulo' => $objSubSessao->subsessao_titulo ?? '',
      'descricao' => '',
      'conteudo' => $objSubSessao->subsessao_conteudo ?? '',
      'menu' => 'menu'
    ]);
    //RETORNA A VIEW DA PÁGINA
    return parent::getPrincipal('EMERJ - Novo Site',$content,$objPagina->pagina_nm);
  }




  /**
   * Método responsável por retornar o conteúdo (view) da nossa home
   * @param string
   */
  public static function getConteudoSessao($pagina,$sessao){

    $objPagina = EntityPagina::getPaginaPorId($pagina);
    $objSessao = EntitySessao::getSessaoPorId($sessao);

    $content = View::render('/pages/site',[
      'titulo' => $objSessao->sessao_titulo ?? '',
      'descricao' => '',
      'conteudo' => $objSessao->sessao_conteudo ?? '',
      'menu' => self::getMenuSessao($pagina,$sessao)
    ]);
    //RETORNA A VIEW DA PÁGINA
    return parent::getPrincipal('EMERJ - Novo Site',$content,$objPagina->pagina_nm);
  }


  /**
   * Método responsável por retornar o conteúdo (view) da nossa home
   * @param string
   */
  public static function getMenuSessao($pagina,$sessao){

    $itens = '';

    $objPagina = EntityPagina::getPaginaPorId($pagina);
    $where = ' id_pagina = '.$pagina;
    $results = EntitySessao::getSessoes($where,'sessao_nm asc');

    //MONTA E RENDERIZA OS ITENS DE Noticia
    while($obSessao = $results->fetchObject(EntitySessao::class)){
      $itens .= View::render('pages/menu/menusessao',[
       'pagina' => $obSessao->id_pagina,
       'current' => $obSessao->sessao_id == $sessao ? 'texto-menu-lateral-ativo' : '',
       'sessao_id' => $obSessao->sessao_id,
       'sessao_nm' => $obSessao->sessao_nm,
     ]);
   }
   return $itens;
 }

  /**
   * Método responsável por retornar o conteúdo (view) da nossa home
   * @param string
   */
  public static function getNoticiaDetalhe($request,$id){

    $objNoticia = EntityNoticia::getNoticiaPorId($id);

    $content = View::render('/pages/noticia/detalhe',[
      'id' => $objNoticia->noticia_id ?? '',
      'noticia_titulo' => $objNoticia->noticia_titulo ?? '',
      'noticia_img' => $objNoticia->noticia_img,
      'noticia_imgalt' => $objNoticia->noticia_imgalt,
      'noticia_imgtittle' => $objNoticia->noticia_imgtittle,
      'noticia_conteudo' => $objNoticia->noticia_descricao ?? ''
    ]);
    //RETORNA A VIEW DA PÁGINA
    return parent::getPrincipal('EMERJ - Novo Site',$content,'noticias');
  }


  /**
   * Método responsável por obter a renderização das Conteúdos do Noticia para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
   public static function getNoticiasIndicatorCarrousel(){

     $itens = '';
     $indicadorini = 0;
     $indicador = 1;

    //RESULTADO DA PAGINA
    $results = EntityNoticia::getNoticias(null,'noticia_id asc', 4);
    //MONTA E RENDERIZA OS ITENS DE Noticia
    while($objNoticia = $results->fetchObject(EntityNoticia::class)){
    $itens .= View::render('pages/carousel/indicators',[
    // 'classe' => $indicadorini == 0 ? 'class="active" aria-current="true"' : '',
    'classe' => $indicadorini == 0 ? 'active' : '',
    'indicadorini' => $indicadorini++,
    'indicador' => $indicador++
    ]);


   }
   return $itens;
 }


  /**
   * Método responsável por obter a renderização das Conteúdos do Noticia para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
   public static function getNoticiasSliderCarrousel(){

     $itens = '';

    //RESULTADO DA PAGINA
    $results = EntityNoticia::getNoticias(null,'noticia_id asc', 4);

    //MONTA E RENDERIZA OS ITENS DE Noticia
    while($objNoticia = $results->fetchObject(EntityNoticia::class)){
      $itens .= View::render('pages/carousel/slider',[
       'id' => $objNoticia->noticia_id,
       'noticia_titulo' => $objNoticia->noticia_titulo,
       'noticia_img' => $objNoticia->noticia_img,
       'active' => ($objNoticia->noticia_id == 1) ? 'active' : '',
       'noticia_imgalt' => $objNoticia->noticia_imgalt,
       'noticia_imgtittle' => $objNoticia->noticia_imgtittle
     ]);
   }
   return $itens;
 }

  /**
   * Método responsável por obter a renderização das Conteúdos do Noticia para a página
   * @param Request $request
   * @param Pagination $obPagination
   * @return string
   */
   public static function getNoticiasCapa($request,$obPagination){

     $itens = '';

    //RESULTADO DA PAGINA
    $results = EntityNoticia::getNoticias(null,'data_inicio desc');

    //MONTA E RENDERIZA OS ITENS DE Noticia
    while($obNoticia = $results->fetchObject(EntityNoticia::class)){
      $itens .= View::render('pages/noticia/item',[
       'id' => $obNoticia->noticia_id,
       'noticia_nm' => $obNoticia->noticia_nm,
       'noticia_titulo' => $obNoticia->noticia_titulo,
       'noticia_img' => $obNoticia->noticia_img,
       'noticia_imgalt' => $obNoticia->noticia_imgalt,
       'noticia_imgtittle' => $obNoticia->noticia_imgtittle,
       'noticia_data' => date('d/m/Y', strtotime($obNoticia->data_inicio)),
       'noticia_textocapa' => self::limitCharacter($obNoticia->noticia_descricao,'\S',1,200).' [...]' ?? '',
     ]);
   }
   //CONTEÚDO DA HOME
   $content = View::render('pages/site',[
     'titulo' => 'Notícias',
     'descricao' => '',
     'conteudo' => $itens,
     'menu' => 'menu'
   ]);
   //RETORNA A PÁGINA COMPLETA
   return parent::getPrincipal('EMERJ - Novo Site',$content,'noticias');
 }

 /**
  * Método responsável por obter a renderização das Conteúdos do Noticia para a página
  * @param Request $request
  * @param Pagination $obPagination
  * @return string
  */
  public static function getCursosCapa($request,$tipo){

    $itens = '';
    $where = '';
    $strTipo = '';

    if ($tipo) {
      $strTipo = ' curso_tipo = '.$tipo;
    }

    $where = $strTipo;

   //RESULTADO DA PAGINA
   $results = EntityCurso::getCursos($where,'data_inicio asc');

   //MONTA E RENDERIZA OS ITENS DE Noticia
   while($obCurso = $results->fetchObject(EntityCurso::class)){
     $itens .= View::render('pages/curso/item',[
      'id' => $obCurso->curso_id,
      'tipo' => $tipo,
      'curso_nm' => $obCurso->curso_nm,
      'curso_titulo' => $obCurso->curso_titulo,
      'curso_img' => $obCurso->curso_img_frente,
      'curso_imgalt' => $obCurso->curso_imgalt_frente,
      'curso_imgtittle' => $obCurso->curso_imgtittle_frente,
      'curso_data' => date('d/m/Y', strtotime($obCurso->data_inicio)),
      'curso_textocapa' => self::limitCharacter($obCurso->curso_descricao,'\S',1,200).' [...]' ?? '',
    ]);
  }
  //CONTEÚDO DA HOME
  $content = View::render('pages/site',[
    'titulo' => EntityTipodecurso::getTipodecursoPorId($tipo)->tipodecurso_nm,
    'descricao' => EntityTipodecurso::getTipodecursoPorId($tipo)->tipodecurso_conteudo ?? '',
    'conteudo' => $itens,
    'menu' => self::getMenuCurso(null)
  ]);
  //RETORNA A PÁGINA COMPLETA
  return parent::getPrincipal('EMERJ - Novo Site',$content,'cursos');
}

/**
 * Método responsável por retornar o conteúdo (view) da nossa home
 * @param string
 */
public static function getCursoDetalhe($request,$tipo,$id){

  $objCurso = EntityCurso::getCursoPorId($id);

  $content = View::render('/pages/curso/detalhe',[
    'id' => $objCurso->curso_id ?? '',
    'nome' => $objCurso->curso_nm ?? '',
    'curso_img' => $objCurso->curso_img_frente ?? '',
    'curso_conteudo' => $objCurso->curso_informacoes ?? '',
    'curso_titulo' => $objCurso->curso_titulo ?? '',
    'curso_imgalt' => $objCurso->curso_imgalt_frente ?? '',
    'curso_imgtittle' => $objCurso->curso_imgtittle_frente ?? '',
    'curso_obs'  => $objCurso->curso_obs,
    'pdf_edital'  => $objCurso->pdf_edital,
  ]);

  //CONTEÚDO DA HOME
  $content = View::render('pages/site',[
    'titulo' => EntityTipodecurso::getTipodecursoPorId($tipo)->tipodecurso_nm,
    'descricao' => '',
    'conteudo' => $content,
    'menu' => self::getMenuCurso(null)
  ]);

  //RETORNA A VIEW DA PÁGINA
  return parent::getPrincipal('EMERJ - Novo Site',$content,'cursos');
}

/**
 * Método responsável por retornar o conteúdo (view) da nossa home
 * @param string
 */
public static function getMenuCurso($tipo){

  $itens = '';
  $where = '';
  $strTipo = '';

  if ($tipo) {
    $strTipo = ' curso_tipo = '.$tipo;
  }

  $where = $strTipo;

  $results = EntityTipodecurso::getTipodecursos($where,'tipodecurso_nm asc');

  //MONTA E RENDERIZA OS ITENS DE Noticia
  while($obMenuCurso = $results->fetchObject(EntityTipodecurso::class)){
    $itens .= View::render('pages/menu/menucurso',[
     'curso' => $obMenuCurso->tipodecurso_id,
     'current' => $obMenuCurso->tipodecurso_id == $tipo ? 'texto-menu-lateral-ativo' : '',
     'tipodecurso_id' => $obMenuCurso->tipodecurso_id,
     'tipodecurso_nm' => $obMenuCurso->tipodecurso_nm,
   ]);
 }
 return $itens;
}

  // FUNÇÃO PARA LIMITAR A QUANTIDADE DE CARACTERES ATE O PRÓXIMO ESPAÇO
  public static function limitCharacter($string,$srtValor,$ini,$tam){

    $regex = '/.{'.$ini.','.$tam.'}('.$srtValor.'*|$)/';

    preg_match_all($regex, $string, $matches);

    $result = array_shift($matches[0]);

    return $result;
  }

 /**
  * Método responsável por retornar o conteúdo (view) da nossa home
  * @param string
  */
 public static function getEventoDetalhe($request,$id){

   $objEvento = EntityEvento::getEventoPorId($id);

   $content = View::render('/pages/evento/detalhe',[
     'codigo' => $objEvento->codigo ?? '',
     'nome' => $objEvento->nome ?? '',
     'local' => $objEvento->local ?? ''
   ]);
   //RETORNA A VIEW DA PÁGINA
   return parent::getPrincipal('EMERJ - Novo Site',$content,'eventos');
 }

 /**
  * Método responsável por obter a renderização das Conteúdos do Noticia para a página
  * @param Request $request
  * @param Pagination $obPagination
  * @return string
  */
  public static function getEventosCapa($request,$obPagination){

    $itens = '';

   //RESULTADO DA PAGINA
   $results = EntityEvento::getEventos(null,'codigo desc',40);

   //MONTA E RENDERIZA OS ITENS DE Noticia
   while($objEvento = $results->fetchObject(EntityEvento::class)){
     $itens .= View::render('pages/evento/item',[
       'codigo' => $objEvento->codigo ?? '',
       'nome' => $objEvento->nome ?? '',
       'local' => $objEvento->local ?? ''
    ]);
  }
  //CONTEÚDO DA HOME
  $content = View::render('pages/site',[
    'titulo' => 'Eventos',
    'descricao' => '',
    'conteudo' => $itens,
    'menu' => 'menu'
  ]);
  //RETORNA A PÁGINA COMPLETA
  return parent::getPrincipal('EMERJ - Novo Site',$content,'eventos');
}


/**
 * Método responsável por retornar o conteúdo (view) da nossa home
 * @param string
 */
public static function getLicitacaoDetalhe($request,$id){

  $objLicitacao = EntityLicitacao::getLicitacaoPorId($id);

  $content = View::render('/pages/licitacao/detalhe',[
    'codigo' => $objLicitacao->codigo ?? '',
    'nome' => $objLicitacao->nome ?? '',
    'local' => $objLicitacao->local ?? ''
  ]);
  //RETORNA A VIEW DA PÁGINA
  return parent::getPrincipal('EMERJ - Novo Site',$content,'licitacoes');
}

/**
 * Método responsável por obter a renderização das Conteúdos do Noticia para a página
 * @param Request $request
 * @param Pagination $obPagination
 * @return string
 */
 public static function getLicitacoesCapa($request,$obPagination){

   $itens = '';

  //RESULTADO DA PAGINA
  $results = EntityLicitacao::getLicitacoes(null,'codigo desc',40);

  //MONTA E RENDERIZA OS ITENS DE Noticia
  while($objLicitacao = $results->fetchObject(EntityLicitacao::class)){
    $itens .= View::render('pages/licitacao/item',[
      'codigo' => $objLicitacao->codigo ?? '',
      'nome' => $objLicitacao->nome ?? '',
      'local' => $objLicitacao->local ?? ''
   ]);
 }
 //CONTEÚDO DA HOME
 $content = View::render('pages/site',[
   'titulo' => 'Licitações',
   'descricao' => '',
   'conteudo' => $itens,
   'menu' => 'menu'
 ]);
 //RETORNA A PÁGINA COMPLETA
 return parent::getPrincipal('EMERJ - Novo Site',$content,'licitacoes');
}


}
