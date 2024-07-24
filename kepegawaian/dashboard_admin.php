<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
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
        <h2 class="welcome-message">Selamat datang, <?php echo $_SESSION['username']; ?></h2>
      
        <nav class="menu">
            <a href="jabatan.php">Manajemen Jabatan</a>
            <a href="pegawai.php">Manajemen Pegawai</a>
            <a href="gaji.php">Manajemen Gaji</a>
            <a href="tunjangan.php">Manajemen Tunjangan</a>
            <a href="perid.php">Informasi PerID Pegawai</a>
            <a href="pertgl.php">Informasi Pertanggal</a>
            <a href="periode.php">Informasi Periode</a>
            <a href="peridtgl.php">Informasi PerID Tanggal</a>
        </nav>
        
        <form action="logout.php" method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</body>
</html>
