var slides = null;
var currentSlide = -1;
var currentButtonName = "";

function setSlides(newSlides) {
    if (Array.isArray(newSlides)) {
        //If is array - cast it to map
        newSlides = Object.assign({}, newSlides);
    }
    slides = newSlides;
    updateSlides();
    checkAndInit();
}

function updateSlides() {   
    jQuery('#slides-input').val(JSON.stringify({items: slides}));
}

function checkAndInit() {
    for (var key in slides) {
        var slide = slides[key];
        if (slide.h_align === undefined) {
            slide.h_align = 'left';
        }
        if (slide.image === undefined) {
            slide.image = '';
        }
        if (slide.buttons === undefined || Object.keys(slide.buttons).length === 0) {
            slide.buttons = {};
        }
        if (slide.title === undefined) {
            slide.title = '';
        }
        if (slide.text === undefined) {
            slide.text = '';
        }
        if (slide.text_color === undefined) {
            slide.text_color = "dark";
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
    /*$('#slide-up').click(onSlideUp);
    $('#slide-down').click(onSlideDown);*/
    $('#add-slide-image').click(onClickAddImageButton);
    
    $('#select-button').change(onChangeButtonSelect);
    $('#add-button').click(onClickAddButton);
    $('#remove-button').click(onClickRemoveButton);
    $('#slide-title').change(onChangeSlideTitle);
    $('#button-key').change(onChangeButtonKey);
    $('#button-value').change(onChangeButtonValue);

    // Bind to slide update
    $('#slide-params button').click(updateSlide);
    $('#slide-params select, #slide-params input, #slide-params textarea').change(updateSlide);

    function onClickAddSlide() {
        var number = getNewSlideNumber();
        var name = "Slide " + (number);
        slides[number] = {
            title: sanitize(name),
            text: "",
            buttons: {},
            image: "",
            h_align: "left",
            text_color: "dark"
        };

        var option = new Option(name, number); 
        $('#select-slide').append($(option));
        $('#select-slide').val(number);
        onChangeSlideSelect();

        updateSlides();

        return false; // FIX Do not submit form
    }

    // Get free slide index
    function getNewSlideNumber() {
        var maxSlideKey = 0; 
        for (var key in slides) {
            if (maxSlideKey < parseInt(key))
                maxSlideKey = parseInt(key);
        }
        return maxSlideKey + 1;
    }

    function onRemoveSlide() {
        delete slides[currentSlide];
        $('#select-slide option').remove('[value="'+ currentSlide  +'"]');

        // Select default value
        $('#select-slide').val('-1');
        onChangeSlideSelect();

        updateSlides();

        return false; // FIX Do not submit form
    }

    /*function onSlideUp() {
        // Up option
        var select = $('#select-slide')
        var self = select.find('option:selected');
        if (self.index() > 0 ) {
            self.insertBefore(self.prev());
            var counter = 0;
        }

        updateSlides();

        return false; // FIX Do not submit form
    }

    function onSlideDown() {
        // Down option
        var select = $('#select-slide')
        var self = select.find('option:selected');
        if (self.index() > 0 ) {
            self.insertAfter(self.next());
        }

        updateSlides();

        return false; // FIX Do not submit form
    }*/

    function onChangeSlideSelect() {
        var itemNumber = $('#select-slide').val();
        currentSlide = itemNumber;
        if (itemNumber < 0) {
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

            /*if (itemNumber > 0)
                $('#slide-up').show();
            else
                $('#slide-up').hide();

            if (itemNumber < Object.keys(slides).length - 1) 
                $('#slide-down').show();
            else
                $('#slide-down').hide();*/
        }
    }

    function onChangeSlideTitle() {
        $('#select-slide option:selected').text($(this).val());
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
        var key = getNewButtonName();

        slides[currentSlide].buttons[key] = key; // FIX Key equals value (no button id yet)
        var option = new Option(key, key); 
        $('#select-button').append($(option));
        $('#select-button').val(key);
        onChangeButtonSelect();

        return false; // FIX Do not submit form
    }

    // Get free button name
    function getNewButtonName() {
        var number = Object.keys(slides[currentSlide].buttons).length + 1;
        var name = "Button";
        while (slides[currentSlide].buttons[name + " " + number] !== undefined) {
            number++;
        }
        return name + " " + number;
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
        if (itemKey < 0) {
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