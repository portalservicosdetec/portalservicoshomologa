<?php

namespace App\Utils;

class View{

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
