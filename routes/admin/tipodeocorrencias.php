<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE TIPOS DE OCORRÊNCIAS
$obRouter->get('/admin/tipodeocorrencias',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodeocorrencias::getListTipodeocorrencias($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO TIPOS DE OCORRÊNCIAS
$obRouter->get('/admin/tipodeocorrencias/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodeocorrencias::getNovoTipodeocorrencia($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO TIPOS DE OCORRÊNCIAS (POST)
$obRouter->post('/admin/tipodeocorrencias/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Tipodeocorrencias::setNovoTipodeocorrencia($request));
  }
]);

//ROTA DE EDIÇÃO DE UM TIPOS DE OCORRÊNCIAS
$obRouter->get('/admin/tipodeocorrencias/{tipodeocorrencia_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::getEditTipodeocorrencia($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE EDIÇÃO DE UM TIPOS DE OCORRÊNCIAS (POST)
$obRouter->post('/admin/tipodeocorrencias/{tipodeocorrencia_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::setEditTipodeocorrencia($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM TIPO DE OCORRÊNCIA
$obRouter->get('/admin/tipodeocorrencias/{tipodeocorrencia_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::getDeleteTipodeocorrencia($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM TIPO DE OCORRÊNCIA VIA MODAL
$obRouter->get('/admin/tipodeocorrencias/{tipodeocorrencia_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::getDeleteTipodeocorrenciaModal($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM TIPO DE OCORRÊNCIA (POST)
$obRouter->post('/admin/tipodeocorrencias/{tipodeocorrencia_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::setDeleteTipodeocorrencia($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM TIPO DE OCORRÊNCIA
$obRouter->get('/admin/tipodeocorrencias/{tipodeocorrencia_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::getAltStatusTipodeocorrenciaModal($request,$tipodeocorrencia_id));
  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM TIPO DE OCORRÊNCIA (POST)
$obRouter->post('/admin/tipodeocorrencias/{tipodeocorrencia_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$tipodeocorrencia_id){
    return new Response(200,Admin\Tipodeocorrencias::setAltStatusTipodeocorrencia($request,$tipodeocorrencia_id));
  }
]);
