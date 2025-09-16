<?php
// Подключаем конфигурационный файл
require_once 'config.php';

// Экспорт в Excel
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=посещаемость_' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Добавляем BOM для корректного отображения кириллицы в Excel
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // Заголовки с разделителем точка с запятой
    fputcsv($output, array('ID', 'Имя', 'Фамилия', 'Группа', 'Дата', 'Время'), ';');
    
    $result = $conn->query("SELECT id, first_name, last_name, group_name, 
                          DATE(timestamp) as date, TIME(timestamp) as time 
                          FROM students ORDER BY timestamp DESC");
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit();
}

// Получение данных с возможностью фильтрации по группе
$group_filter = "";
if (isset($_GET['group']) && !empty($_GET['group'])) {
    $group_filter = " WHERE group_name = '" . $conn->real_escape_string($_GET['group']) . "'";
}

$result = $conn->query("SELECT * FROM students $group_filter ORDER BY timestamp DESC");
$total_records = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр посещаемости</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; cursor: pointer; position: relative; }
        th:hover { background-color: #45a049; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        .btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-excel { background: #217346; color: white; }
        .btn-excel:hover { background: #1a5c38; }
        .filter { margin: 20px 0; padding: 10px; background: #e9ecef; border-radius: 5px; }
        .stats { background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Журнал посещаемости студентов</h1>
        
        <div class="filter">
            <form method="GET">
                <label>Фильтр по группе:</label>
                <select name="group" onchange="this.form.submit()">
                    <option value="">Все группы</option>
                    <?php
                    for ($i = 21; $i <= 29; $i++) {
                        $selected = (isset($_GET['group']) && $_GET['group'] == "0907-$i") ? 'selected' : '';
                        echo "<option value='0907-$i' $selected>0907-$i</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
        
        <div class="stats">
            Всего записей: <strong><?php echo $total_records; ?></strong>
            <?php if (isset($_GET['group']) && !empty($_GET['group'])): ?>
                | Группа: <strong><?php echo htmlspecialchars($_GET['group']); ?></strong>
            <?php endif; ?>
        </div>
        
        <a href="?<?php echo http_build_query($_GET); ?>&export=1" class="btn btn-excel">📥 Экспорт в Excel</a>
        
        <table id="attendanceTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">ID ▲▼</th>
                    <th onclick="sortTable(1)">Имя ▲▼</th>
                    <th onclick="sortTable(2)">Фамилия ▲▼</th>
                    <th onclick="sortTable(3)">Группа ▲▼</th>
                    <th onclick="sortTable(4)">Дата и время ▲▼</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['group_name']); ?></td>
                    <td><?php echo date('d.m.Y H:i:s', strtotime($row['timestamp'])); ?></td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo '<tr><td colspan="5" style="text-align: center;">Нет данных для отображения</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <script>
            function sortTable(column) {
                const table = document.getElementById("attendanceTable");
                let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                switching = true;
                dir = "asc";
                
                while (switching) {
                    switching = false;
                    rows = table.rows;
                    
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("TD")[column];
                        y = rows[i + 1].getElementsByTagName("TD")[column];
                        
                        // Для числовых значений (ID)
                        if (column === 0) {
                            x = parseInt(x.innerHTML);
                            y = parseInt(y.innerHTML);
                        } else {
                            x = x.innerHTML.toLowerCase();
                            y = y.innerHTML.toLowerCase();
                        }
                        
                        if (dir == "asc") {
                            if (x > y) {
                                shouldSwitch = true;
                                break;
                            }
                        } else if (dir == "desc") {
                            if (x < y) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount++;
                    } else {
                        if (switchcount == 0 && dir == "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
            }
        </script>
    </div>
</body>
</html>

<?php $conn->close(); ?>