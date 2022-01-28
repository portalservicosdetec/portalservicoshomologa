<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE CURSOS
$obRouter->get('/admin/cursos',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Cursos::getListCursos($request));
  }
]);

//ROTA DE UPLOAD TEMPORÁRIO DE ARQUIVOS
$obRouter->post('/admin/cursos/uploadajax',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Cursos::setUploadAjax($request));
  }
]);

//ROTA DE UPLOAD TEMPORÁRIO DE ARQUIVOS
$obRouter->post('/admin/cursos/removeuploadajax',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Cursos::setRemoveUploadAjax($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO CURSO
$obRouter->get('/admin/cursos/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Cursos::getNovoCurso($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO CURSO (POST)
$obRouter->post('/admin/cursos/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Cursos::setNovoCurso($request));
  }
]);

//ROTA DE EDIÇÃO DE UM CURSO
$obRouter->get('/admin/cursos/{curso_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$curso_id){
    return new Response(200,Admin\Cursos::getEditCurso($request,$curso_id));
  }
]);

//ROTA DE EDIÇÃO DE UM CURSO (POST)
$obRouter->post('/admin/cursos/{curso_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$curso_id){
    return new Response(200,Admin\Cursos::setEditCurso($request,$curso_id));
  }
]);

//ROTA DE ALTERAÇÃO DE STATUS DE UM CURSO
$obRouter->get('/admin/cursos/{curso_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$curso_id){
    return new Response(200,Admin\Cursos::getAltStatusCurso($request,$curso_id));
  }
]);


//ROTA DE EXCLUSÃO DE UM CURSO
$obRouter->get('/admin/cursos/{curso_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$curso_id){
    return new Response(200,Admin\Cursos::getDeleteCurso($request,$curso_id));
  }
]);
