<?php
echo file_get_contents("views/header.html");

require_once("Set.php");

echo '<a href="/"><button>На главную</button></a>';

$set = new Set();
$ids = $set->get_ids();

if (!count($ids)) {
    echo '<div class="fill__title">Наборов не найдено</div>';
    exit();
}

echo '<div class="fill__title">Найдено наборов: ' . count($ids) . '</div><div class="sets">';

echo '<div class="set"><div class="set__block">Текстовые поля</div><div class="set__block">Строковые поля</div><div class="set__block">Чекбоксы</div></div>';

foreach ($ids as $id) {
    $current_set = $set->get($id);
    echo '<div class="set">';
    foreach ($current_set as $type => $data) {
        echo '<div class="set__block">';
        if (count($data)) {
            switch ($type) {
                case 'textarea':
                case 'text':
                    foreach ($data as $value)
                        echo '<div class="set__element">' . $value . '</div>';
                    break;
                case 'checkbox':
                    for ($i = 1; $i >= 0; $i--)
                        if (isset($data[$i])) {
                            foreach ($data[$i] as $checkbox) {
                                echo '<div class="set__element">';
                                echo '<input type="checkbox" id="' . $id . '" ' . ($i == 1 ? 'checked' : '') . '><label for="' . $id . '">' . $checkbox . '</label>';
                                echo '</div>';
                            }
                        }
                    break;
            }
        } else
            echo "Нет данных";
        echo '</div>';
    }
    echo '</div>';
}

echo '</div>';

echo file_get_contents("views/footer.html");