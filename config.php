<?php
// config.php - файл подключения к базе данных
$servername = "localhost";
$username = "root";    // стандартный пользователь XAMPP
$password = "";        // стандартный пароль XAMPP (пустой)
$dbname = "attendance_db";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку UTF-8
$conn->set_charset("utf8");
?>