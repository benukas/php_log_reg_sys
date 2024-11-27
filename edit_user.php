<?php
session_start();

if (!isset($_SESSION['login_user'])) {
    header("Location: index.php");
    exit;
}

require_once "formosdb.php";

$username = $_SESSION['login_user'];
$isAdmin = ($username === 'admin');

if (!$isAdmin) {
    header("Location: yourinfo.php");
    exit;
}

if (isset($_GET['username'])) {
    $editUsername = $_GET['username'];

    $db = new Database();
    $conn = $db->getConnection();

    // Fetching user information, including city name from the Cities table
    $sql = "SELECT person_info.*, cities.city_name 
            FROM person_info 
            LEFT JOIN cities ON person_info.person_city_id = cities.city_id 
            WHERE person_username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $editUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $hobbyIds = explode(',', $user['person_hobbies']);
        $hobbyNames = [];
        foreach ($hobbyIds as $hobbyId) {
            $hobbySql = "SELECT name FROM hobbies WHERE id = ?";
            $hobbyStmt = $conn->prepare($hobbySql);
            $hobbyStmt->bind_param("i", $hobbyId);
            $hobbyStmt->execute();
            $hobbyResult = $hobbyStmt->get_result();
            $hobbyRow = $hobbyResult->fetch_assoc();
            $hobbyNames[] = $hobbyRow['name'];
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["hide"])) {
                $insertSql = "INSERT INTO hidden_users (person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me) 
                              SELECT person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me 
                              FROM person_info 
                              WHERE person_username = ?";
                $stmt = $conn->prepare($insertSql);
                $stmt->bind_param("s", $editUsername);
                $stmt->execute();

                $deleteSql = "DELETE FROM person_info WHERE person_username = ?";
                $stmt = $conn->prepare($deleteSql);
                $stmt->bind_param("s", $editUsername);
                $stmt->execute();

                header("Location: yourinfo.php");
                session_write_close();
                exit;
            } elseif (isset($_POST["unhide"])) {
                $insertSql = "INSERT INTO person_info (person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me) 
                              SELECT person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me 
                              FROM hidden_users 
                              WHERE person_username = ?";
                $stmt = $conn->prepare($insertSql);
                $stmt->bind_param("s", $editUsername);
                $stmt->execute();

                $deleteSql = "DELETE FROM hidden_users WHERE person_username = ?";
                $stmt = $conn->prepare($deleteSql);
                $stmt->bind_param("s", $editUsername);
                $stmt->execute();

                header("Location: yourinfo.php");
                session_write_close();
                exit;
            } else {
                $cityId = $_POST['city']; // Using city_id for updates
                $hobbies = isset($_POST['hobbies']) ? implode(',', $_POST['hobbies']) : '';
                $aboutMe = $_POST['about_me'];

                $updateSql = "UPDATE person_info SET person_city_id = ?, person_hobbies = ?, person_about_me = ? WHERE person_username = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("isss", $cityId, $hobbies, $aboutMe, $editUsername);
                $updateStmt->execute();

                header("Location: yourinfo.php");
                session_write_close();
                exit;
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Redaguoti vartotoją: <?php echo $editUsername; ?></title>
</head>
<body>
    <div class="container">
        <h2>Redaguoti vartotoją: <?php echo $editUsername; ?></h2>
        <p><strong>Vartotojo vardas:</strong> <?php echo $user['person_username']; ?></p>
        <p><strong>Miestas:</strong> <?php echo $user['city_name']; ?></p>
        <p><strong>Hobiai:</strong> <?php echo implode(', ', $hobbyNames); ?></p>
        <p><strong>Apie mane:</strong> <?php echo $user['person_about_me']; ?></p>

        <form action="edit_user.php?username=<?php echo $editUsername; ?>" method="post">
            <label for="city">Miestas:</label>
            <select id="city" name="city" required>
                <option value="" selected disabled>Nepasirinkta</option>
                <?php
                $citySql = "SELECT * FROM cities";
                $cityResult = $conn->query($citySql);
                while ($cityRow = $cityResult->fetch_assoc()) {
                    echo '<option value="' . $cityRow['city_id'] . '" ' . ($user['person_city_id'] == $cityRow['city_id'] ? 'selected' : '') . '>' . $cityRow['city_name'] . '</option>';
                }
                ?>
            </select><br><br>

            <label for="hobbies">Hobiai:</label><br>
            <?php
            $hobbySql = "SELECT * FROM hobbies";
            $result = $conn->query($hobbySql);
            while ($row = $result->fetch_assoc()) {
                $checked = in_array($row['id'], explode(',', $user['person_hobbies'])) ? 'checked' : '';
                echo '<input type="checkbox" id="hobby'.$row['id'].'" name="hobbies[]" value="'.$row['id'].'" '.$checked.'>';
                echo '<label for="hobby'.$row['id'].'">'.$row['name'].'</label><br>';
            }
            ?><br>

            <label for="about_me">Apie mane:</label><br>
            <textarea id="about_me" name="about_me" rows="4" cols="50"><?php echo $user['person_about_me']; ?></textarea><br><br>

            <input type="submit" name="hide" value="Paslėpti vartotoją">
            <input type="submit" name="unhide" value="Rodyti vartotoją">
            <input type="submit" value="Patvirtinti pakeitimus">
            <input type="button" value="Atgal" onclick="window.location.href='yourinfo.php';">
        </form>
    </div>
</body>
</html>
<?php
    }
} else {
    $hiddenUsersSql = "SELECT * FROM hidden_users";
    $hiddenUsersResult = $conn->query($hiddenUsersSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Paslėpti vartotojai</title>
</head>
<body>
    <div class="container">
        <h2>Paslėpti vartotojai</h2>
        <table>
            <tr>
                <th>Vartotojo vardas</th>
                <th>Miestas</th>
                <th>Hobiai</th>
                <th>Veiksmai</th>
            </tr>
            <?php
            while ($hiddenUser = $hiddenUsersResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $hiddenUser['person_username'] . "</td>";
                echo "<td>" . $hiddenUser['city_name'] . "</td>"; // Updated to fetch city_name dynamically
                $hobbyIds = explode(',', $hiddenUser['person_hobbies']);
                $hobbyNames = [];
                foreach ($hobbyIds as $hobbyId) {
                    $hobbySql = "SELECT name FROM hobbies WHERE id = ?";
                    $hobbyStmt = $conn->prepare($hobbySql);
                    $hobbyStmt->bind_param("i", $hobbyId);
                    $hobbyStmt->execute();
                    $hobbyResult = $hobbyStmt->get_result();
                    $hobbyRow = $hobbyResult->fetch_assoc();
                    $hobbyNames[] = $hobbyRow['name'];
                }
                echo "<td>" . implode(', ', $hobbyNames) . "</td>";
                echo '<td><a href="edit_user.php?username=' . $hiddenUser['person_username'] . '">Rodyti</a></td>';
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
<?php
}
?>
