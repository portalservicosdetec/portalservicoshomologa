<?php

namespace App\Utils;

use \App\Utils\Environment;

ini_set('default_charset', 'utf-8');

//GARREGA AS VARIÁVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

//DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS

define('SECURITY_KEY_',getenv('VAR_SECURITY_KEY'));
define('SECURITY_IV_',getenv('VAR_SECURITY_IV'));
define('SECURITY_METHOD_',getenv('VAR_SECURITY_METHOD'));


class View{

  const SECURITY_KEY = SECURITY_KEY_;
  const SECURITY_IV = SECURITY_IV_;
  const SECURITY_METHOD = SECURITY_METHOD_;

  /**
   * Variáveis padrÕES da view
   * @var array
   */
  private static $vars = [];

  /**
   * Método responsável por definir os dados iniciais da classe
   * @param array $vars
   */
  public static function init($vars = []){
    self::$vars = $vars;
  }


  // FUNÇÃO PARA LIMITAR A QUANTIDADE DE CARACTERES ATE O PRÓXIMO ESPAÇO
  public static function limitCharacter($string,$srtValor,$ini,$tam){

    $regex = '/.{'.$ini.','.$tam.'}('.$srtValor.'*|$)/';
    preg_match_all($regex, $string, $matches);
    $result = array_shift($matches[0]);
    return $result;
  }

// FUNÇÃO PARA LIMITAR A QUANTIDADE DE CARACTERES ATE O PRÓXIMO ESPAÇO
public static function firstName($name){
  $array = explode(" ",$name);
  return $array[0];
}

  /**
   * Método responsável por retornar o conteúdo de uma view
   * @param string $view
   * @return string
   */
  private static function getContentView($view){
    $file = __DIR__.'/../../resources/view/'.$view.'.html';
    //echo "<pre>";    print_r($file);    echo "</pre>"; //exit;
    return file_exists($file) ? file_get_contents($file) : '';
  }

  public static function crypt($action, $string)
 {
     /* =================================================
      * ENCRYPTION-DECRYPTION
      * =================================================
      * ENCRYPTION: encrypt_decrypt('encrypt', $string);
      * DECRYPTION: encrypt_decrypt('decrypt', $string) ;
      */

     //echo "<pre>";    print_r(self::HOST);    echo "</pre>";
     //echo "<pre>";    print_r(self::SECURITY_KEY);    echo "</pre>"; exit;


     $output = false;
     // hash
     $key = hash('sha256', self::SECURITY_KEY);
     // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
     $iv = substr(hash('sha256', self::SECURITY_IV), 0, 16);
     if ($action == 'encrypt') {
         $output = base64_encode(openssl_encrypt($string, self::SECURITY_METHOD, $key, 0, $iv));
     } else {
         if ($action == 'decrypt') {
             $output = openssl_decrypt(base64_decode($string), self::SECURITY_METHOD, $key, 0, $iv);
         }
     }
     return $output;
 }

  /**
   * Método responsável por retornar o conteúdo renderizado de uma view
   * @param string $view
   * @param array $rars (string/numeric)
   * @return string
   */
  public static function render($view, $vars = []){
    // CONTEÚDO DA VIEW
    $contentView = self::getContentView($view);

    //MERGE DE VARIAVEIS DA VIEW
    $vars = array_merge(self::$vars,$vars);

    //DESCUBRIR AS CHAVES DO ARRAY DE VARIÁVEIS
    $keys = array_keys($vars);
    $keys = array_map(function($item){
      return '{{'.$item.'}}';
    },$keys);

  //  echo "<pre>";    print_r($keys);    echo "</pre>";
  //  echo "<pre>";    print_r($vars);    echo "</pre>";
    //RETORNA O CONTEÚDO RENDERIZADO
    return str_replace($keys,array_values($vars),$contentView);
  }
}
