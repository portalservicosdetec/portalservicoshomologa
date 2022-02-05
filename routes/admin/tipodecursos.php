<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE TIPO DE CURSOS
$obRouter->get('/admin/tipodecursos',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodecursos::getListTipodecursos($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO TIPO DE CURSOS
$obRouter->get('/admin/tipodecursos/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodecursos::getNovoTipodecurso($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO TIPO DE CURSOS (POST)
$obRouter->post('/admin/tipodecursos/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodecursos::setNovoTipodecurso($request));
  }
]);

//ROTA DE EDIÇÃO DE UM TIPO DE CURSOS
$obRouter->get('/admin/tipodecursos/{tipodecurso_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::getEditTipodecurso($request,$tipodecurso_id));
  }
]);

//ROTA DE EDIÇÃO DE UM TIPO DE CURSOS (POST)
$obRouter->post('/admin/tipodecursos/{tipodecurso_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::setEditTipodecurso($request,$tipodecurso_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM TIPO DE CURSOS
$obRouter->get('/admin/tipodecursos/{tipodecurso_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::getDeleteTipodecurso($request,$tipodecurso_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM TIPO DE CURSOS (POST)
$obRouter->post('/admin/tipodecursos/{tipodecurso_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::setDeleteTipodecurso($request,$tipodecurso_id));
  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM TIPO DE CURSOS (GET)
$obRouter->get('/admin/tipodecursos/{tipodecurso_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::setAltStatusTipodecurso($request,$tipodecurso_id));

  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM TIPO DE CURSOS (POST)
$obRouter->post('/admin/tipodecursos/{tipodecurso_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodecurso_id){
    return new Response(200,Admin\Tipodecursos::setAltStatusTipodecurso($request,$tipodecurso_id));

  }
]);
