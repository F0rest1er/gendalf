<?php

require_once 'db.php';

// Обработка формы для управления группами
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["group_name"])) {
    $group_id = isset($_POST["group_id"]) ? $_POST["group_id"] : null;
    $group_name = $_POST["group_name"];
    $group_description = $_POST["group_description"];

    if ($group_id) {
        // Обновление существующей группы
        $stmt = $conn->prepare("UPDATE groups SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $group_name, $group_description, $group_id);
    } else {
        // Создание новой группы
        $stmt = $conn->prepare("INSERT INTO groups (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $group_name, $group_description);
    }

    if ($stmt->execute()) {
        echo "Группа сохранена успешно!";
    } else {
        echo "Ошибка при сохранении группы: " . $stmt->error;
    }

    $stmt->close();
}

// Обработка формы для управления пользователями
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_login"])) {
    $user_id = isset($_POST["user_id"]) ? $_POST["user_id"] : null;
    $user_login = $_POST["user_login"];
    $user_password = $_POST["user_password"];
    $user_group = $_POST["user_group"];

    // Проверяем, существует ли выбранная группа
    $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ?");
    $stmt->bind_param("i", $user_group);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($user_id) {
            // Обновление существующего пользователя
            $stmt = $conn->prepare("UPDATE users SET login = ?, password = ?, group_id = ? WHERE id = ?");
            $stmt->bind_param("ssii", $user_login, $user_password, $user_group, $user_id);
        } else {
            // Создание нового пользователя
            $stmt = $conn->prepare("INSERT INTO users (login, password, group_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $user_login, $user_password, $user_group);
        }

        if ($stmt->execute()) {
            echo "Пользователь сохранен успешно!";
        } else {
            echo "Ошибка при сохранении пользователя: " . $stmt->error;
        }
    } else {
        echo "Ошибка: Выбранная группа не существует.";
    }

    $stmt->close();
}

// Получение списка групп для выпадающего списка
$stmt = $conn->prepare("SELECT id, name FROM groups");
$stmt->execute();
$groups_result = $stmt->get_result();

// Получение списка пользователей для выпадающего списка
$stmt = $conn->prepare("SELECT u.id, u.login, u.group_id, g.name AS group_name
                       FROM users u
                       LEFT JOIN groups g ON u.group_id = g.id");
$stmt->execute();
$users_result = $stmt->get_result();

// Вывод формы для управления группами
echo "<h2>Управление группами</h2>";
echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SCRIPT"]) . "'>";
echo "Выберите группу: <select name='group_id'>";
echo "<option value=''>Создать новую группу</option>";
while ($row = $groups_result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
}
echo "</select><br>";
echo "Название группы: <input type='text' name='group_name'><br>";
echo "Описание группы: <textarea name='group_description'></textarea><br>";
echo "<input type='submit' value='Сохранить'>";
echo "</form>";

// Вывод формы для управления пользователями
echo "<h2>Управление пользователями</h2>";
echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SCRIPT"]) . "'>";
echo "Выберите пользователя: <select name='user_id'>";
echo "<option value=''>Создать нового пользователя</option>";
while ($row = $users_result->fetch_assoc()) {
    echo "<option value='" . $row["id"] . "'>" . $row["login"] . " - " . ($row["group_name"] ? $row["group_name"] : "Без группы") . "</option>";
}
echo "</select><br>";
echo "Логин: <input type='text' name='user_login'><br>";
echo "Пароль: <input type='password' name='user_password'><br>";
echo "Группа: <select name='user_group'>";
echo "<option value=''>Выберите группу</option>";
while ($row = $groups_result->fetch_assoc()) {
    $selected = ($row["id"] == $users_result->fetch_assoc()["group_id"]) ? "selected" : "";
    echo "<option value='" . $row["id"] . "' $selected>" . $row["name"] . "</option>";
}
echo "</select><br>";
echo "<input type='submit' value='Сохранить'>";
echo "</form>";

$conn->close();
?>