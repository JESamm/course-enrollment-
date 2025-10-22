<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = hash('sha256', $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
    body {
  ;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
}
    body {
        background-image: url("background.jpg")
        font-family: Arial;
        
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    form {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 300px;
    }
    h2 { text-align: center; }
    input {
        width: 100%;
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        width: 100%;
        cursor: pointer;
    }
    button:hover { background: #0056b3; }
    .error { color: red; text-align: center; }
</style>
</head>
<body>
<form method="post">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
</body>
</html>
