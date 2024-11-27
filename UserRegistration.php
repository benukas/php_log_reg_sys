<!-- pilna registracijos logika -->

<?php
require_once "formosdb.php";

class UserRegistration {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function registerUser($username, $password, $cityId, $hobbies, $aboutMe) {
        $conn = $this->db->getConnection();
        $username = $conn->real_escape_string($username);
        $password = $conn->real_escape_string($password);
        $aboutMe = $conn->real_escape_string($aboutMe);
    
        if ($cityId <= 0) {
            return "<div class=error>Pasirinkite miestą iš sąrašo.</div class=error>";
        }
    
        $checkQuery = "SELECT * FROM person_info WHERE person_username = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $checkResult = $stmt->get_result();
    
        if ($checkResult->num_rows > 0) {
            return "<div class=error> Vartotojo vardas užimtas. Prašome pasirinkti kitą vartotojo vardą. </div>";
        }
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hobbiesStr = implode(',', $hobbies);
    
        $sql = "INSERT INTO person_info (person_username, person_password_encrypt, person_city_id, person_hobbies, person_about_me)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $username, $hashedPassword, $cityId, $hobbiesStr, $aboutMe);
    
        if ($stmt->execute()) {
            return "<div class=success>Sėkmingai užsiregistravote. Ačiū.</div>";
        } else {
            return "<div class=error>Oops... Kažkas nepavyko:</div> " . $stmt->error;
        }
    }
    
    
}
?>
