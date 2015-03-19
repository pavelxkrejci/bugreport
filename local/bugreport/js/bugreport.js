/*
 * bugreport.js
 * verze 1.1
 *
 * Uzivatelske hlaseni chyb
 *
 * modified by uint for MU, Jan 2015
 *
 */

$(document).ready(function() {

    var browser_info = $.browser.name + ' ' + $.browser.fullVersion;
    var default_content_block = '#page';                            // Defaultni obalovy blok
    var ajaxScript = wwwroot + '/local/bugreport/ajax/ajax.php';    // Cesta k defaultnimu ajax scriptu

    var screenRes = window.screen.width + 'x' + window.screen.height;
    var browserRes = $(window).width() + 'x' + $(window).height();

    var color_scheme;

    if (!window.getComputedStyle) {
        window.getComputedStyle = function(el, pseudo) {
            this.el = el;
            this.getPropertyValue = function(prop) {
                var re = /(\-([a-z]){1})/g;
                if (prop == 'float')
                    prop = 'styleFloat';
                if (prop == 'data-content')
                    prop = 'content';
                if (re.test(prop)) {
                    prop = prop.replace(re, function() {
                        return arguments[2].toUpperCase();
                    });
                }
                return el.currentStyle[prop] ? el.currentStyle[prop] : null;
            }
            return this;
        }
    }

    /* Do tela stranek pridame potrebne DIVy */
    $('body').append('<div id="dialog-faq" title="' + js_header_faq + '"></div>');
    $('body').append('<div id="dialog-type" title="' + js_header_type + '"><p>' + js_type_desc + '</p></div>');
    $('body').append('<div id="dialog-simple" title="' + js_header_simple + '"><form><fieldset><textarea id="simple_report" placeholder="' + js_string_marker_hint + '"></textarea></fieldset></form></div>');
    $('body').append('<div id="dialog-screenshot" title="' + js_header_screenshot + '">' +
            '<div class="screenshot_introtext">' +
            js_screenshot_introtext +
            '</div>' +
            '<div id="screenshot_inner"></div>' +
            '</div>');
    $('body').append('<div id="dialog-form" title="' + js_header_dialog + '">' +
            '<form action="" id="report_form" method="POST" enctype="multipart/form-data">' +
            '<fieldset>' +
            '<label for="report_image">' + js_label_dialog_screenshot + '</label>' +
            '<div id="report_image"></div>' +
            '<label for="detailni_popis" title="' + js_string_field_required + '">' + js_label_dialog_description + '<span class="req_star"> *</span></label>' +
            '<textarea id="detailni_popis" name="detailni_popis" class="text ui-widget-content ui-corner-all" rows="5" cols="40"></textarea>' +
            '<input type="hidden" name="h_screenshot_image" id="h_screenshot_image" value="" />' +
            '<input type="hidden" name="h_screenshot_original_image" id="h_screenshot_original_image" value="" />' +
            '<input type="hidden" name="h_current_url" id="h_current_url" value="" />' +
            '<input type="hidden" name="h_browser" id="h_browser" value="" />' +
            '</fieldset>' +
            '</form>' +
            '</div>');
    $('body').append('<div id="screenshot_temp"></div>');
    $('body').append('<div id="screenshot_original_temp" style="display: none;"></div>');

    $('body').append('<button id="report-bug">Našli jste chybu</button>');

    /* Vypiseme si FAQ */
    getFAQ('#dialog-faq');

    /* Konfigurace tlacitka */
    configReportButton('#report-bug');

    /**
     * Funkce pro vypis FAQu pomoci AJAXu
     *
     * @param {type} appendTo
     * @returns {undefined}
     */
    function getFAQ(appendTo) {
        $.ajax({
            type: "POST",
            url: ajaxScript,
            data: {task: "getFaq"}
        }).done(function(data) {
            result = '<div id="faq-choice">' + js_faq_choice + '</div><div id="faq-content">' + $.parseJSON(data) + '</div>';            
            $(appendTo).append(result);
        });
    }

    /**
     * Konfigurace tlacitka pro odeslani chyby
     *
     * @param {type} button
     * @returns {undefined}
     */
    function configReportButton(button) {
        $.ajax({
            type: "POST",
            url: ajaxScript,
            data: {task: "configButton"}
        }).done(function(data) {
            result = $.parseJSON(data);

            result.pos === 'left' ? $(button).addClass('rb_left') : $(button).addClass('rb_right');
            if (result.colorScheme === 'style_default') {                
                /*
                $(button).addClass(result.icon);
                $(button).css('background-color', result.color);
                $('.ui-widget-header').css('background-color', result.color);
                $('.ui-dialog .ui-dialog-buttonpane').css('background-color', result.color);
                $('#screenshot_inner').css('border', '2px solid ' + result.color);
                $('#screenshot_inner').css('box-shadow', '0 0 10px ' + result.color);
                $('#screenshot_inner').css('-moz-box-shadow', '0 0 10px ' + result.color);
                $('#screenshot_inner').css('-webkit-box-shadow', '0 0 10px ' + result.color);
                $(button).css('color', result.fontColor);
                $('.ui-widget-header button').css('background-color', result.fontColor).css('color', '#fff');
                $('.ui-dialog .ui-dialog-buttonpane button').css('background-color', result.fontColor).css('color', '#fff');
                */
            } else {
                
                $(button).addClass(result.colorScheme);
                $('.ui-widget-header').addClass(result.colorScheme);
                $('.ui-dialog .ui-dialog-buttonpane').addClass(result.colorScheme);
                $('#screenshot_inner').addClass(result.colorScheme);

                color_scheme = result.colorScheme;
            }
            $(button).text(result.text);
            $(button).tooltip({
                track: true,
                position: {
                    my: "right bottom-5",
                    at: "right top",
                }
            });
            $(button).prop('title', result.title);
        });
    }

    /**
     * Funkce pro vytvoreni screenshotu
     *
     * @param {type} appendTo
     * @param {type} source
     * @param {type} width
     * @param {type} height
     * @returns {undefined}
     */
    function getScreenshot(appendTo, source, width, height) {
        var elem = default_content_block;
        if (source !== null) {
            elem = source;
        }

        width !== null ? width = ' width="' + width + '"' : width = ' width="100%"';
        height !== null ? height = ' height="' + height + '"' : height = '';

        if ($.browser.msie && (document.documentMode || 100) < 9) {
            // prohlizec je IE a jeho verze je nizzsi nez 8
            $(appendTo).addClass('browserError');
            $(appendTo).html('<img src="' + wwwroot + '/local/bugreport/pix/browserError.png" /><div class="browserError">' + js_string_browser_error + '</div>');
            return false;
        } else {
            // prohlizec je IE (verze 9 a vyssi), nebo jiny            
            $(elem).css('overflow', 'hidden').html2canvas({
                onrendered: function(canvas) {
                    $(appendTo).html('<img src="' + canvas.toDataURL("image/png") + '"' + width + height + ' />');
                }
            });
        }
    }

    /**
     * Dialog FAQ
     */
    $("#dialog-faq").dialog({
        autoOpen: false,
        height: 'auto',
        width: 350,
        maxWidth: $('body').width(),        
        modal: true,        
        resizable: false,
        draggable: true,        
        closeText: js_button_close,
        dialogClass: "bugrep bugrep-faq",
        closeOnEscape: true,
        buttons: [

            {
                text: js_button_showfaq,
                id: "bugrep-faq-show",
                click: function() {
                    $("#bugrep-faq-show").hide();
                    $("#faq-choice").hide();
                    $("#faq-content").show();
                }
            },

                {
                text: js_button_continue,
                id: "bugrep-faq-continue",
                click: function() {
                    $("#dialog-faq").dialog("close");                    
                    $("#dialog-type").dialog("open");
                }
            }
            ],
        open: function() {
            $('#faq-choice, #bugrep-faq-show').show();
            $('#faq-content').hide();
            $('.ui-dialog-buttonset button').removeAttr('disabled').removeClass('btnDisabled');
        },
        close: function() {
            $('#report-bug').blur();            
        }
    });

     /**
     * Dialog TYPE
     */
    $("#dialog-type").dialog({
        autoOpen: false,
        height: 'auto',
        width: 350,
        modal: true,
        resizable: false,
        draggable: true,
        closeText: js_button_close,
        dialogClass: "bugrep bugrep-type",
        closeOnEscape: true,
        buttons: [

            {
                text: js_button_type1,
                id: "bugrep-type1",
                click: function() {
                    $("#dialog-type").dialog("close");                    
                    $("#dialog-simple").dialog("open");
                }
            },

                {
                text: js_button_type2,
                id: "bugrep-type2",
                click: function() {
                    $("#dialog-type").dialog("close");
                    getScreenshot('#dialog-screenshot #screenshot_inner', null, null, null);                    
                    // Do docasneho divu si ulozime originalni screenshot
                    getScreenshot('#screenshot_original_temp', null, null, null);
                    $("#dialog-screenshot").dialog("open");
                }
            }
            ],
        open: function() {
            /*
            $('#faq-choice, #bugrep-faq-show').show();
            $('#faq-content').hide();
            $('.ui-dialog-buttonset button').removeAttr('disabled').removeClass('btnDisabled');
            */
        }
    });

    /**
     * Dialog SIMPLE
     */
    $("#dialog-simple").dialog({
        autoOpen: false,
        height: 'auto',
        width: 350,
        modal: true,
        resizable: false,
        draggable: false,
        closeText: js_button_close,
        dialogClass: "bugrep bugrep-simple",
        closeOnEscape: false,
        buttons: [

                {
                text: js_button_send,
                id: "bugrep-submit2",
                click: function() {
                    $("#dialog-simple").dialog("close");
                    $("#bugrep-submit").trigger("click");
                }
            }
            ],
        open: function() {
            /*
            $('#faq-choice, #bugrep-faq-show').show();
            $('#faq-content').hide();
            $('.ui-dialog-buttonset button').removeAttr('disabled').removeClass('btnDisabled');
            */
        }
    });

    $("#simple_report").autogrow();



    /**
     * Dialog SCREENSHOT
     */
    $("#dialog-screenshot").dialog({
        autoOpen: false,
        width: 'auto',
        height: 'auto',
        modal: true,
        resizable: false,
        draggable: false,
        closeText: js_button_close,
        dialogClass: "bugrep bugrep-scr",
        position: {
            my: 'top', at: 'top', of: 'body', /*within: $("#dialog-screenshot"),*/ collision: 'none'
        },
        closeOnEscape: false,
        buttons: [{
                text: js_button_send,
                click: function() {
                    getScreenshot('#dialog-form #report_image', '#dialog-screenshot #screenshot_inner', 350, null);

                    if ($.browser.msie && (document.documentMode || 100) < 9) {
                        $("#dialog-screenshot").dialog("close");
                        //$("#dialog-form").dialog("open");
                        $("#bugrep-submit").trigger("click");
                    } else {
                        $('#dialog-form #report_image').bind('DOMNodeInserted DOMNodeRemoved', function() {
                            $("#dialog-screenshot").dialog("close");
                            //$("#dialog-form").dialog("open");
                            $("#bugrep-submit").trigger("click");
                        });
                    }

                    /* Zneaktivneni tlacitka pro odeslani formulare v nasledujicim dialogu */
                    //$('.ui-dialog-buttonset button').attr('disabled', 'disabled').addClass('btnDisabled');
                }
            }],
        open: function(event, ui) {
            var dialog = $(this).closest('.ui-dialog');

            var originalButtons = $('.ui-dialog-buttonpane .ui-dialog-buttonset button', dialog)
            var clonedButtons = originalButtons.clone().addClass('continue-butt-top');
            $('.ui-dialog-titlebar span.ui-dialog-title', dialog).after(clonedButtons);

            $('button.continue-butt-top').click(function() {
                getScreenshot('#dialog-form #report_image', '#dialog-screenshot #screenshot_inner', 350, null);

                if ($.browser.msie && (document.documentMode || 100) < 9) {
                    $("#dialog-screenshot").dialog("close");
                    //$("#dialog-form").dialog("open");
                    $("#bugrep-submit").trigger("click");
                } else {
                    $('#dialog-form #report_image').bind('DOMNodeInserted DOMNodeRemoved', function() {
                        $("#dialog-screenshot").dialog("close");
                        //$("#dialog-form").dialog("open");
                        $("#bugrep-submit").trigger("click");
                    });
                }

                /* Zneaktivneni tlacitka pro odeslani formulare v nasledujicim dialogu */
                //$('.ui-dialog-buttonset button').attr('disabled', 'disabled').addClass('btnDisabled');
            });

            $('#bugrep-fallback').blur().click(function(e){                
                $("#dialog-screenshot").dialog("close");
                $("#dialog-simple").dialog("open");
                return false;
            });
        }
    });

    /**
     * Dialog ODESILACI FORMULAR
     */
    $("#dialog-form").dialog({
        autoOpen: false,
        height: 'auto',
        width: 380,
        modal: true,
        closeText: js_button_close,
        closeOnEscape: false,
        buttons: [{
                text: js_button_send,
                id: "bugrep-submit",
                click: function() {
                    $('input#h_screenshot_image').val($('#dialog-form #report_image img').attr('src'));
                    $('input#h_screenshot_original_image').val($('#screenshot_original_temp img').attr('src'));
                    $('input#h_current_url').val($(location).attr('href'));
                    $('input#h_browser').val(browser_info);
                    
                    $.ajax({
                        type: "POST",
                        url: ajaxScript,
                        data: {popis: $('#simple_report').val() + $('#screen_report').val(), screenshot: $('input#h_screenshot_image').val(), screenshot_original: $('input#h_screenshot_original_image').val(), aktualniUrl: $('input#h_current_url').val(), browser: $('input#h_browser').val(), task: 'getFormData', root: wwwroot, screen_res: screenRes, browser_res: browserRes},
                        success: function(data) {                            
                            document.write(data);
                        }
                    });
                    $(this).dialog("close");
                }
            }]
    });

    /**
     * Tlacitko pro zobrazeni dialogu
     */
    $("#report-bug").button().click(function() {
        $("#dialog-faq").dialog("open");
    });


    /* 
     * Kontrola, zda je vyplneno pole s detailnim popisem chyby 
     */
     
     /*
    $('textarea#detailni_popis').keyup(function() {
        if ($(this).val() !== '') {
            $('.ui-dialog-buttonset button').removeAttr('disabled', 'disabled');
            $('.ui-dialog-buttonset button').removeClass('btnDisabled')
        } else {
            $('.ui-dialog-buttonset button').attr('disabled', 'disabled');
            $('.ui-dialog-buttonset button').addClass('btnDisabled')
        }
    });
    */

    /**
     * Pridavani markeru do screenshotu
     */

    var is_ie_8_lower = false;
    if ($.browser.msie && (document.documentMode || 100) < 9) {
        is_ie_8_lower = true;
    }

    if (is_ie_8_lower === false) {
        $('#dialog-screenshot #screenshot_inner').bind('click', function(e) {
            var elementUnder = document.elementFromPoint(e.pageX, e.pageY);
            var isHover = $(elementUnder).attr('param');

            //if (isHover !== 'marker') {              
            if($(".marker").length == 0) {
                var newElem = document.createElement('div');
                $(newElem)
                    .addClass('marker ' + color_scheme)
                    .appendTo($(this))
                    .append('<div class="marker_name" param="marker"><div class="marker_head" param="marker">' + js_string_marker_name + '<div class="marker_remove" param="marker" title="Odstranit"></div></div><div class="marker_content" param="marker"><textarea id="screen_report" name="marker_name[]" class="ta_marker_name" param="marker" wrap="on" rows="1" placeholder="' + js_string_marker_hint + '""></textarea></div></div>')
                    .attr('param', 'marker')
                    .attr('title', js_string_marker_hint)
                    .offset({left: e.pageX-55, top: e.pageY-105})
                    .draggable()
                    //.resizable({helper: "ui-resizable-helper", ghost: true})
                    .click(function(e) {
                        e.preventDefault();
                        $(this).toggleClass('highlighted');
                    })
                    .mousedown(function(e) {
                        if (e.which === 2) {
                            $(this).remove();
                        }
                    });                    
                    //.mouseenter(function() {
                    //    $(this).append($(''));                        
                    //})
                    //.mouseleave(function() {
                    //    $(this).find('div:last').remove();
                    //});
                    
            }
            $('textarea.ta_marker_name').autogrow().focus();            
            $('.marker_remove').click(function() {
                $(this).parents('.marker').remove();
                return false;
            });
        });
    }

});