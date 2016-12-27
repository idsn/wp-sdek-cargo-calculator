<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 02.12.16
 * Time: 19:00
 */
defined('ABSPATH') or die("No script kiddies please!");
?>

<style>
    .sdek_wrap .title {
        width: 540px;

    }

    .sdek_wrap input {
        min-width: 400px;
    }
</style>

<div class="sdek_wrap">

    <h1>Настройки калькулятора доставки грузов через СДЭК</h1>

    <h2>Основные настройки</h2>

    <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="page_options"
               value="sdek_authLogin,sdek_authPassword,sdek_dateExecuteShift,sdek_senderCityId,sdek_modeId,sdek_tariffList,sdek_tariffDefault"/>


        <table style="clear: both;float: left">
            <tr>
                <td class="title"><strong>Логин, выдается компанией СДЭК по вашему запросу:</strong></td>
                <td><input type="text" class="form-control" required="required" placeholder="Строковое значение"
                           name="sdek_authLogin" value="<?php echo get_option('sdek_authLogin'); ?>"></td>
            </tr>

            <tr>
                <td class="title"><strong>Пароль, выдается компанией СДЭК по вашему запросу:</strong></td>
                <td><input type="text" class="form-control" required="required" placeholder="Строковое значение"
                           name="sdek_authPassword" value="<?php echo get_option('sdek_authPassword'); ?>"></td>
            </tr>

            <tr>
                <td class="title"><strong>Смещение в днях относительно текущего, для которого выполняется
                        рассчёт:</strong></td>
                <td><input type="number" min="1" class="form-control" required="required"
                           placeholder="Положительное, целочисленное"
                           name="sdek_dateExecuteShift" value="<?php echo get_option('sdek_dateExecuteShift'); ?>"></td>
            </tr>

            <tr>
                <td class="title"><strong>Код города-отправителя в соответствии с кодами городов, предоставляемых
                        компанией СДЭК:</strong></td>
                <td><input type="number" min="0" class="form-control" required="required"
                           placeholder="Положительное, целочисленное"
                           name="sdek_senderCityId" value="<?php echo get_option('sdek_senderCityId'); ?>"></td>
            </tr>

            <tr>
                <td class="title"><strong>Выбранный режим доставки. Выбирается из предоставляемого СДЭК списка:</strong>
                </td>
                <td><input type="number" min="0" class="form-control" required="required"
                           placeholder="Положительное, целочисленное"
                           name="sdek_modeId" value="<?php echo get_option('sdek_modeId'); ?>"></td>
            </tr>

            <tr>
                <td class="title"><strong>Тариф по умолчанию. Выбирается из предоставляемого СДЭК перченя:</strong>
                </td>
                <td><input type="number" min="0" class="form-control" required="required"
                           placeholder="Положительное, целочисленное"
                           name="sdek_tariffDefault" value="<?php echo get_option('sdek_tariffDefault'); ?>"></td>
            </tr>
        </table>

        <div style="clear: both;float: left;margin-top: 20px;">
            <h2>Список тарифов доставки.</h2>
            <p style="font-style: italic;max-width: 950px">
                Каждый тариф указывается с новой строки, без пустых строк. Каждая строка в формате
                "приоритет=ид_тарифа",
                где "приоритет" - это целочисленное значение, чем меньше - тем выше приоритет, а ид_тарифа - это
                соответствующий идентификатор тарифа доставки СДЭК.
            </p>
        <textarea title="Список тарифов"
                  name="sdek_tariffList"
                  style="width: 950px;height: 300px"><?php echo get_option('sdek_tariffList'); ?></textarea>
        </div>

        <div style="clear: both;float: left;margin-top: 20px">
            <button type="submit" class="btn btn-default"
                    style="margin-top: 15px;margin-bottom:15px;padding-top: 5px;padding-bottom: 5px;">Сохранить
                изменения параметров
            </button>
        </div>
    </form>

<div style="float: left;clear: both">
    <h2>Пример подгрузки списка городов</h2>
<pre>
	$(function() {
	  $(&quot;#city&quot;).autocomplete({
	    source: function(request,response) {
	      $.ajax({
	        url: &quot;http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?&quot;,
	        dataType: &quot;jsonp&quot;,
	        data: {
	        	q: function () { return $(&quot;#city&quot;).val() },
	        	name_startsWith: function () { return $(&quot;#city&quot;).val() }
	        },
	        success: function(data) {
	          response($.map(data.geonames, function(item) {
	            return {
	              label: item.name,
	              value: item.name,
	              id: item.id
	            }
	          }));
	        }
	      });
	    },
	    minLength: 1,
	    select: function(event,ui) {
	    	//console.log(&quot;Yep!&quot;);
	    	$('#receiverCityId').val(ui.item.id);
	    }
	  });
	});
</pre>
</div>
</div>