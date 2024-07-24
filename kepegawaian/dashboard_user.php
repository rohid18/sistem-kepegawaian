<?php
include 'db.php';

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pegawai') {
    header('Location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Dashboard</h2>
        <p class="welcome-message">Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <div class="menu">
            <?php if ($_SESSION['role'] == 'pegawai'): ?>
        <nav class="menu">
            <a href="infopegawai.php">Profil Pegawai</a>
            <a href="lihatgaji.php">Lihat Gaji</a>
            <a href="lihat.php">Lihat Tunjangan</a>
        </nav>
            <?php else: ?>
                <p>Anda tidak memiliki akses ke menu ini.</p>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
