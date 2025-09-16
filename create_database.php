<?php
// create_database.php - создание базы данных и таблицы
$servername = "localhost";
$username = "root";
$password = "";

// Создаем соединение без выбора базы данных
$conn = new mysqli($servername, $username, $password);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Создаем базу данных
$sql = "CREATE DATABASE IF NOT EXISTS attendance_db 
        CHARACTER SET utf8mb4 
        COLLATE utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "База данных создана успешно или уже существует<br>";
} else {
    echo "Ошибка создания базы данных: " . $conn->error . "<br>";
}

// Выбираем базу данных
$conn->select_db("attendance_db");

// Создаем таблицу students
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    group_name VARCHAR(20) NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_group (group_name),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "Таблица students создана успешно или уже существует<br>";
} else {
    echo "Ошибка создания таблицы: " . $conn->error . "<br>";
}

$conn->close();
echo "Настройка завершена. <a href='student_attendance.php'>Перейти к форме отметки</a>";
?>