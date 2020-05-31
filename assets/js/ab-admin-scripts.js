var slides = null;
var lastSlideLength = 0;
var currentSlide = -1;
var currentButtonName = "";

function setSlides(newSlides) {
    slides = newSlides;
    updateSlides();
    lastSlideLength = slides.length;
    checkAndInit();
}

function updateSlides() {   
    jQuery('#slides-input').val(JSON.stringify({items: slides}));
}

function checkAndInit() {
    for (var i = 0; i < slides.length; ++i) {
        if (slides[i].h_align === undefined) {
            slides[i].h_align = 'left';
        }
        if (slides[i].image === undefined) {
            slides[i].image = '';
        }
        if (slides[i].buttons === undefined || Object.keys(slides[i].buttons).length === 0) {
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
    $('#slide-up').click(onSlideUp);
    $('#slide-down').click(onSlideDown);
    $('#add-slide-image').click(onClickAddImageButton);
    
    $('#select-button').change(onChangeButtonSelect);
    $('#add-button').click(onClickAddButton);
    $('#remove-button').click(onClickRemoveButton);
    $('#button-key').change(onChangeButtonKey);
    $('#button-value').change(onChangeButtonValue);

    // Bind to slide update
    $('#slide-params button').click(updateSlide);
    $('#slide-params select, #slide-params input, #slide-params textarea').change(updateSlide);

    function onClickAddSlide() {
        var name = "Slide " + (slides.length + 1);
        slides.push({
            title: sanitize(name),
            text: "",
            buttons: {},
            image: "",
            h_align: "left",
            text_color: "dark"
        });

        var option = new Option(name, slides.length - 1); 
        $('#select-slide').append($(option));
        $('#select-slide').val(slides.length - 1);
        onChangeSlideSelect();

        updateSlides();

        return false; // FIX Do not submit form
    }

    function onRemoveSlide() {
        slides.splice(currentSlide, 1);
        $('#select-slide option').remove('[value="'+ currentSlide  +'"]');

        // Select default value
        $('#select-slide').val('-1');
        onChangeSlideSelect();

        updateSlides();

        return false; // FIX Do not submit form
    }

    function onSlideUp() {


        updateSlides();

        return false; // FIX Do not submit form
    }

    function onSlideDown() {
        updateSlides();

        return false; // FIX Do not submit form
    }

    function onChangeSlideSelect() {
        var itemNumber = $('#select-slide').val();
        currentSlide = itemNumber;
        if (itemNumber >= slides.length || itemNumber < 0) {
            $('#slide-params').hide();
            $('#remove-slide').hide();
            $('#slide-up').hide();
            $('#slide-down').hide();
            $('#button-params').hide();
        } else {
            $('#slide-title').val(slides[currentSlide].title).show();
            $('#slide-text').val(slides[currentSlide].text).show();
            $('#slide-image').val(slides[currentSlide].image).show();
            $('input[name="horizontal_align"]').prop('checked', false).parent().find('input[name="horizontal_align"][value="' + slides[currentSlide].h_align + '"]').prop('checked', true);
            $('input[name="text_color"]').prop('checked', false).parent().find('input[name="text_color"][value="' + slides[currentSlide].text_color + '"]').prop('checked', true);
            
            // Clear slide button options
            $('#select-button option').remove('[value!="-1"]');
            onChangeButtonSelect();

            // Add slide button options
            var buttons = slides[currentSlide].buttons;
            for (key in buttons) {
                var option = new Option(key, key); 
                $('#select-button').append($(option));
            }

            $('#slide-params').show();
            $('#remove-slide').show();

            if (itemNumber > 0)
                $('#slide-up').show();
            else
                $('#slide-up').hide();

            if (itemNumber < slides.length - 1) 
                $('#slide-down').show();
            else
                $('#slide-down').hide();
        }
    }

    function onClickAddImageButton() {
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open().on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#slide-image').val(image_url);
            $('#slide-image').change(); // Imitate change event
        });
        return false; // FIX Do not submit form
    }

    function onClickAddButton() {
        var key = "Button " + (Object.keys(slides[currentSlide].buttons).length + 1);

        slides[currentSlide].buttons[key] = key; // FIX Key equals value (no button id yet)
        var option = new Option(key, key); 
        $('#select-button').append($(option));
        $('#select-button').val(key);
        onChangeButtonSelect();

        return false; // FIX Do not submit form
    }

    function onClickRemoveButton() {
        delete slides[currentSlide].buttons[currentButtonName];
        $('#select-button option').remove('[value="'+ currentButtonName  +'"]');

        // Select default value
        $('#select-button').val('-1');
        onChangeButtonSelect();

        return false; // FIX Do not submit form
    }

    function onChangeButtonSelect() {
        var itemKey = $('#select-button').val();
        var buttons = slides[currentSlide].buttons;
        currentButtonName = itemKey;
        if (itemKey >= Object.keys(buttons).length || itemKey < 0) {
            $('#button-params').hide();
            $('#remove-button').hide();
        } else {
            $('#button-key').val(currentButtonName);
            $('#button-value').val(buttons[currentButtonName]);

            $('#button-params').show();
            $('#remove-button').show();
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

    function updateSlide() {   
        if (currentSlide !== -1) {
            slides[currentSlide].title = sanitize($('#slide-title').val());
            slides[currentSlide].text = sanitize($('#slide-text').val());
            slides[currentSlide].image = ($('#slide-image').val());
            slides[currentSlide].h_align = $('input[name="horizontal_align"]:checked').val();
            slides[currentSlide].text_color = $('input[name="text_color"]:checked').val();
            updateSlides();
        }
        return false; // FIX Do not submit form
    }
});