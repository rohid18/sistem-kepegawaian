<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $username;
        
        if ($row['role'] == 'admin') {
            $_SESSION['role'] = 'admin';
            header('Location: dashboard_admin.php');
        } else {
            $_SESSION['role'] = 'pegawai';
            header('Location: dashboard_user.php');
        }
        exit();
    } else {
        $error = "Username or password incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Informasi Kepegawaian</title>
    <link rel="stylesheet" type="text/css" href="login1.css">
</head>
<body>
    <div class="login-container">
	    <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <h2>Login Sistem Informasi Kepegawaian</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
       <p>Belum punya akun? <a href="registrasi.php">Registrasi di sini</a></p>
    </div>
</body>
</html>
