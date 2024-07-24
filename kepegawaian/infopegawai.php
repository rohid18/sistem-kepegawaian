<?php
session_start();
include 'db.php'; // Pastikan ini adalah koneksi ke database

$message = '';

// Tambah atau Edit Pegawai
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id_pegawai = $_POST['id_pegawai'] ?? null;
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $id_jabatan = $_POST['id_jabatan'];

    if ($action == 'add') {
        $sql = "INSERT INTO Pegawai (nama, alamat, no_telepon, id_jabatan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $nama, $alamat, $no_telepon, $id_jabatan);
            if ($stmt->execute()) {
                $message = "Pegawai berhasil ditambahkan.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    } elseif ($action == 'update') {
        $sql = "UPDATE Pegawai SET nama = ?, alamat = ?, no_telepon = ?, id_jabatan = ? WHERE id_pegawai = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssi", $nama, $alamat, $no_telepon, $id_jabatan, $id_pegawai);
            if ($stmt->execute()) {
                $message = "Pegawai berhasil diupdate.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    }
}

// Ambil Data Pegawai untuk Edit
$pegawai = null;
if (isset($_GET['edit'])) {
    $id_pegawai = $_GET['edit'];
    $sql = "SELECT * FROM Pegawai WHERE id_pegawai = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_pegawai);
        $stmt->execute();
        $result = $stmt->get_result();
        $pegawai = $result->fetch_assoc();
        $stmt->close();
    }
}

// Ambil Daftar Pegawai untuk Tampilkan
$sql = "SELECT * FROM Pegawai";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pegawai</title>
    <link rel="stylesheet" href="dashboard.css">
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
    <div class="container">
        <h2 align="center">Manajemen Pegawai</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="<?php echo $pegawai ? 'update' : 'add'; ?>">
            <?php if ($pegawai): ?>
                <input type="hidden" name="id_pegawai" value="<?php echo $pegawai['id_pegawai']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" name="nama" id="nama" value="<?php echo $pegawai ? htmlspecialchars($pegawai['nama']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea name="alamat" id="alamat" required><?php echo $pegawai ? htmlspecialchars($pegawai['alamat']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="no_telepon">No. Telepon:</label>
                <input type="text" name="no_telepon" id="no_telepon" value="<?php echo $pegawai ? htmlspecialchars($pegawai['no_telepon']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="id_jabatan">Jabatan:</label>
                <select name="id_jabatan" id="id_jabatan" required>
                    <!-- Option values should be dynamically populated from Jabatan table -->
                    <?php
                    $jabatan_sql = "SELECT * FROM Jabatan";
                    $jabatan_result = $conn->query($jabatan_sql);
                    while ($jabatan = $jabatan_result->fetch_assoc()) {
                        echo "<option value=\"{$jabatan['id_jabatan']}\"" . ($pegawai && $pegawai['id_jabatan'] == $jabatan['id_jabatan'] ? ' selected' : '') . ">{$jabatan['nama_jabatan']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit"><?php echo $pegawai ? 'Update' : 'Tambah'; ?></button>
        </form>

        <h2 align="center">Daftar Pegawai</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID</th><th>Nama</th><th>Alamat</th><th>No. Telepon</th><th>Jabatan</th></tr>";
            while ($row = $result->fetch_assoc()) {
                // Fetch jabatan name
                $jabatan_sql = "SELECT nama_jabatan FROM Jabatan WHERE id_jabatan = " . $row['id_jabatan'];
                $jabatan_result = $conn->query($jabatan_sql);
                $jabatan_name = $jabatan_result->fetch_assoc()['nama_jabatan'];

                echo "<tr>
                    <td>{$row['id_pegawai']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['no_telepon']}</td>
                    <td>{$jabatan_name}</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data pegawai.";
        }
        ?>
    </div>
</body>
</html>
