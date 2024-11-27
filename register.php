<!-- siek tiek registracijos logikos ir pats formatas -->
<?php
require_once "UserRegistration.php";

$registrationResult = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["person_username"];
    $password = $_POST["person_password"];
    $verifyPassword = $_POST["verify_password"];
    $cityId = $_POST["city"]; 
    $hobbyIds = isset($_POST["hobby"]) ? $_POST["hobby"] : [];
    $aboutMe = $_POST["about_me"];

    if ($password !== $verifyPassword) {
        $registrationResult = "Slaptažodžiai nesutampa";
    } else {
        $userReg = new UserRegistration();
        $registrationResult = $userReg->registerUser($username, $password, $cityId, $hobbyIds, $aboutMe);
    }
}
$messageClass = ""; // Initialize the message class

if (!empty($registrationResult)) {
    if ($registrationResult === "Slaptažodžiai nesutampa") {
        $messageClass = 'error';
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .error {
            color: white;
            background-color: #ff6347;
            border: 1px solid #b22222;
            padding: 10px;
            margin-bottom: 15px;
        }
        .success {
            color: black;
            background-color: #90EE90;
            border: 1px solid #006400;
            padding: 10px;
            margin-bottom: 15px;
        }

        .miestas {
            width: 101%;
        }
        
    </style>
    <title>Registracija</title>
</head>
<body>
    <div class="container">
        <h2>Registracija</h2>
        <?php if (!empty($registrationResult)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo $registrationResult; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <label for="person_username">Vartotojo vardas:</label><br>
            <input type="text" id="person_username" name="person_username" required><br><br>
            
            <label for="person_password">Slaptažodis:</label><br>
            <input type="password" id="person_password" name="person_password" required><br><br>
            
            <label for="verify_password">Patvirtinkite slaptažodį:</label><br>
            <input type="password" id="verify_password" name="verify_password" required><br><br>
            
            <label for="city">Miestas:</label><br>
            <select id="city" class="miestas" name="city" required>
                <option value="" selected disabled>Pasirinkite miestą</option>
                <?php
                require_once "formosdb.php";
                $db = new Database();
                $conn = $db->getConnection();
                $sql = "SELECT * FROM Cities";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['city_id'] . '">' . $row['city_name'] . '</option>';
                }
                ?>
            </select><br><br>


            <label for="hobby">Pasirinkite pomėgius:</label><br>
            <?php
            $sql = "SELECT * FROM hobbies";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo '<input type="checkbox" id="hobby'.$row['id'].'" name="hobby[]" value="'.$row['id'].'">';
                echo '<label for="hobby'.$row['id'].'">'.$row['name'].'</label><br>';
            }
            ?><br>

            <label for="about_me">Apie mane:</label><br>
            <textarea id="about_me" name="about_me" rows="4" cols="50" required></textarea><br><br>
            
            <input type="submit" value="Registruotis">
            <input type="button" value="Aš jau prisiregistravęs" onclick="window.location.href='index.php';">
        </form>
    </div>
</body>
</html>
