<?php

require_once 'db.php';

// Обработка формы добавления/редактирования товара
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = isset($_POST["product_id"]) ? $_POST["product_id"] : null;
    $product_name = $_POST["product_name"];
    $product_description = $_POST["product_description"];
    $product_price = $_POST["product_price"];
    $product_category = $_POST["product_category"];
    $hidden_for_groups = isset($_POST["hidden_for_groups"]) ? $_POST["hidden_for_groups"] : null;

    if ($product_id) {
        // Обновление существующего товара
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, hidden_for_groups = ? WHERE id = ?");
        $stmt->bind_param("ssdsii", $product_name, $product_description, $product_price, $product_category, $hidden_for_groups, $product_id);
    } else {
        // Создание нового товара
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, hidden_for_groups) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $product_name, $product_description, $product_price, $product_category, $hidden_for_groups);
    }

    if ($stmt->execute()) {
        echo "Товар сохранен успешно!";
    } else {
        echo "Ошибка при сохранении товара: " . $stmt->error;
    }

    $stmt->close();
}

// Получение списка товаров для выпадающего списка
$stmt = $conn->prepare("SELECT id, name FROM products");
$stmt->execute();
$products_result = $stmt->get_result();

// Получение списка категорий для выпадающего списка
$stmt = $conn->prepare("SELECT id, name FROM categories");
$stmt->execute();
$categories_result = $stmt->get_result();

// Получение списка групп для выпадающего списка
$stmt = $conn->prepare("SELECT id, name FROM groups");
$stmt->execute();
$groups_result = $stmt->get_result();

// Вывод формы для добавления/редактирования товара
echo "<h2>Управление товарами</h2>";
echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SCRIPT"]) . "'>";
echo "Выберите товар: <select name='product_id'>";
echo "<option value=''>Выберите товар</option>";
while ($row = $products_result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
}
echo "</select><br>";
echo "Название товара: <input type='text' name='product_name'><br>";
echo "Описание товара: <textarea name='product_description'></textarea><br>";
echo "Цена товара: <input type='number' step='0.01' name='product_price'><br>";
echo "Категория товара: <select name='product_category'>";
echo "<option value=''>Выберите категорию</option>";
while ($row = $categories_result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
}
echo "</select><br>";
echo "Скрыть для групп: <select name='hidden_for_groups'>";
echo "<option value=''>Выберите группы</option>";
while ($row = $groups_result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
}
echo "</select><br>";
echo "<input type='submit' value='Сохранить'>";
echo "</form>";

$conn->close();
?>