<!-- logino logika ir tikrinimai su UserAuthentication klase (objektais)-->
<?php
require_once "formosdb.php";

class UserAuthentication {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function authenticate($username, $password) {
        $conn = $this->db->getConnection();
        $username = $conn->real_escape_string($username);
    
        $sql = "SELECT * FROM person_info WHERE person_username='$username'";
        $result = $conn->query($sql);
    
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['person_password_encrypt'];
    
            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION['login_user'] = $username;
                
                $_SESSION['login_role'] = ($username === 'admin') ? 'admin' : 'user';
                
                header("Location: yourinfo.php");
                exit;
            } else {
                return "Jūsų prisijungimo duomenys neteisingi.";
            }
        } else {
            return "Vartotojas su tokiu vardu neegzistuoja.";
        }
    }
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["person_username"];
    $password = $_POST["person_password"];

    $userAuth = new UserAuthentication();
    $result = $userAuth->authenticate($username, $password);

    if (is_string($result)) {
        $error_message = $result;
    }
}
?>
