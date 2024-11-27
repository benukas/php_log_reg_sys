<!-- pagrindinis loginas, pirmas puslapis saite -->

<?php
session_start();
require_once "login.php";
require_once "formosdb.php";

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["person_username"];
    $password = $_POST["person_password"];

    $userAuth = new UserAuthentication();

    $error_message = $userAuth->authenticate($username, $password);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Prisijungimas</title>
</head>

<body>
    <div class="container">
        <h2>Prisijungimas</h2>
        <form action="index.php" method="post">
            <label for="person_username">Jūsų vartotojo vardas:</label><br>
            <input type="text" name="person_username" required><br><br>

            <label for="person_password">Jūsų slaptažodis:</label><br>
            <input type="password" name="person_password" required><br><br>

            <input type="submit" value="Prisijungti"> <br><br>
            Neturite paskyros?: <input type="button" value="Registracija" onclick="window.location.href='register.php';"><br>

            <?php if (!empty($error_message)) : ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>
