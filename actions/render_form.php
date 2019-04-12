<?php
echo file_get_contents("../views/header.html");

echo '<a href="/"><button>На главную</button></a>';

echo '<div class="fill__title">Заполните поля ниже</div><form method="post" action="save_form.php"><div class="fill__content">';

foreach ($_POST as $type => $count) {
    if ($count) {
        echo '<div class="fill__block">';
        for ($i = 0; $i < $count; $i++) {
            switch ($type) {
                case 'textarea':
                    echo '<textarea name="' . $type . '_' . $i . '" placeholder="Текстовое поле #' . ($i + 1) . '" rows="5"></textarea>';
                    break;
                case 'text':
                    echo '<input type="text" name="' . $type . '_' . $i . '" placeholder="Строковое поле #' . ($i + 1) . '">';
                    break;
                case 'checkbox':
                    $name = $type . '_' . $i;
                    echo '<div class="content__checkbox">';
                    echo '<input type="checkbox" id="' . $name . '" name="' . $name . '"><label for="' . $name . '"><input type="text" name="' . $name . '_text" placeholder="Чекбокс #' . ($i + 1) . '"></label>';
                    echo '</div>';
                    break;
            }
        }
        echo '</div>';
    }
}

echo '</div><input type="submit" value="Сохранить в базу"></form>';

echo file_get_contents("../views/footer.html");