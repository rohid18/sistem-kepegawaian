<?php
session_start();
include 'db.php'; 

$search_results = [];
$message = '';
$total_gaji = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $id_pegawai = $_POST['id_pegawai'];
    $tanggal_gajian = $_POST['tanggal_gajian'];

    if (!empty($id_pegawai) && !empty($tanggal_gajian)) {
        // Ambil data gaji berdasarkan ID Pegawai dan Tanggal Gajian
        $sql = "SELECT Gaji.*, Pegawai.nama FROM Gaji
                JOIN Pegawai ON Gaji.id_pegawai = Pegawai.id_pegawai
                WHERE Gaji.id_pegawai = ? AND Gaji.tanggal_gajian = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $message = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("is", $id_pegawai, $tanggal_gajian);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                if ($result->num_rows > 0) {
                    $search_results = $result->fetch_all(MYSQLI_ASSOC);

                    foreach ($search_results as &$row) {
                        // Ambil tunjangan
                        $tunjangan_sql = "SELECT SUM(jumlah_tunjangan) AS total_tunjangan FROM Tunjangan WHERE id_pegawai = ?";
                        $tunjangan_stmt = $conn->prepare($tunjangan_sql);
                        if ($tunjangan_stmt) {
                            $tunjangan_stmt->bind_param("i", $id_pegawai);
                            $tunjangan_stmt->execute();
                            $tunjangan_result = $tunjangan_stmt->get_result();
                            $tunjangan_data = $tunjangan_result->fetch_assoc();
                            $row['jumlah_tunjangan'] = $tunjangan_data['total_tunjangan'] ?? 0;
                            $tunjangan_stmt->close();
                        } else {
                            $row['jumlah_tunjangan'] = "Error preparing tunjangan query: " . $conn->error;
                        }

                        // Hitung total gajian
                        $row['total_gajian'] = $row['jumlah_gaji'] + $row['jumlah_tunjangan'];

                        // Tambahkan ke total keseluruhan
                        $total_gaji += $row['total_gajian'];
                    }
                } else {
                    $message = "Tidak ada gaji untuk ID Pegawai $id_pegawai dengan tanggal gajian $tanggal_gajian.";
                }
            } else {
                $message = "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "Silakan masukkan ID Pegawai dan Tanggal Gajian untuk pencarian.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Gaji Berdasarkan ID Pegawai dan Tanggal Gajian</title>
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
        <h2 align="center">Pencarian Gaji Berdasarkan ID Pegawai dan Tanggal Gajian</h2>
		<fieldset>
        <div class="form-group">
            <label for="id_pegawai">ID Pegawai:</label>
            <input type="number" id="id_pegawai" name="id_pegawai" required>
        </div>
        <div class="form-group">
            <label for="tanggal_gajian">Tanggal Gajian:</label>
            <input type="date" id="tanggal_gajian" name="tanggal_gajian" required>
        </div>
        <div class="form-group">
            <button type="submit" name="search">Cari</button>
        </div>
		</fieldset>
    </form>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($search_results)): ?>
        <h2 align="center">Hasil Pencarian untuk ID Pegawai <?php echo htmlspecialchars($id_pegawai); ?> dengan Tanggal Gajian <?php echo htmlspecialchars($tanggal_gajian); ?></h2>
        <table>
            <tr>
                <th>ID Gaji</th>
                <th>Nama Pegawai</th>
                <th>Jumlah Gaji</th>
                <th>Tanggal Gajian</th>
                <th>Jumlah Tunjangan</th>
                <th>Total Gajian</th>
            </tr>
            <?php foreach ($search_results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_gaji']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['jumlah_gaji']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal_gajian']); ?></td>
                    <td><?php echo htmlspecialchars($row['jumlah_tunjangan']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_gajian']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" align="right"><strong>Total Keseluruhan:</strong></td>
                <td><?php echo htmlspecialchars($total_gaji); ?></td>
            </tr>
        </table>
    <?php endif; ?>
</body>
</html>