<?php
session_start();
include 'db.php'; // Pastikan koneksi ke database sudah benar

$search_result = null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $id_pegawai = $_POST['id_pegawai'];

    if (!empty($id_pegawai)) {
        // Ambil data pegawai
        $sql = "SELECT * FROM Pegawai WHERE id_pegawai = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $message = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("i", $id_pegawai);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                if ($result->num_rows > 0) {
                    $search_result = $result->fetch_assoc(); // Ambil hasil pencarian
                } else {
                    $message = "Tidak ada pegawai dengan ID $id_pegawai.";
                }
            } else {
                $message = "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "Silakan masukkan ID pegawai untuk pencarian.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Pegawai</title>
    <link rel="stylesheet" type="text/css" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">       
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

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <h2 align="center">Pencarian Pegawai</h2>
		<fieldset>
        <div class="form-group">
            <label for="id_pegawai">Masukkan ID Pegawai:</label>
            <input type="text" id="id_pegawai" name="id_pegawai" required>
        </div>
        <div class="form-group">
            <button type="submit" name="search">Cari</button>
        </div>
		</fieldset>
    </form>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($search_result): ?>
        <h2 align="center">Hasil Pencarian ID Pegawai <?php echo htmlspecialchars($id_pegawai); ?></h2>
        <table>
            <tr>
                <th>ID Pegawai</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No Telepon</th>
                <th>Jabatan</th>
                <th>Gaji</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($search_result['id_pegawai']); ?></td>
                <td><?php echo htmlspecialchars($search_result['nama']); ?></td>
                <td><?php echo htmlspecialchars($search_result['alamat']); ?></td>
                <td><?php echo htmlspecialchars($search_result['no_telepon']); ?></td>
                <?php
                // Fetch jabatan name
                $jabatan_sql = "SELECT nama_jabatan FROM Jabatan WHERE id_jabatan = ?";
                $jabatan_stmt = $conn->prepare($jabatan_sql);
                if ($jabatan_stmt) {
                    $jabatan_stmt->bind_param("i", $search_result['id_jabatan']);
                    $jabatan_stmt->execute();
                    $jabatan_result = $jabatan_stmt->get_result();
                    if ($jabatan_result && $jabatan_result->num_rows > 0) {
                        $jabatan_name = $jabatan_result->fetch_assoc()['nama_jabatan'];
                    } else {
                        $jabatan_name = "Jabatan tidak ditemukan";
                    }
                    $jabatan_stmt->close();
                } else {
                    $jabatan_name = "Error preparing jabatan query: " . $conn->error;
                }

                // Fetch gaji amount
                $gaji_sql = "SELECT jumlah_gaji FROM Gaji WHERE id_pegawai = ?";
                $gaji_stmt = $conn->prepare($gaji_sql);
                if ($gaji_stmt) {
                    $gaji_stmt->bind_param("i", $search_result['id_pegawai']);
                    $gaji_stmt->execute();
                    $gaji_result = $gaji_stmt->get_result();
                    if ($gaji_result && $gaji_result->num_rows > 0) {
                        $gaji_amount = $gaji_result->fetch_assoc()['jumlah_gaji'];
                    } else {
                        $gaji_amount = "Gaji tidak ditemukan";
                    }
                    $gaji_stmt->close();
                } else {
                    $gaji_amount = "Error preparing gaji query: " . $conn->error;
                }
                ?>
                <td><?php echo htmlspecialchars($jabatan_name); ?></td>
                <td><?php echo htmlspecialchars($gaji_amount); ?></td>
            </tr>
        </table>
    <?php endif; ?>
</body>
</html>
