$(document).ready(function () {
    // количество элементов каждого типа
    var elements = {
        textarea: 0,
        text: 0,
        checkbox: 0
    };

    // добавление нового элемента на страницу
    $(".content__block button").on("click", function () {
        var type = $(this).parent().data('type'), // тип добавляемого элемента
            field = ''; // HTML-текст добавляемого элемента

        if (type === undefined)
            return false;

        switch (type) {
            case 'textarea':
                field = '<textarea disabled></textarea>';

                break;
            case 'text':
                field = '<input type="text" disabled>';

                break;
            case 'checkbox':
                var id = "checkbox_" + new Date().getTime();
                field = '<input type="checkbox" id="' + id + '" disabled><label for="' + id + '">Не задано</label>';

                break;
            default:
                alert("Упс! Произошла ошибка, обновите страницу.");
                break;
        }

        if (field.length) {
            var remover = '<span title="Удалить элемент">&times;</span>';
            $(this).parent().append('<div class="content__element">' + field + remover + '</div>');

            elements[type]++;

            // показываем кнопку перехода к заполнению (форму), если добавлен самый первый элемент
            if ($(".new__set-form").is(":hidden"))
                $(".new__set-form").slideDown(100);

            fillForm();
        }
    });

    // удаление элемента со страницы
    $(".content__block").on("click", "span", function () {
        var type = $(this).parent().parent().data('type');
        elements[type]--;
        $(this).parent().remove();

        // если это последний элемент на странице - прячем кнопку перехода к заполнению
        if (!$("*").is(".content__element"))
            $(".new__set-form").slideUp(100);

        fillForm();
    });

    // записывает значения количества каждого вида элементов в скрытые поля формы
    function fillForm() {
        for (var key in elements)
            $("input[name=" + key + "]").val(elements[key]);
    }

});