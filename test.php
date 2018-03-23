<?php

define('LOCAL_JSON', 'tests.json');

if (!file_exists(LOCAL_JSON)) {
    header("HTTP/1.1 404 Not Found");
    exit("404");
} else {
    $tests = json_decode(file_get_contents(LOCAL_JSON), true);
    $index = intval($_GET['index']); // (int) $number
    if (!$tests[$index])
    {
        header("HTTP/1.1 404 Not Found");
        exit("Test not found! 404");
    }
}

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<style type="text/css">
			.green { color: #009900; }
			.red   { color: #990000; }
		</style>
	</head>
	<body><?php

/*
1.	Принимает в качестве GET-параметра номер теста и отображает форму теста.
2.	Если форма отправлена, проверяет и показывает результат.

3.  При прохождении теста запрашивать имя (нужно дополнительное поле под имя).
    После прохождения теста генерировать PNG-сертификат с именем и оценкой.
*/

    if (empty($_POST['ans'])) {
        // пользователь ещё не ответил на тест, показать вопросы
        show_test($tests[$index], $index);
    } else {
        check_answers($tests[$index], $_POST['ans']);
    }


function show_test($test, $index) {
	echo "<h1>{$test['test_name']}</h1>";
	echo "<form method='post' action='{$_SERVER['SCRIPT_NAME']}?index={$index}'>";

	foreach ($test['questions'] as $qi => $question) {
		echo "<div>";
		echo "<p>{$question['question']}</p>"; // вопрос
		foreach ($question['answers'] as $ai => $answer) {
			echo "<p><input type='checkbox' name='ans[{$qi}][]' value='{$ai}'> {$answer['q']}</p>";
		}
		echo "</div>";
	}

    echo "<label>
				Name: <input type=\"text\" name=\"first_name\">
			</label>
			<label>
				SecondName: <input type=\"text\" name=\"second_name\">
			</label>
	      	<input type='submit' value='Проверить и получить сертификат'><br><br>";
	echo "</form>";
}

function check_answers($test, $user_answers) {
	$result = [];

	foreach ($test['questions'] as $qi => $question) {
		// [0, 1] & [1] - пересечение множеств (intersect)
		$correct_q_a = []; // индексы правильных вариантов
		foreach ($question['answers'] as $ai => $answer) {
			if (isset($answer['correct']) && $answer['correct'])
				$correct_q_a[] = $ai;
		}
		if (empty($user_answers[$qi]))
			$user_answers[$qi] = [];
		$size_intersect = count(array_intersect($correct_q_a, $user_answers[$qi])); //размер пересечения
		$result[$qi] = ($size_intersect == count($correct_q_a)); //пользователь ответил корректно на этот вопрос?
	}

	$count_correct = count(array_filter($result));
	$count_all = count($result);
	$percent = round($count_correct / $count_all * 100, 2);
	echo "<h1>Результаты: {$test['test_name']}</h1>";
	echo "<p>Правильных: {$count_correct}</p>";
	echo "<p>Всего: {$count_all}</p>";
	echo "<p>{$percent}%</p>";

	foreach ($test['questions'] as $qi => $question) {
		echo "<div>";
		echo "<p>{$question['question']}</p>"; // вопрос

		if ($result[$qi])
			echo "<p class='green'>Корректно</p>";
		else
			echo "<p class='red'>Некорректно</p>";
		
		echo "<ul>";
		foreach ($question['answers'] as $ai => $answer) {
			echo "<li>{$answer['q']}</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
    echo '<img src="certificate.php?first_name=' . $_POST["first_name"] . '&second_name=' . $_POST["second_name"] . '&persent=' . $percent . '">';
}

?>
    <a href="list.php"><button>Вернуться к списку</button></a>

	</body>
</html>
