<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 05.12.2016
 * Time: 1:52
 */
?>


<link type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet"/>
<!--<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>-->
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
<script>
    var calculationType=0;
    $(document).ready(function () {
        $("#metricsVariants").change(function () {
            var variantId = "metrics";
            $(this).find("option:selected").each(function () {
                variantId = $(this).attr('value');
            });
            console.log(variantId);
            if ("metrics" == variantId) {
                $("#id_volume").addClass("hidden");
                $("#cargoVolume").removeAttr("required");

                $("#id_cargoMetrics").removeClass("hidden");
                $("#cargoLength").attr("required", "required");
                $("#cargoHeight").attr("required", "required");
                $("#cargoWidth").attr("required", "required");

                calculationType=0;
            } else {
                $("#id_cargoMetrics").addClass("hidden");
                $("#id_volume").removeClass("hidden");
                $("#cargoVolume").attr("required", "required");
                calculationType=1;

                $("#cargoLength").removeAttr("required");
                $("#cargoHeight").removeAttr("required");
                $("#cargoWidth").removeAttr("required");
            }
        });


        $("#cityTo").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '//api.cdek.ru/city/getListByTerm/jsonp.php?callback=?',
                    dataType: 'jsonp',
                    data: {
                        q: function () {
                            return $('#cityTo').val()
                        },
                        name_startsWith: function () {
                            return $('#cityTo').val()
                        }
                    },
                    success: function (data) {
                        response($.map(data.geonames, function (item) {
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
            select: function (event, ui) {
                console.log('City id: ' + ui.item.id);
                $("#cityFromId").val(ui.item.id);

            }
        });


        $('form').on('submit', function(e){
            e.preventDefault();

            console.log("Calculating...");

            var weight = $("#cargoWeight").val();
            if(!weight){
                console.log("Cargo weight not specified.");
                return;
            }


            weight=parseFloat(weight);

            var cityId = $("#cityFromId").val();
            if(cityId){
                if(calculationType==0){
                    console.log("Prepare for calculation with metrics...");
                    var length = $("#cargoLength").val();
                    var width = $("#cargoWidth").val();
                    var height = $("#cargoHeight").val();
                    if(length&&width&&height){
                        console.log("Calculating delivery price for metrics ["+width+","+length+","+height+"] and weight = "+weight+"...");

                        $.ajax({
                            url: "/wp-content/plugins/wp-plg-sdek-calculator/calc.php",
                            data:{
                                cityTo:cityId,
                                weight:weight,
                                type:'s',
                                l:length,
                                w:width,
                                h:height
                            },
                            dataType: "json"

                        }).success(function(data) {
                            processServerResponse(data);
                        });


                    }else {
                        console.log("Not all cargo metrics has been specified");
                    }
                }else {
                    console.log("Prepare for calculation with volume...");
                    var volume = $("#cargoVolume").val();
                    if(volume){
                        console.log("Calculating delivery price for volume = "+volume+" and weight = "+weight+"...");
                        $.ajax({
                            url: "/wp-content/plugins/wp-plg-sdek-calculator/calc.php",
                            data:{
                                cityTo:cityId,
                                weight:weight,
                                type:'v',
                                volume:volume
                            },
                            dataType: "json"

                        }).success(function(data) {
                            processServerResponse(data);
                        });
                    }else {
                        console.log("Cargo volume has not been specified");
                    }

                }
            }else{
                console.log("City 'from' id not specified.");
            }
        });
    });
    function processServerResponse(data) {
        if (data['last_error']) {
            $("#calculationErrorText").html("("+data['last_error'] + ") " + data['last_error_text']);
            $("#id_calculationError").removeClass("hidden");

            $("#id_calculationResult").addClass("hidden");
        } else {
            $("#calculationResultDate").html(data['date_min'] + " - " + data['date_max']);
            $("#calculationResultPrice").html(data['price']);
            $("#id_calculationResult").removeClass("hidden");

            $("#id_calculationError").addClass("hidden");
        }
    }
</script>

<form class="jotform-form" style="margin-left: 20%">
    <input type="hidden" id="cityFromId">
    <style scoped>
        .hidden {
            display: none!important;
        }
    </style>

    <div class="form-all">
        <ul class="form-section page-section">
            <li id="cid_18" class="form-input-wide" data-type="control_head">
                <div class="form-header-group">
                    <div class="header-text httal htvam">
                        <h1 id="header_18" class="form-header">
                            Рассчёт стоимости доставки
                        </h1>
                        <div id="subHeader_18" class="form-subHeader">
                            Пожалуйста, укажите параметры вашего отправления и мы сможем рассчитать его стоимость.
                        </div>
                    </div>
                </div>
            </li>


            <li class="form-line" id="id_cargoWeight">
                <label class="form-label form-label-top form-label-auto" id="cargoParametersWeight"> Основные
                    параметры </label>

                <div class="form-input-wide jf-required">
                <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="text" size="30" name="cityTo" id="cityTo" required="required">
            <label class="form-sub-label" for="cityTo" id="sublabel_cityTo" style="min-height: 13px;">
                Город, куда требуется выполнить доставку </label>
          </span>
                </div>


                <div class="form-input-wide jf-required">

          <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="number" size="10" name="cargoWeight" id="cargoWeight" min="1" max="100" required="required">
            <label class="form-sub-label" for="cargoWeight" id="sublabel_Weight" style="min-height: 13px;">
                Вес, кг </label>
          </span>


                    <span class="form-sub-label-container" style="vertical-align: top;">
            <select class="form-dropdown" style="width:150px;" id="metricsVariants">
                <option value="metrics"> Укажу метрики</option>
                <option value="volume"> Укажу объём</option>
            </select>
            <label class="form-sub-label" for="metricsVariants" style="min-height: 13px;"> Объём или метрики? </label>
          </span>
                </div>
            </li>

            <li class="form-line" id="id_cargoMetrics">
                <label class="form-label form-label-top form-label-auto" id="cargoParameters"> Габариты
                    отправления </label>
                <div class="form-input-wide jf-required">

          <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="number" size="10" name="cargoLength" id="cargoLength" min="1" required="required">
            <label class="form-sub-label" for="cargoLength" id="sublabel_cargoLength" style="min-height: 13px;">
                Длина, см </label>
          </span>
          <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="number" size="10" name="cargoWidth" id="cargoWidth" min="1" required="required">
            <label class="form-sub-label" for="cargoWidth" id="sublabel_cargoWidth" style="min-height: 13px;">
                Ширина, см </label>
          </span>
          <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="number" size="10" name="cargoHeight" id="cargoHeight" min="1" required="required">
            <label class="form-sub-label" for="cargoHeight" id="sublabel_cargoHeight" style="min-height: 13px;">
                Высота, см </label>
          </span>
                </div>

            </li>

            <li class="form-line hidden" id="id_volume">
                <label class="form-label form-label-top form-label-auto" id="cargoVolumeParameters"> Объём
                    отправления </label>
                <div class="form-input-wide jf-required">

          <span class="form-sub-label-container" style="vertical-align: top;">
            <input class="form-textbox" type="text" size="30" name="cargoVolume" id="cargoVolume">
            <label class="form-sub-label" for="cargoVolume" id="sublabel_cargoVolume" style="min-height: 13px;">
                Объём, м3 </label>
          </span>
                </div>

            </li>

            <li class="form-line" data-type="control_button" id="calculateSection">
                <div class="form-input-wide">
                    <div class="form-buttons-wrapper">
                        <button id="calculate" type="submit" class="form-submit-button" data-component="button">
                            Рассчитать
                        </button>
                    </div>
                </div>
            </li>

            <li class="form-line hidden" id="id_calculationResult">
                <label class="form-label form-label-top form-label-auto"> Результаты рассчёта</label>

                <div class="form-input-wide">
                    <Label class="form-label form-label-auto">Ожидаемые даты доставки: <em id="calculationResultDate"></em></Label>

                </div>

                <div class="form-input-wide">
                    <Label class="form-label form-label-auto">Стоимость доставки: <em id="calculationResultPrice"></em> руб.</Label>
                </div>
            </li>

            <li class="form-line hidden" id="id_calculationError">
                <label class="form-label form-label-top form-label-auto"> Ошибка подсчёта</label>

                <div class="form-input-wide">
                    <Label class="form-label form-label-auto" id="calculationErrorText"></Label>
                </div>
            </li>
        </ul>
    </div>
</form>
