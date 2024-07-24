<?php
session_start();
include 'db.php'; // Pastikan koneksi ke database sudah benar

// Ambil data gaji
$sql = "SELECT * FROM Gaji";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Gaji</title>
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
        <h2 align="center">Daftar Gaji</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='gaji-table'><thead><tr><th>ID Gaji</th><th>Jumlah Gaji</th><th>Pegawai</th><th>Tanggal Gajian</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                // Fetch pegawai name
                $pegawai_sql = "SELECT nama FROM Pegawai WHERE id_pegawai = " . $row['id_pegawai'];
                $pegawai_result = $conn->query($pegawai_sql);
                $pegawai_name = $pegawai_result->fetch_assoc()['nama'];

                echo "<tr>
                    <td>{$row['id_gaji']}</td>
                    <td>{$row['jumlah_gaji']}</td>
                    <td>{$pegawai_name}</td>
                    <td>{$row['tanggal_gajian']}</td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p align='center'>Tidak ada data gaji.</p>";
        }
        ?>
    </div>
</body>
</html>
