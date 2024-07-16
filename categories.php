<?php

require_once 'db.php';

// Обработка формы создания/редактирования категории
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category_name"])) {
    $category_name = $_POST["category_name"];
    $category_id = isset($_POST["category_id"]) ? $_POST["category_id"] : null;

    if ($category_id) {
        // Обновление существующей категории
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $category_name, $category_id);
    } else {
        // Создание новой категории
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
    }

    if ($stmt->execute()) {
        echo "Категория сохранена успешно!";
    } else {
        echo "Ошибка при сохранении категории: " . $stmt->error;
    }

    $stmt->close();
}

// Вывод формы для создания/редактирования категории
echo "<h2>Управление категориями</h2>";
echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SCRIPT"]) . "'>";
echo "Название категории: <input type='text' name='category_name'><br>";
echo "<select name='category_id'>";
echo "<option value=''>Выберите категорию</option>";

// Получение списка категорий для выпадающего списка
$stmt = $conn->prepare("SELECT id, name FROM categories");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
}

echo "</select>";
echo "<input type='submit' value='Сохранить'>";
echo "</form>";

$conn->close();
?>