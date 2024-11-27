<!-- cia atliekama user'io informacijos atnaujinimo logika, vyksta dalykeliai su database-->

<?php
session_start();

if (!isset($_SESSION['login_user'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["unhide"])) {
        $username = $_POST["username"];

        require_once "formosdb.php";

        $db = new Database();
        $conn = $db->getConnection();

        $is_hidden = false;
        $checkSql = "SELECT * FROM hidden_users WHERE person_username =?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $is_hidden = true;
        }

        if ($is_hidden) {
            $logSql = "SELECT * FROM hidden_users WHERE person_username =?";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param("s", $username);
            $logStmt->execute();
            $logResult = $logStmt->get_result();
            $logData = $logResult->fetch_assoc();
            error_log("Before Unhide - hidden_users: ". print_r($logData, true));

            $insertSql = "INSERT INTO person_info (person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me) 
                          SELECT person_username, person_password_encrypt, person_city, person_city_id, person_hobbies, person_about_me 
                          FROM hidden_users WHERE person_username =?";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            $logSql = "SELECT * FROM person_info WHERE person_username =?";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param("s", $username);
            $logStmt->execute();
            $logResult = $logStmt->get_result();
            $logData = $logResult->fetch_assoc();
            error_log("After Unhide - person_info: ". print_r($logData, true));

            $deleteSql = "DELETE FROM hidden_users WHERE person_username =?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("s", $username);
            $deleteStmt->execute();

            header("Location: yourinfo.php");
            session_regenerate_id(true);
            exit;
        } else {
            header("Location: yourinfo.php");
            exit;
        }
    } elseif (isset($_POST["city"]) && isset($_POST["hobbies"]) && isset($_POST["about_me"])) {

        $username = $_SESSION['login_user'];
        $cityName = $_POST["city"];
        $hobbies = implode(',', $_POST["hobbies"]);
        $about_me = $_POST["about_me"];
    
        require_once "formosdb.php";
    
        $db = new Database();
        $conn = $db->getConnection();
    
        $cityId = $_POST["city"];
        $hobbies = implode(',', $_POST["hobbies"]);
        $about_me = $_POST["about_me"];
        
        $updateSql = "UPDATE person_info SET person_city_id =?, person_hobbies =?, person_about_me =? WHERE person_username =?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("isss", $cityId, $hobbies, $about_me, $username);
        $stmt->execute();
        
    
        header("Location: yourinfo.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}
?>
