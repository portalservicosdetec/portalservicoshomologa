<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE REQUERIMENTOS
$obRouter->get('/admin/requerimentos',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Requerimentos::getListRequerimentos($request));
  }
]);

//ROTA DE CADASTRO DE UM NOVO REQUERIMENTO
$obRouter->get('/admin/requerimentos/novo/{{chamado_id}}',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$chamado_id){
    return new Response(200,Admin\Requerimentos::getNovoRequerimento($request,$chamado_id));
  }
]);

//ROTA DE CADASTRO DE UM NOVO REQUERIMENTO (POST)
$obRouter->post('/admin/requerimentos/novo',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request){
    return new Response(200,Admin\Requerimentos::setNovoRequerimento($request));
  }
]);

//ROTA DE EDIÇÃO DE UM REQUERIMENTO
$obRouter->get('/admin/requerimentos/{requerimento_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$requerimento_id){
    return new Response(200,Admin\Requerimentos::getEditRequerimento($request,$requerimento_id));
  }
]);

//ROTA DE EDIÇÃO DE UM REQUERIMENTO (POST)
$obRouter->post('/admin/requerimentos/{requerimento_id}/edit',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$requerimento_id){
    return new Response(200,Admin\Requerimentos::setEditRequerimento($request,$requerimento_id));
  }
]);

//ROTA DE EXCLUSÃO DE UM REQUERIMENTO (POST)
$obRouter->get('/admin/requerimentos/{requerimento_id}/delete',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$requerimento_id){
    return new Response(200,Admin\Requerimentos::getDeleteRequerimento($request,$requerimento_id));
  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM REQUERIMENTO
$obRouter->get('/admin/requerimentos/{requerimento_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$requerimento_id){
    return new Response(200,Admin\Requerimentos::getAltStatusRequerimentoModal($request,$requerimento_id));
  }
]);

//ROTA DE ALTERAÇÂO DE STATUS DE UM REQUERIMENTO (POST)
$obRouter->post('/admin/requerimentos/{requerimento_id}/alterastatus',[
  'middlewares' => [
    'require-admin-login'
  ],
  function($request,$requerimento_id){
    return new Response(200,Admin\Requerimentos::setAltStatusRequerimento($request,$requerimento_id));
  }
]);
