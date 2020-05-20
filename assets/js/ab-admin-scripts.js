jQuery(document).ready(function( $ ) {
	document.getElementById("slides-input").value = JSON.stringify({items: slides});
    var lastSlideLength = slides.length;
    var currentSlide = -1;

    $('#add-slide').click(onClickAddSlide);
    $('#select-input').change(onChangeSlideSelect);
    $('#remove-slide').click(onRemoveSlide);
    $('#save-slide').click(onClickSaveSlide);
    $('#add-slide-button').click(onClickAddButton);

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

    checkAndInit();

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

    function onClickAddSlide() {
        var optionName = "Slide " + slides.length;
        var o = new Option(optionName, slides.length);
        if (lastSlideLength == 0) {
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
        var selectInput = jQuery('#select-input');
        selectInput.append(o);
        if (lastSlideLength == 0) {
            selectInput.change();
        }
        lastSlideLength = slides.length;
        $('#slides-input').val(JSON.stringify({items: slides}));
        return false;
    }

    function onRemoveSlide() {
        if (currentSlide >= slides.length || currentSlide < 0) {
            $('#slide-params-form').hide();
            $('#slide-buttons').hide();
            $('#add-slide-button').hide();
            $('#slide-buttons-key').hide();
            $('#slide-button-value').hide();
            return;
        }
        var selectInput = $('#select-input');
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

        return false;
    }

    function onChangeSlideSelect() {
        var itemNumber = $('#select-input').val();
        if (itemNumber >= slides.length || itemNumber < 0) {
            $('#slide-params-form').hide();
            $('#slide-buttons').hide();
            $('#add-slide-button').hide();
            $('#slide-buttons-key').hide();
            $('#slide-button-value').hide();
            $('#remove-slide').hide();
            return;
        }
        currentSlide = itemNumber;
        $('#slide-title').val(slides[currentSlide].title).show();
        $('#slide-text').val(slides[currentSlide].text).show();
        $('#slide-image').val(slides[currentSlide].image).show();
        $('#slide-buttons').html(JSON.stringify(slides[currentSlide].buttons)).show();
        $('input[name="horizontal_align"]').prop('checked', false).parent().find('input[name="horizontal_align"][value="' + slides[currentSlide].h_align + '"]').prop('checked', true);
        $('input[name="text_color"]').prop('checked', false).parent().find('input[name="text_color"][value="' + slides[currentSlide].text_color + '"]').prop('checked', true);

        $('#add-slide-button').show();
        $('#slide-buttons-key').show();
        $('#slide-button-value').show();
        $('#slide-params-form').show();
        $('#remove-slide').show();
        //TODO: buttons
    }

    function onClickSaveSlide() {

        slides[currentSlide].title = sanitize($('#slide-title').val());
        slides[currentSlide].text = sanitize($('#slide-text').val());
        slides[currentSlide].image = ($('#slide-image').val());
        slides[currentSlide].h_align = $('input[name="horizontal_align"]:checked').val();
        slides[currentSlide].text_color = $('input[name="text_color"]:checked').val();
        $('#slides-input').val(JSON.stringify({items: slides}));
        $('#select-input option[value="'+ currentSlide  +'"]').html(slides[currentSlide].title);
        return false;
    }

    function onClickAddButton() {
        var key = $('#slide-buttons-key').val();
        var value = $('#slide-button-value').val();
        slides[currentSlide].buttons[key] = value;
        $('#slide-buttons-key').val("");
        $('#slide-button-value').val("");
        $('#slide-buttons').html(JSON.stringify(slides[currentSlide].buttons));
        return false;
    }
});