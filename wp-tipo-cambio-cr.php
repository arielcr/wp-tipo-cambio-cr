<?php

/**
 * Plugin Name:       Tipo de Cambio Costa Rica 
 * Plugin URI:        http://arielorozco.com/tutoriales/obtener-tipo-de-cambio-colones-dolares-en-wordpress/
 * Description:       Obtiene el tipo de cambio del dia (colones/dolares) del BCCR en Costa Rica.
 * Version:           1.0.0
 * Author:            Ariel Orozco
 * Author URI:        http://arielorozco.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-tipo-cambio-cr
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constantes de tipo de cambio
define('WPTCR_COMPRA_CR', 317);
define('WPTCR_VENTA_CR', 318);

// URL del WebService
define('WPTCR_IND_ECONOM_WS', "http://indicadoreseconomicos.bccr.fi.cr/indicadoreseconomicos/WebServices/wsIndicadoresEconomicos.asmx");

// Metodo que se va a utilizar del WebService
define('WPTCR_IND_ECONOM_METH', "ObtenerIndicadoresEconomicosXML");

// Numero de horas para actualizar los valores
define('WPTCR_TIME_LIMIT', 4);

/**
 * Obtiene el tipo de cambio del dia
 *
 * @param string $tipo Tipo de cambio deseado (COMPRA/VENTA/EURO)
 * @param string $fecha Fecha del tipo de cambio deseado
 * @return float Valor del tipo de cambio
 */
function wptcr_tipo_cambio($tipo = "", $fecha = "", $retorno = false){
    date_default_timezone_set('America/Costa_Rica');
    $fecha_tc = empty($fecha) ? date("d/m/Y") : $fecha;
    $fecha_actual = date('Y-m-d H:i:s');

    if(empty($tipo)){
        $tipo_tc = WPTCR_COMPRA_CR;
    } else if($tipo === "COMPRA"){
        $tipo_tc = WPTCR_COMPRA_CR;
    } else if($tipo === "VENTA"){
        $tipo_tc = WPTCR_VENTA_CR;
    } else {
        $tipo_tc = WPTCR_COMPRA_CR;
    }

    $tipo_cambio = 0;
    $url_ws = WPTCR_IND_ECONOM_WS . "/" . WPTCR_IND_ECONOM_METH . "?tcIndicador=" . $tipo_tc . "&tcFechaInicio=" . $fecha_tc . "&tcFechaFinal=" . $fecha_tc . "&tcNombre=tq&tnSubNiveles=N";

    if($tipo_tc == WPTCR_COMPRA_CR){
        $wptcr_compra_cr = get_option('WPTCR_COMPRA_CR');

        if(!$wptcr_compra_cr){
            $tipo_cambio = wptcr_get_bccr_service_data($url_ws); 
            $op_compra = $tipo_cambio . '|' . $fecha_actual;
            add_option('WPTCR_COMPRA_CR', $op_compra);
        } else {
            $opts = explode('|', $wptcr_compra_cr);
            $diff = date_diff(date_create($opts[1]), date_create($fecha_actual));
            $tipo_cambio = $opts[0];

            if($diff->h >= WPTCR_TIME_LIMIT){
                $tipo_cambio = wptcr_get_bccr_service_data($url_ws); 
                $op_compra = $tipo_cambio . '|' . $fecha_actual;
                update_option('WPTCR_COMPRA_CR', $op_compra);
            }
        }

    } else if($tipo_tc == WPTCR_VENTA_CR){
        $wptcr_venta_cr = get_option('WPTCR_VENTA_CR');

        if(!$wptcr_venta_cr){
            $tipo_cambio = wptcr_get_bccr_service_data($url_ws); 
            $op_venta = $tipo_cambio . '|' . $fecha_actual;
            add_option('WPTCR_VENTA_CR', $op_venta);
        } else {
            $opts = explode('|', $wptcr_venta_cr);
            $diff = date_diff(date_create($opts[1]), date_create($fecha_actual));
            $tipo_cambio = $opts[0];

            if($diff->h >= WPTCR_TIME_LIMIT){
                $tipo_cambio = wptcr_get_bccr_service_data($url_ws); 
                $op_venta = $tipo_cambio . '|' . $fecha_actual;
                update_option('WPTCR_VENTA_CR', $op_venta);
            }
        }
    }

    if(!$retorno) 
        echo (float)$tipo_cambio;
    else
        return (float)$tipo_cambio;
}

 /**
 * Convierte un monto de colones a dolares
 *
 * @param $monto El monto a convertir
 * @return float El monto convertido
 */
function wptcr_convertir_colones_dolares($monto) {
    $tc_venta = wptcr_tipo_cambio(WPTCR_VENTA_CR, '', true);
    echo ($tc_venta > 0) ? number_format(($monto / $tc_venta), 2, '.', '') : 0;
}

/**
 * Convierte un monto de dolares a colones
 *
 * @param $monto El monto a convertir
 * @return float El monto convertido
 */
function wptcr_convertir_dolares_colones($monto) {
    $tc_compra = wptcr_tipo_cambio(WPTCR_COMPRA_CR, '', true);
    echo ($tc_compra > 0) ? number_format(($monto * $tc_compra), 2, '.', '') : 0;
}

/**
 * Obtiene datos por CURL
 * @param  string $Url Url del webservice
 * @return $output Respuesta del webservice
 */
function wptcr_get_bccr_service_data($url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    $xml = simplexml_load_string($output);
    $tc = trim(strip_tags(substr($xml, strpos($xml, "<NUM_VALOR>"), strripos($xml, "</NUM_VALOR>"))));
    $tc_format = number_format($tc, 2);

    return $tc_format;
}

/**
 * SHORTCODES
 *
 */
function wptcr_tipo_cambio_compra_shortcode() {
    ob_start();  
    wptcr_tipo_cambio('COMPRA');
    return ob_get_clean();
}
add_shortcode('WPTCR_TIPO_CAMBIO_COMPRA', 'wptcr_tipo_cambio_compra_shortcode');

function wptcr_tipo_cambio_venta_shortcode() {
    ob_start();  
    wptcr_tipo_cambio('VENTA');
    return ob_get_clean();
}
add_shortcode('WPTCR_TIPO_CAMBIO_VENTA', 'wptcr_tipo_cambio_venta_shortcode');

function wptcr_convertir_colones_dolares_shortcode($atts) {
    extract(shortcode_atts(array(
        'monto' => '100'
    ), $atts));
    ob_start();  
    wptcr_convertir_colones_dolares($monto);
    return ob_get_clean();
}
add_shortcode('WPTCR_CONVERTIR_COLONES_DOLARES', 'wptcr_convertir_colones_dolares_shortcode');

function wptcr_convertir_dolares_colones_shortcode($atts) {
    extract(shortcode_atts(array(
        'monto' => '100'
    ), $atts));
    ob_start();  
    wptcr_convertir_dolares_colones($monto);
    return ob_get_clean();
}
add_shortcode('WPTCR_CONVERTIR_DOLARES_COLONES', 'wptcr_convertir_dolares_colones_shortcode');

