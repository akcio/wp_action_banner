var slides = null;
var lastSlideLength = 0;
var currentSlide = -1;
var currentButtonName = "";

function setSlides(newSlides) {
    slides = newSlides;
    jQuery('#slides-input').val(JSON.stringify({items: slides}));
    lastSlideLength = slides.length;
    checkAndInit();
}

function checkAndInit() {
    for (var i = 0; i < slides.length; ++i) {
        if (slides[i].h_align === undefined) {
            slides[i].h_align = 'left';
        }
        if (slides[i].image === undefined) {
            slides[i].image = '';
        }
        if (slides[i].buttons === undefined) {
            slides[i].buttons = {};
        }
        if (slides[i].title === undefined) {
            slides[i].title = '';
        }
        if (slides[i].text === undefined) {
            slides[i].text = '';
        }
        if (slides[i].text_color === undefined) {
            slides[i].text_color = "dark";
        }
    }
}

jQuery(document).ready(function($) {
    function sanitize(string) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            "/": '&#x2F;',
        };
        const reg = /[&<>"'/]/ig;
        return string.replace(reg, (match)=>(map[match]));
    }

    $('#select-slide').change(onChangeSlideSelect);
    $('#add-slide').click(onClickAddSlide);
    $('#remove-slide').click(onRemoveSlide);
    $('#add-slide-image').click(onClickAddImageButton);
    
    $('#select-button').change(onChangeButtonSelect);
    $('#add-button').click(onClickAddButton);
    $('#remove-button').click(onClickRemoveButton);
    $('#button-key').change(onChangeButtonKey);
    $('#button-value').change(onChangeButtonValue);

    $('#save-slide').click(onClickSaveSlide);

    function onClickAddSlide() {
        var optionName = "Slide " + slides.length;
        var o = new Option(optionName, slides.length);
        if (lastSlideLength === 0) {
            o.selected = true;
        }
        slides.push({
            title: sanitize(optionName),
            text: "",
            buttons: {},
            image: "",
            h_align: "left",
            text_color: "dark"
        });
        $(o).html(optionName);
        var selectInput = $('#select-slide');
        selectInput.append(o);
        if (lastSlideLength === 0) {
            selectInput.change();
        }
        lastSlideLength = slides.length;
        $('#slides-input').val(JSON.stringify({items: slides}));
        return false;
    }

    function onRemoveSlide() {
        if (currentSlide >= slides.length || currentSlide < 0) {
            $('#slide-params').hide();
            //$('#add-slide-button').hide();
            $('#button-params').hide();
            return;
        }
        var selectInput = $('#select-slide');
        selectInput.find('option:selected').remove();
        selectInput.find('option[value="-1"]').prop('selected', true);
        var newSlides = [];
        for (var i=0; i < slides.length; ++i) {
            if (i !== currentSlide) {
                newSlides.push(slides[i]);
            }
        }
        slides = newSlides;
        currentSlide = -1;
        lastSlideLength = -1;
        $('#slides-input').val(JSON.stringify({items: slides}));

        onChangeSlideSelect();
    }

    function onChangeSlideSelect() {
        var itemNumber = $('#select-slide').val();
        if (itemNumber >= slides.length || itemNumber < 0) {
            $('#slide-params').hide();
            $('#remove-slide').hide();
            $('#button-params').hide();
        } else {
            currentSlide = itemNumber;
            $('#slide-title').val(slides[currentSlide].title).show();
            $('#slide-text').val(slides[currentSlide].text).show();
            $('#slide-image').val(slides[currentSlide].image).show();
            $('input[name="horizontal_align"]').prop('checked', false).parent().find('input[name="horizontal_align"][value="' + slides[currentSlide].h_align + '"]').prop('checked', true);
            $('input[name="text_color"]').prop('checked', false).parent().find('input[name="text_color"][value="' + slides[currentSlide].text_color + '"]').prop('checked', true);
            
            // Clear slide button options
            $('#select-button option').remove('[value!="-1"]');

            // Add slide button options
            var buttons = slides[currentSlide].buttons;
            for (key in buttons) {
                var option = new Option(key, key); 
                $('#select-button').append($(option));
            }

            $('#slide-params').show();
            $('#remove-slide').show();
        }
    }

    function onClickAddImageButton() {
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            console.log(uploaded_image);
            $('#slide-image').val(image_url);
        });
    }

    function onClickSaveSlide() {
        slides[currentSlide].title = sanitize($('#slide-title').val());
        slides[currentSlide].text = sanitize($('#slide-text').val());
        slides[currentSlide].image = ($('#slide-image').val());
        slides[currentSlide].h_align = $('input[name="horizontal_align"]:checked').val();
        slides[currentSlide].text_color = $('input[name="text_color"]:checked').val();
        $('#slides-input').val(JSON.stringify({items: slides}));
        $('#select-slide option[value="'+ currentSlide  +'"]').html(slides[currentSlide].title);
    }

    function onClickAddButton() {
        /*var key = $('#slide-buttons-key').val();
        var value = $('#slide-button-value').val();
        slides[currentSlide].buttons[key] = value;
        $('#slide-buttons-key').val("");
        $('#slide-button-value').val("");
        $('#slide-buttons').html(JSON.stringify(slides[currentSlide].buttons));
        return false;*/
    }

    function onClickRemoveButton() {

    }

    function onChangeButtonSelect() {
        var itemKey = $('#select-button').val();
        var buttons = slides[currentSlide].buttons;
        if (itemKey >= slides.length || itemKey < 0) {
            $('#button-params').hide();
            $('#remove-slide').hide();
        } else {
            currentButtonName = itemKey;
            $('#button-key').val(currentButtonName);
            $('#button-value').val(buttons[currentButtonName]);

            $('#button-params').show();
            $('#remove-slide').show();
        }
    }

    function onChangeButtonKey() {
        // Delete old value
        delete slides[currentSlide].buttons[currentButtonName];

        // Create new value and reset option
        var buttonName = $(this).val();
        slides[currentSlide].buttons[buttonName] = $('#button-value').val();
        $('#select-button option[value="'+ currentButtonName  +'"]').val(buttonName).text(buttonName);
        currentButtonName = buttonName;
    }

    function onChangeButtonValue() {
        slides[currentSlide].buttons[currentButtonName] = $(this).val();
    }

});