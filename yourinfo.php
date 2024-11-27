<?php
session_start();

if (!isset($_SESSION['login_user'])) {
    header("Location: index.php");
    exit;
}

require_once "formosdb.php";

$username = $_SESSION['login_user'];

$db = new Database();
$conn = $db->getConnection();

// Fetch user information, including city name
$sql = "SELECT person_info.*, cities.city_name 
        FROM person_info 
        LEFT JOIN cities ON person_info.person_city_id = cities.city_id 
        WHERE person_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // Check if the user is admin
    $isAdmin = ($user['person_username'] === 'admin');

    $hobbyNames = [];
    if (isset($user['person_hobbies'])) {
        $hobbyIds = explode(',', $user['person_hobbies']);
        foreach ($hobbyIds as $hobbyId) {
            $hobbySql = "SELECT name FROM hobbies WHERE id = ?";
            $hobbyStmt = $conn->prepare($hobbySql);
            $hobbyStmt->bind_param("i", $hobbyId);
            $hobbyStmt->execute();
            $hobbyResult = $hobbyStmt->get_result();
            $hobbyRow = $hobbyResult->fetch_assoc();
            $hobbyNames[] = $hobbyRow['name'];
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <title>Jūsų informacija</title>
    </head>
    <body>
        <div class="container">
            <h2>Jūsų informacija</h2>
            <p><strong>Vartotojo vardas:</strong> <?php echo $user['person_username']; ?></p>
            <p><strong>Miestas:</strong> <?php echo $user['city_name']; ?></p>
            <p><strong>Hobiai:</strong> <?php echo implode(', ', $hobbyNames); ?></p>
            <p><strong>Apie mane:</strong> <?php echo $user['person_about_me']; ?></p>

            <!-- Admin-specific content -->
            <?php if ($isAdmin): ?>
            <style>
                body {
                    background-image: url('https://wallpaperxyz.com/wp-content/uploads/Gif-Animated-Wallpaper-Background-Full-HD-Free-Download-for-PC-Macbook-261121-Wallpaperxyz.com-13.gif');
                    background-size: cover;
                    background-repeat: no-repeat;
                }
            </style>
            <h2>Vartotojų sąrašas</h2>
            <ul>
                <?php
                $usersSql = "SELECT person_username FROM person_info";
                $usersResult = $conn->query($usersSql);
                while ($userRow = $usersResult->fetch_assoc()) {
                    echo '<li><a href="edit_user.php?username=' . $userRow['person_username'] . '">' . $userRow['person_username'] . '</a></li>';
                }
                ?>
            </ul>

            <?php
            $hiddenUsersSql = "SELECT hidden_users.*, cities.city_name 
                               FROM hidden_users 
                               LEFT JOIN cities ON hidden_users.person_city_id = cities.city_id";
            $hiddenUsersResult = $conn->query($hiddenUsersSql);
            if ($hiddenUsersResult->num_rows > 0): ?>
                <h2>Paslėpti vartotojai</h2>
                <table>
                    <tr>
                        <th>Vartotojo vardas</th>
                        <th>Miestas</th>
                        <th>Veiksmai</th>
                    </tr>
                    <?php while ($hiddenUser = $hiddenUsersResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $hiddenUser['person_username']; ?></td>
                            <td><?php echo $hiddenUser['city_name']; ?></td>
                            <td>
                                <form action="update_info.php" method="post">
                                    <input type="hidden" name="username" value="<?php echo $hiddenUser['person_username']; ?>">
                                    <input type="submit" name="unhide" value="Rodyti">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Form for all users to update their info -->
            <h2>Redaguoti vartotoją:</h2>
            <form action="update_info.php" method="post">
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
                
                <input type="submit" value="Išsaugoti pakeitimus">
                <input type="button" value="Atsijungti" onclick="window.location.href='logout.php';">
            </form>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Vartotojas nerastas";
}
?>
