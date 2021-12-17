<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 2/8/2021
 * Time: 3:59 PM
 */

namespace App\Util;


class SlugMaker
{
    static function obtenerSlug($cadena, $separador = '-'){
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);

        return $slug;

    }
}