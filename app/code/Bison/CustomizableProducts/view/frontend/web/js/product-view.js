require(["jquery", 'Bison_CustomizableProducts/js/interact.min', 'Magento_Ui/js/modal/modal', 'mage/url', 'Bison_CustomizableProducts/js/clippingMagic' ,'select2',  'Bison_CustomizableProducts/js/jquery.svg.pan.zoom', 'jquery/ui', "domReady!"], function ($, interact, modal, url) {

    var elementsToHideForCustomizer = '.tabing-content, header, .ser_info.clearfix, footer.footer, small.copyright, .block.related, .additional-note, .fotorama-item, .page.messages',
        elementsToShowForCustomizer = '.customizable-options, .customizable-tab, .gallery-designer-buttons, .customizer-actions',
        overlayClasses = '.options .race-number, .options .number-font, .options .driver-name, .options .name-position, .options .name-font',
        colorPaletteClasses = '.race-number.options .number-color, .race-number.options .number-background-color, .driver-name.options .name-color',
        saveCancelButtons = '#colors-confirm, #colors-cancel, #number-confirm, #number-cancel, #driver-confirm, #driver-cancel, #logo-confirm, #logo-cancel',
        saveCancelPaletteButtons = '#number-palette-confirm, #number-palette-cancel,#driver-palette-confirm, #driver-palette-cancel',
        svg = $('#svgComponentContainer'),
        svgWidth = 58000,
        svgHeight = 52000,
        svgPositionTop,
        svgPositionLeft,
        svgElementWidth,
        svgElementHeight,
        xAxisMultiplier,
        yAxisMultiplier,
        imageDx,
        imageDy,
        rotateDx,
        rotateDy,
        currentColorNr = 0,
        rotateRegExp = /rotate\(([0-9.\-]+) ([(0-9.\-]+) ([(0-9.\-]+)\)/,
        translateRegExp = /translate\(([(0-9.\-]+) ([(0-9.\-]+)\)/,
        logosHtml,
        usedColor,
        svgOriginal = [],
        svgCustomized = [],
        plainSvg,
        viewBox,
        logoDuplicate = 1,
        cmErrorsArray,
        initialNumberCoordinates;

    $('.tab-content.inspiration').show();

    function isPortraitOrientation() {
        if (typeof window.screen !== 'undefined' && typeof window.screen.orientation !== 'undefined' && typeof window.screen.orientation.angle !== 'undefined') {
            return !(window.screen.orientation.angle === 90 || window.screen.orientation.angle === -90 || window.screen.orientation.angle === 270);
        }

        if (typeof orientation !== 'undefined') {
            return !(orientation === 90 || orientation === -90 || orientation === 270);
        }
    }

    function isMobile() {
        return window.innerWidth < 768;
    }

    function rotateToPortraitAlert() {
        alert('Please rotate your device to portrait position');
    }

    $(window).on('resize', function () {
        if ($('#maincontent, .columns').hasClass('customizer') && isMobile() && !isPortraitOrientation()) {
            rotateToPortraitAlert();
        }
    });

    function showCustomizer() {
        $('.product-info-main').children().hide();
        $('#maincontent, .columns').addClass('customizer');
        $(elementsToHideForCustomizer).hide();
        $(elementsToShowForCustomizer).show();
        $(window).trigger('resize');
        var fonts = $('.svg-fonts').clone();
        $('.svg_preview svg').prepend(fonts.find('style'));
    }

    function hideCustomizer() {
        $('.product-info-main').children().not('#logoSelector, #infoPopup').show();
        $('#maincontent, .columns').removeClass('customizer');
        $(elementsToHideForCustomizer).show();
        $(elementsToShowForCustomizer).hide();
        $(window).trigger('resize');
    }

    function showCustomizeProductButton() {
        $('.change-design-button-loader').hide();
        $('#customize-product-button').show();
    }

    $(document).on('click', '#customize-product-button', function () {
            if (isMobile() && !isPortraitOrientation()) {
                rotateToPortraitAlert();
            } else {
                showCustomizer();
                svgOriginal = [];
                $('#svgDesigner > g').each(function() {
                    svgOriginal.push(this.outerHTML || new XMLSerializer().serializeToString(this));
                });
            }
    });

    $(document).ready(function () {
        cmErrorsArray = ClippingMagic.initialize({apiId: window.cmApiId});

        $.post(
            url.build('customiser/bodywork'),
            {
                product_id: $('.price-box.price-final_price').attr('data-product-id')
            },
            function (response) {
                plainSvg = response;
                $('#svgComponentContainer').html(response);
                initialNumberCoordinates = getInitialNumberCoordinates();
                setDefaultSvgOptions($('.svg_preview svg'));
                initializeSvgPanZoom();
                showCustomizeProductButton();
                viewBox = document.getElementById('svgDesigner').getAttribute('viewBox');
                var svgElement = $('.svg_preview svg').get(0);
                $('.generatedSvgTextarea').val(svgElement.outerHTML || new XMLSerializer().serializeToString(svgElement));
                loadDefaultColors();
            }
        );
    });

    function getInitialNumberCoordinates() {
        var numberTextElements = $('.svg_preview svg').find('g#Number text'),
            coordinatesArray = [];

        if (numberTextElements.length) {
            for (var i = 0; i < numberTextElements.length; i++) {
                coordinatesArray[i] = parseInt(numberTextElements.get(i).getAttribute('x'));
            }
        }

        return coordinatesArray;
    }

    function setDefaultSvgOptions(svgElement) {
        svgElement.attr('id', 'svgDesigner');

        if ($('#show-number, #show-name').prop('checked')) {
            $('#show-number, #show-name').trigger('click');
        }

        setNumberLengthAdjust(svgElement);

        svgElement.find('g#Number, path#Border, path#No_Background').css('visibility', 'hidden');
        svgElement.find('[id^="Name_Style"]').attr('visibility', 'hidden');
    }

    function setNumberLengthAdjust(svgElement) {
        var numberTextElements = svgElement.find('g#Number text');

        if (numberTextElements.length) {
            for (var i = 0; i < numberTextElements.length; i++) {
                numberTextElements.get(i).setAttribute('textLength', 6000);

                var numberElement = numberTextElements.get(i),
                    numberLength = numberElement.textContent.length;

                if (numberLength > 1) {
                    numberElement.setAttribute('lengthAdjust', 'spacingAndGlyphs');
                } else {
                    numberElement.setAttribute('lengthAdjust', 'spacing');
                }

                if (isInternetExplorerBrowser()) {
                    if (numberLength == 1) {
                        numberElement.setAttribute('x', initialNumberCoordinates[i])
                    } else if (numberLength == 2) {
                        numberElement.setAttribute('x', (initialNumberCoordinates[i] + 1000))
                    } else if (numberLength == 3) {
                        numberElement.setAttribute('x', (initialNumberCoordinates[i] + 3500))
                    }
                }
            }
        }
    }

    function isInternetExplorerBrowser() {
        if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > -1) {
            return true;
        }
    }

    $(document).on('click', '#customization-done', function () {
        document.getElementById('svgDesigner').setAttribute('viewBox',viewBox);
        if ($('.svg_preview svg').length) {
            var svg = $('.svg_preview svg').get(0);

            svg = svg.outerHTML || new XMLSerializer().serializeToString(svg);

            svg = svg.replace(/\s{2,}/gi, ' ');

            $('.generatedSvgTextarea').val(svg);
        }

        svgCustomized = [];
        $('#svgDesigner > g').each(function() {
            svgCustomized.push(this.outerHTML || new XMLSerializer().serializeToString(this));
        });

        var isDesignCustomized = !compareArrays(svgOriginal, svgCustomized);
        if (isDesignCustomized) {
            saveDesign();
            updateGallery();
        }

        hideCustomizer();

        $('#customization-price-checkbox input[type="checkbox"]').prop('checked', isDesignCustomized).trigger('change');
    });

    function compareArrays(array1, array2) {
        return $(array1).not(array2).length == 0 && $(array2).not(array1).length == 0;
    }

    $(document).on('click', '#startOverButton', function(){
        if (confirm('Are you sure you want to start again?')) {
            $('#svgComponentContainer').html(plainSvg);
            setDefaultSvgOptions($('.svg_preview svg'));
            loadDefaultColors();
            initializeSvgPanZoom();
        }
    });

    $(document).on('click', '.customizable-tab', function () {
        var firstClass = $(this).attr('class').split(' ')[0];

        if( firstClass === 'customizable-logo' && logosHtml && logosHtml.length) {
            $('.customizable-options').html(logosHtml);
        } else {
            getHtmlContent(firstClass);
        }

        $('.customizer-actions').addClass('mobile-hide');
    });

    $(document).on('click', overlayClasses, function () {
        var firstClass = $(this).attr('class').split(' ')[0];
        $('select.overlay-input').trigger('change');
        showOverlay(firstClass, true);
    });

    $(document).on('click', colorPaletteClasses, function () {
        $(this).hide();
        $(this).siblings().hide();
        if ($(this).data('rel')) {
            $('.options .color-picker.'+$(this).data('rel')).show();
        } else {
            $('.options .color-picker').show();
        }

        $(this).parent('.items').removeClass('scroll');
        $('.options .colors').addClass('flex');
        $('#number-palette-cancel, #number-palette-confirm, #driver-palette-cancel, #driver-palette-confirm').show();
        $('#number-cancel, #number-confirm, #driver-confirm, #driver-cancel').hide();
    });

    $(document).on('click', '.colors-in-design .picked-colors .color', function () {
        $(this).addClass('picked');
        $('.picked-colors').hide();
        $('.color-picker').show();
        $('.colors').addClass('flex');
        $('#colors-palette-cancel, #colors-palette-confirm').show();
        $('#colors-cancel, #colors-confirm').hide();
    });

    $(document).on('submit', '#logo-upload', function (e) {
        e.preventDefault();
        $('.product-info-main').trigger('processStart');
        $.ajax({
            type: "POST",
            url: url.build("customiser/logo/upload/"),
            data: new FormData(this),
            contentType: false,
            processData: false,
            complete: function (data) {
                $('.product-info-main').trigger('processStop');
                $('#logo-upload input').val('');
                var responseData = data.responseJSON;

                if (typeof responseData.name == 'undefined') {
                    return;
                }

                if (responseData.type) {
                    var options = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: true,
                        title: 'How to remove the unwanted background of your logo',
                        modalClass: 'infoPopupModal',
                        opened: function(event, ui) {
                            $(this).parent().parent().find('.action-close').hide()
                        },
                        buttons: [
                            {
                                text: $.mage.__('Skip'),
                                class: 'skip',
                                click: function () {
                                    addLogoToSvg(responseData.url, responseData.id, responseData.width, responseData.height, responseData.type, responseData.name);
                                    getLogoUploadedMessages(data);
                                    this.closeModal();
                                }
                            },
                            {
                                text: $.mage.__('Start background removal'),
                                class: 'openEditor',
                                click: function () {
                                    $('.loader').show();

                                    if (cmErrorsArray.length > 0) {
                                        alert("Sorry, your browser is missing some required features: \n\n " + cmErrorsArray.join("\n "));
                                        addLogoToSvg(responseData.url, responseData.id, responseData.width, responseData.height, responseData.type, responseData.name);
                                        getLogoUploadedMessages(data);
                                        $('.loader').hide();
                                    } else {
                                        var jsonData = data;
                                        $.ajax({
                                            type: "POST",
                                            url: url.build("customiser/logo/removeBackground"),
                                            data: {
                                                logo_id: responseData.id
                                            }
                                        })
                                        .done(function (data) {
                                            $('.loader').hide();
                                            if (data.success) {
                                                ClippingMagic.edit({
                                                    "image" : {
                                                        "id" : data.imageId,
                                                        "secret" : data.secret
                                                    },
                                                    "locale" : "en-US"
                                                }, function (response) {
                                                    if (response.event === "result-generated") {
                                                        $.post(
                                                            url.build("customiser/logo/update"),
                                                            {
                                                                logoId: responseData.id,
                                                                imageId: response.image.id,
                                                            },
                                                            function (updatedData) {
                                                                var date = new Date();
                                                                addLogoToSvg(updatedData.url+'?t='+date.getTime(), responseData.id, responseData.width, responseData.height, responseData.type, responseData.name);
                                                                getLogoUploadedMessages(jsonData);
                                                                $('.loader').hide();
                                                            }
                                                        );
                                                    }
                                                });
                                            } else if (data.error) {
                                                alert(data.error);
                                            }
                                        });
                                    }

                                    this.closeModal();
                                }
                            }
                        ]
                    };


                    var infoPopupSelector = $('#infoPopup'),
                        infoPopup = modal(options, infoPopupSelector);

                    infoPopupSelector.modal("openModal");
                } else {
                    addLogoToSvg(responseData.url, responseData.id, responseData.width, responseData.height, responseData.type, responseData.name);
                    getLogoUploadedMessages(data);
                }
            }
        });
    });

    $(document).on('click', '.tab-header', function () {
        $('.tab-header').removeClass('active');
        $(this).addClass('active');
        var id = $(this).attr('id'),
            targetTabContent = $('.tab-content.'+id);

        if (!targetTabContent.is(':visible')) {
            targetTabContent.siblings().hide();
            targetTabContent.slideDown(1000);
        }
    });

    $(document).on('click', '#rgb, #fluorescent', function () {
        $(this).addClass('active');
        if ($(this).attr('id') === 'rgb') {
            $('#fluorescent').removeClass('active');
            $('.available-fluorescent-colors').each(function () {
                $(this).hide();
            });

            $('.available-rgb-colors').each(function () {
                $(this).show();
            });
        }

        if ($(this).attr('id') === 'fluorescent') {
            $('#rgb').removeClass('active');
            $('.available-rgb-colors').each(function () {
                $(this).hide();
            });

            $('.available-fluorescent-colors').each(function () {
                $(this).show();
            });
        }
    });

    $(document).on('click', '.available-colors, .available-rgb-colors, .available-fluorescent-colors', function () {
        $(this).parent().children().each(function () {
            if ($(this).hasClass('chosen-color')) {
                $(this).removeClass('chosen-color')
            }
        });
        $(this).addClass('chosen-color');
    });

    $(document).on('click', saveCancelButtons, function (e) {
        e.stopPropagation();
        if ($(e.target).attr('id') === 'logo-cancel') {
            logosHtml = $('.customizable-options').html();
        }

        if ($(this).attr('id').indexOf('confirm') != -1) {
            save($(this).attr('id'));
        }

        getHtmlContent('menu');

        $('.customizer-actions').removeClass('mobile-hide');
    });

    $(document).on('click', '#colors-palette-confirm, #colors-palette-cancel', function (e) {
        e.stopPropagation();
        if ($(this).attr('id').indexOf('confirm') != -1) {
            var color = $('.chosen-color').find('.color-hash').text().replace('#', '');
            $('.color.picked').parent().html($('.chosen-color').html());
            $('[data-layer-id="layer_'+currentColorNr+'"]').find('select option:contains('+color+')').attr('selected', 'selected').parent().trigger('change');
        } else {
            $('.chosen-color').removeClass('chosen-color');
        }

        $('.picked-colors').show();
        $('.color-picker').hide();
        $('.colors').removeClass('flex');
        $('#colors-palette-cancel, #colors-palette-confirm').hide();
        $('#colors-cancel, #colors-confirm').show();
    });

    $(document).on('click', saveCancelPaletteButtons, function (e) {
        e.stopPropagation();
        if ($(this).attr('id').indexOf('confirm') != -1) {
            save($(this).attr('id'));
        } else {
            $('.chosen-color').removeClass('chosen-color');
        }

        $('.options .row > .items > div').show();
        $('.options .row > .items').addClass('scroll');
        $('.options .color-picker').hide();
        $('.options .colors').removeClass('flex');
        $('#number-palette-cancel, #number-palette-confirm, #driver-palette-cancel, #driver-palette-confirm').hide();
        $('#number-cancel, #number-confirm, #driver-confirm, #driver-cancel').show();
    });

    $('.colors-button.overlay button').on('click', function () {
        var selector = '.' + $(this).parent().parent().attr('class');
        showOverlay(selector, false);
        if ($(this).hasClass('confirm')) {
            $('.customizable-tab .options ' + selector).val($(selector + " .overlay-input").val());
        }

        if ($(this).hasClass('cancel')) {
            $(selector + ' .overlay-input, .race-number input').val("");
        }
    });

    $(document).on('click', '.logo-upload', function (e) {
        if (e.target !== e.currentTarget) return;
        $('#logo-file').trigger('click');
    });

    $(document).on('change', '#logo-file', function () {
        $('#logo-upload').trigger('submit');
    });

    $(document).on('click', '.remove-logo > i', function (event) {
        event.stopPropagation();
        var id = $(this).parent().attr('logo-id');
        var el = this;

        if (parseInt(id) > 0) {
            $.ajax({
                type: "POST",
                url: url.build("customiser/logo/delete/"),
                data: { id : id },
                complete: function (data) {
                    if (!data.responseJSON.error) {
                        $(el).parent().fadeOut(800, function () {
                            $(this).remove();
                        });
                        $('#logo_'+id).remove();

                        var logoImageElements = svg.find('.logo-image.selected');

                        if (!logoImageElements.length) {
                            $('#logo-adjustments-panel').hide();
                        }
                    } else {
                        var messageDiv = $('.logo.options .message.base').clone();
                        messageDiv.removeClass('base').addClass('error').html(data.responseJSON.error);
                        messageDiv.insertAfter('.logo.options .customizable-option-title').fadeOut(2000, function() {
                            $(this).remove();
                        });
                    }
                }
            });
        } else {
            $(el).parent().fadeOut(800, function () {
                $(this).remove();
            });
            $('#logo_'+id).remove();
        }
    });

    $(document).on('click', '.tab-content img', function () {
        logosHtml = null;
        var productId = $('.price-box.price-final_price').attr('data-product-id');
        var imgUrl = $(this).attr('src');

        $.ajax({
            type: "POST",
            url: url.build('customiser/bodywork'),
            data: {
                product_id: productId,
                img_url: imgUrl
            },
            complete: function (data) {
                if (data.responseJSON) {
                    $('#svgComponentContainer').html(data.responseJSON);
                    plainSvg = data.responseJSON;
                    viewBox = document.getElementById('svgDesigner').getAttribute('viewBox');
                    initializeSvgPanZoom();
                    updateGallery();
                    getHtmlContent('menu');
                    $('.generatedSvgTextarea').val(plainSvg);
                    loadDefaultColors();
					$('#productsvg_img').val(imgUrl);
                }
            }
        });

    });

    /**
     * Colors
     */
    $(document).on('click', '.picked-colors', function(){
        var self = $(this);
        currentColorNr = parseInt($(this).attr('data-layer-id'));

        var product_id = $('.price-box.price-final_price').attr('data-product-id');

        $.ajax({
            type: "POST",
            url: url.build("customiser/renderer"),
            data: {
                type: 'customizable-layer_colors',
                product_id : product_id,
                layer_id: currentColorNr
            }
        })
        .done(function (data) {
            var colorPicker = $('.color-picker'),
                selectedColor = self.find('.color-hash').attr('data-color').replace('#', '');

            usedColor = selectedColor;

            colorPicker.find('.available-colors').remove();
            colorPicker.append(data);
            colorPicker.find('.available-colors').removeClass('chosen-color');
            colorPicker.find('#layerColor'+selectedColor).attr('checked', 'checked').parent().addClass('chosen-color');
        });
    });
    $(document).on('click', '.colors-in-design .available-colors', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var colorDiv = $(this).find('.color-hash'),
            color = colorDiv.attr('data-color').toUpperCase(),
            colorName = colorDiv.attr('data-color-name'),
            additionalPrice = colorDiv.attr('data-additional-price');

        $('[data-layer-id="'+currentColorNr+'"]')
            .find('.color').css('background', color).next().attr('data-color', color)
            .find('span').text(colorName)
            .siblings('.additional-price').text(additionalPrice);

        $('[data-layer-id="layer_'+currentColorNr+'"]').find('select option:contains('+color.replace('#', '')+')').attr('selected', 'selected').parent().trigger('change');
        $('.svg_preview svg .Color_'+currentColorNr).each(function(index, elem){
            elem.setAttribute('fill', color);
        })
    });

    $(document).on('click', '#colors-palette-cancel', function(){
        $('[data-layer-id="'+currentColorNr+'"]').find('.color').css('background', '#'+usedColor).next().find('span').text('#'+usedColor);

        $('[data-layer-id="layer_'+currentColorNr+'"]').find('select option:contains('+usedColor.replace('#', '')+')').attr('selected', 'selected').parent().trigger('change');
        $('.svg_preview svg .Color_'+currentColorNr).each(function(index, elem){
            elem.setAttribute('fill', '#'+usedColor);
        })
    });

    /**
     * Race number adjustments
     */
    $(document).on('click', '.race-number .confirm', function(){
        $('.svg_preview svg #Number').find('g text, text').each(function(index, elem){
            elem.textContent = $('.race-number .overlay-input').val();
        });

        setNumberLengthAdjust($('.svg_preview svg'));

        var displayValue = $('.race-number .overlay-input').val() != '' ? $('.race-number .overlay-input').val() : '123';

        $('select#number-font-select option').each(function () {
            $(this).text(displayValue);
        });

        $('select.overlay-input').select2();
    });

    $('select.overlay-input').select2();

    $(document).on('change','select.overlay-input:not(#name-position-select)', function () {
        var style = $(this).children(':selected').attr('style');
        $(this).siblings('span.select2').attr('style', style);
    });

    $(document).on('click', '.number-font .confirm', function(){
        $('.svg_preview svg #Number').find('g text, text').each(function(index, elem){
            elem.setAttribute('font-family', $('.number-font .overlay-input').val());
        })
    });

    $(document).on('click', '.number .available-colors', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var color = $(this).find('.color-hash').attr('data-color');

        $('[data-layer-id="number"]').find('select option:contains('+color.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        $('.svg_preview svg #Number').find('g text, text').each(function(index, elem){
            elem.setAttribute('fill', color);
        });
    });

    $(document).on('click', '.number-background .available-colors', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var color = $(this).find('input[name=number_background]').val();
        $('[data-layer-id="number_bkg"]').find('select option:contains('+color.replace('#', '')+')').attr('selected', 'selected').parent().trigger('change');
        $('.svg_preview svg #No_Panel #No_Background').each(function(index, elem){
            elem.setAttribute('fill', color);
        })
    });

    /**
     * Driver number adjustments
     */
    $(document).on('click', '.driver-name .confirm', function(){
        $('.svg_preview svg').find('#Name_Style_1, #Name_Style_2, #Name_Style_3').find('g text, text').each(function(index, elem){
            elem.textContent = $('.driver-name .overlay-input').val();
        })

        var displayValue = $('.driver-name .overlay-input').val() != '' ? $('.driver-name .overlay-input').val() : 'Your Name';
        $('select#name-font-select option').each(function () {
            $(this).text(displayValue);
        });

        $('select.overlay-input').select2();
    });

    $(document).on('click', '.driver-name .available-colors', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var color = $(this).find('input[name=driver_name_color]').val();
        $('[data-layer-id="driver"]').find('select option:contains('+color.replace('#', '')+')').attr('selected', 'selected').parent().trigger('change');
        $('.svg_preview svg').find('#Name_Style_1, #Name_Style_2, #Name_Style_3').find('g text, text').each(function(index, elem){
            jQuery(this).attr('fill', color);
        })
    });

    $(document).on('click', '.name-font .confirm', function(){
        $('.svg_preview svg').find('#Name_Style_1, #Name_Style_2, #Name_Style_3').find('g text, text').each(function(index, elem){
            elem.setAttribute('font-family', $('.name-font .overlay-input').val());
        })
    });

    $(document).on('click', '.name-position .confirm', function(){
        $('.svg_preview svg').find('#Name_Style_1, #Name_Style_2, #Name_Style_3').each(function(index, elem){
            var visible = $(elem).closest('#Name_Style_'+$('.name-position .overlay-input').val()).length;
            elem.setAttribute('visibility', visible ? 'visible' : 'hidden');
        });
    });

    $(document).on('click touchend', '#svgDesigner image.logo-image', function(){
        selectLogo($(this));
    });

    $(document).on('click', '.uploaded-logos .remove-logo', function () {
        var logoId = $(this).attr('logo-id'),
            logoToSelect = $('#svgDesigner #logo_' + logoId + ' .logo-image');

        selectLogo(logoToSelect);
    });

    function selectLogo(logo) {
        if (typeof logo === 'undefined') {
            return;
        }

        var isActive = logo.hasClass('selected'),
            logoParentId = logo.parent().attr('id'),
            logoId = logoParentId.substring(5, logoParentId.length);

        if (!isActive) {
            logo.addClass('selected').siblings('rect').addClass('selected');
            logo.parent().siblings().children().removeClass('selected');
            $('.uploaded-logos .remove-logo[logo-id="'+logoId+'"]').addClass('selected').siblings().removeClass('selected');
            $('#logo-adjustments-panel').show();
        }
    }

    $(document).on('click touchend', '.svg', function(event) {
        event.stopPropagation();

        if (!$(event.target).hasClass('logo-image')) {
            $('g.logo, .uploaded-logos').children().removeClass('selected');
            $('#logo-adjustments-panel').hide();
        }
    });

    $(window).on('resize', function(){
        svgPositionTop = svg.position().top;
        svgPositionLeft = svg.position().left;
        svgElementWidth = svg.width();
        svgElementHeight = svg.height();
        xAxisMultiplier = svgWidth/svgElementWidth;
        yAxisMultiplier = svgHeight/svgElementHeight;
    });

    $(document).on('change', '.super-attribute-select', function () {
        updateDesigns();
    });

    var mouseX;
    var mouseY;
    var offset;

    $(document).on('mousemove', 'body', function (event) {
        mouseX = event.pageX;
        mouseY = event.pageY;
    });

    $(document).on('mouseenter','.logo-image', function (event) {
        if (typeof zoomSvg !== 'undefined') {
            zoomSvg.events.drag = false;
        }
    });

    $(document).on('mouseleave', '.logo-image', function (event) {
        if (typeof zoomSvg !== 'undefined') {
            zoomSvg.events.drag = true;
        }
    });

    function getMousePosition(evt) {
        var CTM = document.getElementById('svgDesigner').getScreenCTM();
        return {
            x: (evt.clientX - CTM.e) / CTM.a,
            y: (evt.clientY - CTM.f) / CTM.d
        };
    }

    interact('g.logo', { context: document.getElementById('svgDesigner') })
        .draggable({
            onstart: function (event) {
                var logoContainer = $(event.target);
                offset = getMousePosition(event);
                var svgTranslateData = logoContainer.attr('transform').match(translateRegExp);
                var translateX = svgTranslateData === null ? 0 : svgTranslateData[1];
                var translateY = svgTranslateData === null ? 0 : svgTranslateData[2];

                offset.x -= parseFloat(translateX);
                offset.y -= parseFloat(translateY);
                event.target.style.cursor = 'move';
            },
            onmove: function (event) {
                var coord = getMousePosition(event);
                var logoContainer = $(event.target);
                var svgRotateData = logoContainer.attr('transform').match(rotateRegExp);
                var rotateAngle = svgRotateData === null ? 0 : parseFloat(svgRotateData[1]);

                var logo = logoContainer.find('.logo-image')[0];
                var attributes = logo.attributes;
                var rotateX = attributes.width.value/2;
                var rotateY = attributes.height.value/2;

                var translateX = coord.x - offset.x;
                var translateY = coord.y - offset.y;

                logoContainer.attr('transform', 'translate(' + translateX + ' ' + translateY + ') rotate(' + rotateAngle + ' ' + rotateX + ' ' + rotateY + ')');
            },
            onend: function (event) {
                event.target.style.cursor = 'default';
            }
        })
        .styleCursor(false);

    /**
     * Scale selected logo
     * @param direction (up or down)
     */
    function scaleLogo(direction) {
        var logoImageElements = svg.find('.logo-image.selected'),
            logoOutline = svg.find('rect.selected')[0];

        if (!logoImageElements.length) {
            return;
        }

        var scale;
        var logo = logoImageElements[0];
        var logoSvgContainer = $(logo).parent();
        var svgTranslateData = logoSvgContainer.attr('transform').match(translateRegExp);
        var translateX = svgTranslateData === null ? 0 : svgTranslateData[1];
        var translateY = svgTranslateData === null ? 0 : svgTranslateData[2];
        var svgRotateData = logoSvgContainer.attr('transform').match(rotateRegExp);
        var rotateAngle = svgRotateData === null ? 0 : parseFloat(svgRotateData[1]);
        var rotateX = svgRotateData === null ? 0 : parseFloat(svgRotateData[2]);
        var rotateY = svgRotateData === null ? 0 : parseFloat(svgRotateData[3]);

        var attributes = logo.attributes;
        var width = parseFloat(attributes.width.value);
        var height = parseFloat(attributes.height.value);

        if (direction === 'up') {
            scale = 1.10;
        } else {
            scale = 0.90;
        }

        var imageRatio = width/height;
        var scaledWidth = width * scale;
        var scaledHeight = scaledWidth / imageRatio;

        if (direction === 'up') {
            translateX = parseFloat(translateX) - (scaledWidth - width)/2;
            translateY = parseFloat(translateY) - (scaledHeight - height)/2;
        } else {
            translateX = parseFloat(translateX) + (width - scaledWidth)/2;
            translateY = parseFloat(translateY) + (height - scaledHeight)/2;
        }

        logoSvgContainer.attr('transform', 'translate(' + translateX + ' ' + translateY + ') rotate(' + rotateAngle + ' ' + rotateX + ' ' + rotateY + ')');
        logo.setAttribute('width', scaledWidth);
        logo.setAttribute('height',  scaledHeight);
        logoOutline.setAttribute('width', scaledWidth);
        logoOutline.setAttribute('height',  scaledHeight);
    }

    /**
     * Rotate selected logo
     * @param rotation (left or right)
     */
    function rotateLogo(rotation) {
        var logoImageElements = svg.find('.logo-image.selected');

        if (!logoImageElements.length) {
            return;
        }

        var rotationAngle;
        var logo = logoImageElements[0];
        var logoSvgContainer = $(logo).parent();

        var attributes = logo.attributes;
        var rotateX = attributes.width.value/2;
        var rotateY = attributes.height.value/2;

        /** change logo rotation by 5 degrees */
        if (rotation === 'left') {
            rotationAngle = -5;
        } else {
            rotationAngle = 5;
        }

        var svgTranslateData = logoSvgContainer.attr('transform').match(translateRegExp);
        var svgRotateData = logoSvgContainer.attr('transform').match(rotateRegExp);
        var currentAngle = svgRotateData === null ? 0 : parseFloat((svgRotateData[1]));

        var newAngle = currentAngle + rotationAngle;
        newAngle = Math.abs(newAngle) === 360 ? 0 : newAngle;

        logoSvgContainer.attr('transform', 'translate(' + svgTranslateData[1] + ' '+ svgTranslateData[2]+ ') rotate(' + newAngle + ' ' + rotateX + ' ' + rotateY + ')');
    }

    function addLogoToSvg(url, id, width, height, type, name) {
        toDataURL(url, function (dataUrl) {
            url = dataUrl;
            var scale = 15000 / width,
                imageWidth = 15000,
                imageHeight = height * scale;

            if (svg.find('g#logo_'+id).length) {
                id = id + '' + logoDuplicate;
            }

            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('class', 'logo');
            g.setAttribute('id', 'logo_' + id);
            g.setAttribute('transform', 'translate(1000 1000)');
            g.setAttribute('data-name', name);

            var image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
            image.setAttributeNS('http://www.w3.org/1999/xlink', 'href', url);
            image.setAttributeNS(null, 'class', 'logo-image');
            image.setAttributeNS(null, 'width', imageWidth + '');
            image.setAttributeNS(null, 'height', imageHeight + '');
            image.setAttributeNS(null, 'preserveAspectRatio', 'none');
            image.setAttributeNS(null, 'transform', 'rotate(0)');

            var outline = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            outline.setAttributeNS(null, 'width', imageWidth + '');
            outline.setAttributeNS(null, 'height', imageHeight + '');
            outline.setAttributeNS(null, 'fill', 'transparent');

            $(g).append(outline);
            $(g).append(image);
            svg.find('svg').append(g);

            g.addEventListener('touchstart', function (e) {
                if (typeof zoomSvg !== 'undefined') {
                    zoomSvg.events.drag = false;
                }
            });

            g.addEventListener('touchend', function (e) {
                if (typeof zoomSvg !== 'undefined') {
                    zoomSvg.events.drag = true;
                }
            });
        });
    }

    $(document).on('click', '.logo-container ', function(){
        $("#logoSelector").find('.logo-container').removeClass('active');
        $(this).addClass('active');
        $('.useLogo').show();
    });

    $(document).on('click', '.logo-select', function(){
        var product_id = $('.price-box.price-final_price').attr('data-product-id'),
            logoSelector = $('#logoSelector');

        if ($(this).hasClass('disabled')) {
            return false;
        }

        $(this).addClass('disabled');

        $.ajax({
            type: "POST",
            url: url.build("customiser/renderer"),
            data: {
                type: 'customizable-predefined_logo',
                product_id : product_id,
                layer_id: 0
            }
        }).done(function(data){
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Select a logo',
                buttons: [{
                    text: $.mage.__('Add'),
                    class: 'useLogo',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            logoSelector.html(data);

            var popup = modal(options, logoSelector);
            logoSelector.modal("openModal");
        })
    });

    $(document).on('click', '.useLogo', function(){
        var logoSelector = $("#logoSelector"),
            label = logoSelector.find('.logo-container.active span'),
            image = logoSelector.find('.logo-container.active img');

        var data = {
            responseJSON: {
                id: image.attr('data-id'),
                name: label.text(),
                success: $.mage.__('Logo has been added.')
            }
        };

        getLogoUploadedMessages(data);
        addLogoToSvg(image.attr('src'), image.attr('data-id'), image.attr('data-width'), image.attr('data-height'), false, label.text());
    });

    $(document).on('click', '.logo-scale-up', function(){
        scaleLogo('up');
    });

    $(document).on('click', '.logo-scale-down', function(){
        scaleLogo('down');
    });

    $(document).on('click', '.logo-rotate-left', function(){
        rotateLogo('left');
    });

    $(document).on('click', '.logo-rotate-right', function(){
        rotateLogo('right');
    });

    function showOverlay(selector, show) {
        if (typeof show === 'undefined') {
            show = true;
        }

        if (show === true) {
            $('.product-info-main').hide();
            $('.product.media').addClass('fullscreen');
            $('.overlay .' + selector).show();
        }

        if (show === false) {
            $('.product-info-main').show();
            $('.product.media').removeClass('fullscreen');
            $('.overlay ' + selector).hide();
        }

        $(window).trigger('resize');
    }

    function getHtmlContent(type) {
        var product_id = $('.price-box.price-final_price').attr('data-product-id');

        $.ajax({
            type: "POST",
            url: url.build("customiser/renderer"),
            data: {
                type: type,
                product_id : product_id,
                layer_id: currentColorNr
            }
        })
        .done(function (data) {
            $('.customizable-options div').not('.start-over-button').remove();
            $('.customizable-options').prepend(data);

            if (type === 'customizable-colors') {
                var colorsElement = $('.colors-in-design'),
                    layer1Color = colorsElement.find('[data-layer-id="1"]'),
                    layer2Color = colorsElement.find('[data-layer-id="2"]'),
                    layer3Color = colorsElement.find('[data-layer-id="3"]');

                var color1 = svg.find('.Color_1').eq(0).attr('fill').toUpperCase(),
                    color1Element = colorsElement.find('[data-layer="1"] [data-color="'+color1+'"]'),
                    color1Name = color1Element.attr('data-color-name'),
                    color1AdditionalPrice = color1Element.attr('data-additional-price');

                layer1Color.find('.color').css('background', color1);
                layer1Color.find('.color-hash').attr('data-color', color1).find('span').text(color1Name).siblings('.additional-price').text(color1AdditionalPrice);

                var color2 = svg.find('.Color_2').eq(0).attr('fill').toUpperCase(),
                    color2Element = colorsElement.find('[data-layer="2"] [data-color="'+color2+'"]'),
                    color2Name = color2Element.attr('data-color-name'),
                    color2AdditionalPrice = color2Element.attr('data-additional-price');

                layer2Color.find('.color').css('background', color2);
                layer2Color.find('.color-hash').attr('data-color', color2).find('span').text(color2Name).siblings('.additional-price').text(color2AdditionalPrice);

                var color3 = svg.find('.Color_3').eq(0).attr('fill').toUpperCase(),
                    color3Element = colorsElement.find('[data-layer="3"] [data-color="'+color3+'"]'),
                    color3Name = color3Element.attr('data-color-name'),
                    color3AdditionalPrice = color3Element.attr('data-additional-price');

                layer3Color.find('.color').css('background', color3);
                layer3Color.find('.color-hash').attr('data-color', color3).find('span').text(color3Name).siblings('.additional-price').text(color3AdditionalPrice);

                $('.scrollable').css('height', ($(window).height()-parseInt($('.customizable-options').css('padding-top'))-$('.customizable-option-title').height()-$('#colors-palette-confirm').height()-60)+'px');
            }

            if (type === 'customizable-number') {
                if ($('#svgDesigner g#Number').css('visibility') === 'visible') {
                    $('.race-number .items > div').show();
                    $('.race-number .items > .tg-list').removeClass('single-item');
                    $('#show-number').prop( "checked", true );
                } else {
                    $('.race-number .items > div:not(.tg-list)').hide();
                    $('.race-number .items > .tg-list').addClass('single-item');
                }
            }

            if (type === 'customizable-name') {
                if ($('g[id^="Name_Style"][visibility="visible"]').length) {
                    $('.driver-name .items > div').show();
                    $('.driver-name .items > .tg-list').removeClass('single-item');
                    $('#show-name').prop( "checked", true );
                } else {
                    $('.driver-name .items > div:not(.tg-list)').hide();
                    $('.driver-name .items > .tg-list').addClass('single-item');
                }
            }

            if (type === 'customizable-logo') {
                $('#svgDesigner g.logo').each(function () {
                    var id = $(this).attr('id'),
                        logoId = id.substring(5, id.length);

                    if (!$('.uploaded-logos .remove-logo[logo-id='+logoId+']').length) {
                        var name = prepareName($(this).attr('data-name'));
                        $('.uploaded-logos').append('<span class="remove-logo" logo-id="'+logoId+'" style="display: block;">'+name+'<i class="fa fa-trash"></i></span>');
                    }
                });
            }
        });
    }

    function save(selector) {
        //TODO
    }

    function prepareName(name) {
        if (name.length > 20) name = name.substring(0, 20) + '...';
        return name;
    }

    function getLogoUploadedMessages(data) {
        var msgClass = 'success';
        var msg = data.responseJSON.success;

        if (data.responseJSON.error) {
            msgClass = "error";
            msg = data.responseJSON.error;
        }

        var messageDiv = $('.logo.options .message.base').clone();
        var logoDiv = $('.logo.options .remove-logo.base').clone();

        messageDiv.insertAfter('.logo.options .customizable-option-title');
        messageDiv.text(msg).addClass(msgClass);

        $('.uploaded-logos').css('max-height', ($(window).height()-350)+'px');

        messageDiv.delay(1000).fadeOut(800, function () {
            if (data.responseJSON.success) {
                var id = data.responseJSON.id;
                if ($('.uploaded-logos [logo-id="'+id+'"]').length) {
                    id = id + '' + logoDuplicate;
                    logoDuplicate++;
                }
                logoDiv.prepend(prepareName(data.responseJSON.name)).attr('logo-id', id);
                $('.uploaded-logos').append(logoDiv)
                logoDiv.fadeIn(800).removeClass('base').css('display', 'block');
            }

            $('.logo-select').removeClass('disabled');
            messageDiv.remove();
        });
    }

    $(document).on('click', '.modal-popup .action-close', function () {
        $('.logo-select').removeClass('disabled');
    });

    function initializeSvgPanZoom() {
        window.zoomSvg = $('#svgDesigner').svgPanZoom({
            events: {
                mouseWheel: false,
                doubleClick: false,
                drag: false
            },
            zoomFactor: 0.1
        });

        $('.zoom-control.fa-search-plus').on('click', function () {
            zoomSvg.zoomIn();
            zoomSvg.events.drag = true;
        });

        $('.zoom-control.fa-search-minus').on('click', function () {
            zoomSvg.zoomOut();
            zoomSvg.events.drag = true;
        });
    }

    $(document).ready(function (){
        $.ajax({
            type: "GET",
            url: url.build("customiser/design/data/"),
            dataType: 'json',
            contentType: 'application/json',
            complete: function (data) {
                if (data.responseJSON) {
                    var result = JSON.parse(data.responseJSON),
                        design_id = result.design_id;

                    setTimeout(function () {
                        $('div').find("img[design-id="+design_id+"]").trigger('click');
                    },1000);
                }
            }
        });
    });

    function saveDesign() {
        var svg = $('.svg .svg_preview svg').get(0);
        var productId = $('.price-box.price-final_price').attr('data-product-id');

        svg = svg.outerHTML || new XMLSerializer().serializeToString(svg);

        $.ajax({
            type: "POST",
            url: url.build("customiser/design/save/"),
            data: {
                svg: svg,
                product_id: productId
            },
            complete: function (data) {
                updateDesigns();
            }
        });
    }

    function updateDesigns() {
        var productId = $('.price-box.price-final_price').attr('data-product-id');

        $.ajax({
            type: "POST",
            url: url.build("customiser/design/renderer"),
            data: {
                product_id: productId
            },
            async: false,
            complete: function (data) {
                if (data.responseJSON) {
                    $('.container.product-page-tabs').replaceWith(data.responseJSON);
					var svgtabimg = $('#svgtabimg').val();
					$('#productsvg_img').val(svgtabimg);
                }

            }
        })
    }

    function loadDefaultColors() {
        var svg = $('#svgComponentContainer'),
            colorsElement = $('.colors-in-design'),
            layer1Color = colorsElement.find('[data-layer-id="1"]'),
            layer2Color = colorsElement.find('[data-layer-id="2"]'),
            layer3Color = colorsElement.find('[data-layer-id="3"]');

        var color1 = svg.find('.Color_1').eq(0).attr('fill');
        layer1Color.find('.color').css('background', color1);
        layer1Color.find('.color-hash span').text(color1);

        var color2 = svg.find('.Color_2').eq(0).attr('fill');
        layer2Color.find('.color').css('background', color2);
        layer2Color.find('.color-hash span').text(color2);

        var color3 = svg.find('.Color_3').eq(0).attr('fill');
        layer3Color.find('.color').css('background', color3);
        layer3Color.find('.color-hash span').text(color3);

        var driverColor = svg.find('[id^="Name_Style"][visibility="visible"] text').eq(0).attr('fill'),
            numberColor,
            numberBckgColor;

        svg.find('#Number').each(function () {
            if ($(this).css('visibility') == 'visible') {
                numberColor = $(this).find('text').eq(0).attr('fill');
            }
        });

        svg.find('#No_Background').each(function () {
            if ($(this).css('visibility') == 'visible') {
                numberBckgColor = $(this).eq(0).attr('fill');
            }
        });

        $('.control.bodywork-select select').val('').trigger('change');
        $('[data-layer-id="layer_'+1+'"]').find('select option:contains('+color1.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        $('[data-layer-id="layer_'+2+'"]').find('select option:contains('+color2.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        $('[data-layer-id="layer_'+3+'"]').find('select option:contains('+color3.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');

        if (driverColor) {
            $('[data-layer-id="driver"]').find('select option:contains('+driverColor.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        }
        if (numberColor) {
            $('[data-layer-id="number"]').find('select option:contains('+numberColor.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        }
        if (numberBckgColor) {
            $('[data-layer-id="number_bkg"]').find('select option:contains('+numberBckgColor.replace('#', '').toLowerCase()+')').attr('selected', 'selected').parent().trigger('change');
        }

    }

    function updateGallery() {
        var fotorama = jQuery('div.gallery-placeholder > div.fotorama').data('fotorama'),
            svgIndex = '',
            image = svgToimg();

        for (var i = 0; i < fotorama.data.length; i ++) {
            if (fotorama.data[i].svg === true) {
                svgIndex = i;
                break;
            }
        }

        if (svgIndex === '') {
            fotorama.push({img: image, thumb: image, svg: true})
            fotorama.show('>>');
        } else {
            fotorama.splice(-1, 1, {img: image, thumb: image, svg: true});
        }
    }

    function svgToimg(){
        var svg = document.querySelector('svg');
        var xml = new XMLSerializer().serializeToString(svg);
        var svg64 = btoa(xml);
        var b64start = 'data:image/svg+xml;base64,';
        return b64start + svg64;
    }

    function toDataURL(url, callback) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onload = function() {
            var fileReader = new FileReader();
            fileReader.onloadend = function() {
                callback(fileReader.result);
            }
            fileReader.readAsDataURL(httpRequest.response);
        };
        httpRequest.open('GET', url);
        httpRequest.responseType = 'blob';
        httpRequest.send();
    }

    $(document).on('click', '#show-number', function () {
        if ($(this).attr('checked')) {
            $('.race-number .items > div').show();
            $('.race-number .items > .tg-list').removeClass('single-item');
            $('#svgDesigner g#Number').css('visibility', 'visible');
            $('#svgDesigner path#Border').css('visibility', 'visible');
            $('#svgDesigner path#No_Background').css('visibility', 'visible');
        } else {
            $('.race-number .items > div:not(.tg-list)').hide();
            $('.race-number .items > .tg-list').addClass('single-item');
            $('#svgDesigner g#Number').css('visibility', 'hidden');
            $('#svgDesigner path#Border').css('visibility', 'hidden');
            $('#svgDesigner path#No_Background').css('visibility', 'hidden');
        }
    });

    $(document).on('click', '#show-name', function () {
        if ($(this).attr('checked')) {
            $('.driver-name .items > div').show();
            $('.driver-name .items > .tg-list').removeClass('single-item');
            var position = parseInt(jQuery('.name-position select.overlay-input option:selected').val());
            $("#Name_Style_" + position).attr('visibility', 'visible');
        } else {
            $('.driver-name .items > div:not(.tg-list)').hide();
            $('.driver-name .items > .tg-list').addClass('single-item');

            $('[id^="Name_Style_"]').each(function () {
                $(this).attr('visibility', 'hidden');
            });
        }
    });

    $.expr[":"].contains = $.expr.createPseudo(function (arg) {
        return function (elem) {
            return $(elem).text().toLowerCase().indexOf(arg.toLowerCase()) >= 0;
        }
    });
});
