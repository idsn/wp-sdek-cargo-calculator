<?php
/**
 * @package denlans_ru
 */
/*
Plugin Name: Калькулятор доставки грузов через СДЭК
Plugin URI: http://denlans.ru/
Description: Для вставки калькулятора используйте шорткод [sdek-calculator]
Version: 1.0.0
Author: Denis Ryabikov
Author URI: http://denlans.ru/
Text Domain: wp-plg-sdek-calculator
*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

function calcForm() {
	include "shortTag.php";
}
/** @noinspection PhpUndefinedFunctionInspection */
add_shortcode( 'sdek-calculator', 'calcForm' );

/** @noinspection PhpUndefinedFunctionInspection */
add_action( 'admin_menu', 'sdekCalculatorAction' );


function sdekCalculatorAction() {
    /** @noinspection PhpUndefinedFunctionInspection */
    add_options_page('Настройки плагина калькулятора доставки грузов через СДЭК',
        'Калькулятор доставки грузов через СДЭК', 'edit_pages', 'wp-plg-sdek-calculator', 'sdekCalculatorOptions');
}


function sdekCalculatorOptions() {
    /** @noinspection PhpUndefinedFunctionInspection */
    if (!current_user_can('edit_pages')) {
        /** @noinspection PhpUndefinedFunctionInspection */
        wp_die( __( 'У вас надостаточно прав для изменения настроек калькулятора доставки грузов через СДЭК.' ) );
	}
    include "admin/sdek-calculator-options.php";
}
?>