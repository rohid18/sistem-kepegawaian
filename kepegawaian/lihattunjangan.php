<?php
session_start();
include 'db.php'; // Pastikan ini adalah koneksi ke database

// Ambil Daftar Tunjangan untuk Tampilkan
$sql = "SELECT * FROM Tunjangan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tunjangan</title>
    <link rel="stylesheet" href="info.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="menu">
            <a href="infopegawai.php">Manajemen Pegawai</a>
            <a href="lihatgaji.php">Lihat Gaji</a>
            <a href="lihattunjangan.php">Lihat Tunjangan</a>
        </nav>
        
        <form action="logout.php" method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
    <div class="content">
        <h2 align="center">Daftar Tunjangan</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='tunjangan-table'><thead><tr><th>ID</th><th>Jenis Tunjangan</th><th>Jumlah Tunjangan</th><th>Pegawai</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                // Fetch pegawai name
                $pegawai_sql = "SELECT nama FROM Pegawai WHERE id_pegawai = " . $row['id_pegawai'];
                $pegawai_result = $conn->query($pegawai_sql);
                $pegawai_name = $pegawai_result->fetch_assoc()['nama'];

                echo "<tr>
                    <td>{$row['id_tunjangan']}</td>
                    <td>{$row['jenis_tunjangan']}</td>
                    <td>{$row['jumlah_tunjangan']}</td>
                    <td>{$pegawai_name}</td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p align='center'>Tidak ada data tunjangan.</p>";
        }
        ?>
    </div>
</body>
</html>
