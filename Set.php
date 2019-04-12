<?php

class Set
{
    protected $mysqli, $set;
    private $pattern = [
        'textarea' => array(),
        'text' => array(),
        'checkbox' => array()
    ];

    function __construct()
    {
        include("config.php");

        $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (mysqli_connect_errno()) {
            echo "Не удалось подключиться к базе данных";
            exit();
        }

        $this->mysqli = $db;
    }

    function __destruct()
    {
        $this->mysqli->close();
    }

    // обрабатывает поступившие в $_POST данные, преобразовывая в многомерный массив
    private function prepare_post($input)
    {
        $data = $this->pattern;

        foreach ($input as $key => $value) {
            $type = mb_substr($key, 0, mb_strpos($key, '_'));

            switch ($type) {
                case 'textarea':
                case 'text':
                    if (mb_strlen($value))
                        $data[$type][] = $value;
                    break;
                case 'checkbox':
                    // здесь код ищет чекбоксы, у которых имеется подпись, иначе игнорирует несмотря на наличие/отсутствие статуса checked
                    if (mb_strpos($key, 'text') && mb_strlen($value)) {
                        $first_underscore_pos = mb_strpos($key, '_');
                        $index = mb_substr($key, $first_underscore_pos + 1, mb_strlen($key) - $first_underscore_pos - 6);
                        $data[$type][array_key_exists("checkbox_" . $index, $_POST)][] = $value;
                    }
                    break;
            }
        }

        $this->set = $data;
    }

    // сохраняет в базу введенные пользователем данные формы
    public function save($input)
    {
        $this->prepare_post($input);

        if (count($this->set, COUNT_RECURSIVE) > 3) {

            // строковые поля и чекбоксы сохраются в базе сериализованными
            $texts = count($this->set['text']) ? serialize($this->set['text']) : '';
            $checkboxes = count($this->set['checkbox']) ? serialize($this->set['checkbox']) : '';

            $query = "INSERT INTO sets VALUES (NULL, '$texts', '$checkboxes')";
            if (!$this->mysqli->query($query))
                return "Не удалось записать в базу данных весь набор <br>" . $this->mysqli->error;

            // данные из textarea сохраняются в отдельную таблицу
            if (count($this->set['textarea'])) {
                $set_id = $this->mysqli->insert_id;

                foreach ($this->set['textarea'] as $value)
                    if (!$this->mysqli->query("INSERT INTO textareas VALUES (NULL, '$set_id', '$value')"))
                        return "Не удалось записать в базу данных текстовые поля <br>" . $this->mysqli->error;
            }
        } else
            return "Данных для записи не найдено";

        return true;
    }

    // возвращает id всех наборов в базе
    public function get_ids()
    {
        $result = $this->mysqli->query("SELECT id FROM sets ORDER BY id DESC");

        $ids = array();

        while ($row = $result->fetch_row()) {
            $ids[] = (int)$row[0];
        }

        return $ids;
    }

    // возвращает массив с данными набора по заданному id
    public function get($id)
    {
        $set = $this->mysqli->query("SELECT * FROM sets WHERE id = $id");
        $textareas = $this->mysqli->query("SELECT content FROM textareas WHERE set_id = $id");

        $data = $this->pattern;

        while ($row = $set->fetch_assoc()) {
            $data['text'] = mb_strlen($row['texts']) ? unserialize($row['texts']) : [];
            $data['checkbox'] = mb_strlen($row['checkboxes']) ? unserialize($row['checkboxes']) : [];
        }

        while ($row = $textareas->fetch_row())
            $data['textarea'][] = $row[0];

        return $data;
    }
}