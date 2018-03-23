<?php
define('LOCAL_JSON', 'tests.json');
define('MAX_UPLOAD_FILE_SIZE', 5*1024*1024);
define('SAVE_JSON_OPTIONS', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

function elem_is_array_and_not_empty($hash, $key) {
	return array_key_exists($key, $hash) && is_array($hash[$key]) && !empty($hash[$key]);
}

function is_json_correct($json) {
	$valid = array_key_exists("test_name", $json);
	$valid = $valid && elem_is_array_and_not_empty($json, "questions");

	if ($valid) {
		foreach ($json["questions"] as $question) {
			$valid = $valid && array_key_exists("question", $question);
			$valid = $valid && elem_is_array_and_not_empty($question, "answers");

			if ($valid) {
				foreach ($question["answers"] as $answer) {
					$valid = $valid && array_key_exists("q", $answer);
				}
			}
		}
	}

	return $valid;
}

function read_json_file($file_name) {
	$file_contents = file_get_contents($file_name);
	if (strlen($file_name) >= MAX_UPLOAD_FILE_SIZE)
		return null;
	return json_decode($file_contents, true); // => структуры PHP
}

if (!empty($_FILES['tests'])) {
	// обработка загруженного файла
	$success = false;
	if (!is_uploaded_file($_FILES['tests']['tmp_name'])) {
		echo "<p>Получена запись о файле, но файл не был загружен во временную папку!</p>";
	} else {
		$json = read_json_file($_FILES['tests']['tmp_name']);
		if (!is_null($json) && is_json_correct($json)) {
			// сохраняем в файле json, дописываем в существующий файл с тестами
			if (file_exists(LOCAL_JSON)) {
				$local_file = file_get_contents(LOCAL_JSON); 
					if (empty($local_file))
					$local_file = '[]';
			} else {
				$local_file = '[]'; // пустой массив в JSON
			}
			if ($local_file) {
				$local_json = json_decode($local_file);
				$local_json[] = $json;
				$new_json = json_encode($local_json, SAVE_JSON_OPTIONS);
				$success = file_put_contents(LOCAL_JSON, $new_json);
			}
		}
	}

	if ($success === false)
		echo "<p>Произошла ошибка при сохранении файла. Проверьте его содержимое.</p>";
	else
		header('Location:list.php');//Добавляем редирект на список тестов, который будет отрабатывать после загрузки нового теста.
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" enctype="multipart/form-data">
			<p>Выберите файл JSON с тестами</p>
			<p><input type="file" name="tests"></p>
			<p><input type="submit" value="Отправить"></p>
		</form>
		<a href="list.php"><button>Выбрать тест для выполнения</button></a>
	</body>
</html>
