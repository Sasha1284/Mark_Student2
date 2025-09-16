<?php
// Подключаем конфигурационный файл
require_once 'config.php';

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($conn->real_escape_string($_POST['first_name']));
    $last_name = trim($conn->real_escape_string($_POST['last_name']));
    $group_name = $conn->real_escape_string($_POST['group_name']);
    
    if (!empty($first_name) && !empty($last_name) && !empty($group_name)) {
        // Проверяем, не отмечался ли студент сегодня
        $today = date('Y-m-d');
        $check_sql = "SELECT id FROM students 
                     WHERE first_name = '$first_name' 
                     AND last_name = '$last_name' 
                     AND group_name = '$group_name' 
                     AND DATE(timestamp) = '$today'";
        
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $error = "Вы уже отметились сегодня!";
        } else {
            // Вставляем новую запись
            $sql = "INSERT INTO students (first_name, last_name, group_name) 
                   VALUES ('$first_name', '$last_name', '$group_name')";
            
            if ($conn->query($sql) === TRUE) {
                $success = "Отметка успешно сохранена!";
            } else {
                $error = "Ошибка: " . $conn->error;
            }
        }
    } else {
        $error = "Пожалуйста, заполните все поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отметка студентов</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 50px; background: #f5f5f5; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; border: none; cursor: pointer; font-size: 16px; }
        button:hover { background: #45a049; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Система отметки студентов</h1>
        
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <input type="text" name="first_name" placeholder="Имя" required maxlength="50">
            <input type="text" name="last_name" placeholder="Фамилия" required maxlength="50">
            
            <select name="group_name" required>
                <option value="">Выберите группу</option>
                <?php
                for ($i = 21; $i <= 29; $i++) {
                    echo "<option value='0907-$i'>0907-$i</option>";
                }
                ?>
            </select>
            
            <button type="submit">✅ Отметиться</button>
        </form>
        
        <p style="margin-top: 20px; color: #666;">
            <small>Сегодня: <?php echo date('d.m.Y'); ?></small>
        </p>
    </div>
</body>
</html>

<?php $conn->close(); ?>