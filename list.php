<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
<?php
// список загруженных тестов

define('LOCAL_JSON', 'tests.json');

if (!file_exists(LOCAL_JSON)) {
	echo "<p>Файл JSON с тестами не найден!</p>";
} else {
	$tests = json_decode(file_get_contents(LOCAL_JSON), true);

	echo "<h1>Тесты</h1>";
	echo "<ul>";

	foreach ($tests as $index => $test) {
		$count = count($test['questions']); // количество вопросов
		echo "<li><a href='test.php?index={$index}'>{$test['test_name']}</a> ({$count} вопрос(ов))</li>";
	}

	echo "</ul>";
}
?>
    <a href="admin.php"><button>Загрузить новый тест</button></a>
	</body>
</html>
