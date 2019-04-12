<?php

echo file_get_contents("../views/header.html");

require_once("../Set.php");

$set = new Set();

$result = $set->save($_POST);

echo '<div class="new__set-title">' . ($result === true ? 'Данные успешно записаны' : $result) . '</div>';

echo '<a href="/"><button>На главную</button></a>';
echo '<a href="/show.php"><button>Посмотреть все наборы</button></a>';

echo file_get_contents("../views/footer.html");