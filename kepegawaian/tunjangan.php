<?php
session_start();
include 'db.php'; // Pastikan ini adalah koneksi ke database

$message = '';

// Tambah atau Edit Tunjangan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id_tunjangan = $_POST['id_tunjangan'] ?? null;
    $jenis_tunjangan = $_POST['jenis_tunjangan'];
    $jumlah_tunjangan = $_POST['jumlah_tunjangan'];
    $id_pegawai = $_POST['id_pegawai'];

    if ($action == 'add') {
        $sql = "INSERT INTO Tunjangan (jenis_tunjangan, jumlah_tunjangan, id_pegawai) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssd", $jenis_tunjangan, $jumlah_tunjangan, $id_pegawai);
            if ($stmt->execute()) {
                $message = "Tunjangan berhasil ditambahkan.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    } elseif ($action == 'update') {
        $sql = "UPDATE Tunjangan SET jenis_tunjangan = ?, jumlah_tunjangan = ?, id_pegawai = ? WHERE id_tunjangan = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssdi", $jenis_tunjangan, $jumlah_tunjangan, $id_pegawai, $id_tunjangan);
            if ($stmt->execute()) {
                $message = "Tunjangan berhasil diupdate.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    }
}

// Hapus Tunjangan
if (isset($_GET['delete'])) {
    $id_tunjangan = $_GET['delete'];
    $sql = "DELETE FROM Tunjangan WHERE id_tunjangan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_tunjangan);
        if ($stmt->execute()) {
            $message = "Tunjangan berhasil dihapus.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Query database gagal.";
    }
}

// Ambil Data Tunjangan untuk Edit
$tunjangan = null;
if (isset($_GET['edit'])) {
    $id_tunjangan = $_GET['edit'];
    $sql = "SELECT * FROM Tunjangan WHERE id_tunjangan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_tunjangan);
        $stmt->execute();
        $result = $stmt->get_result();
        $tunjangan = $result->fetch_assoc();
        $stmt->close();
    }
}

// Ambil Daftar Tunjangan untuk Tampilkan
$sql = "SELECT * FROM Tunjangan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tunjangan</title>
    <link rel="stylesheet" href="dashboard.css">
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
    <div class="container">
        <h2 align="center">Manajemen Tunjangan</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="<?php echo $tunjangan ? 'update' : 'add'; ?>">
            <?php if ($tunjangan): ?>
                <input type="hidden" name="id_tunjangan" value="<?php echo $tunjangan['id_tunjangan']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="jenis_tunjangan">Jenis Tunjangan:</label>
                <input type="text" name="jenis_tunjangan" id="jenis_tunjangan" value="<?php echo $tunjangan ? htmlspecialchars($tunjangan['jenis_tunjangan']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="jumlah_tunjangan">Jumlah Tunjangan:</label>
                <input type="number" step="0.01" name="jumlah_tunjangan" id="jumlah_tunjangan" value="<?php echo $tunjangan ? htmlspecialchars($tunjangan['jumlah_tunjangan']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="id_pegawai">Pegawai:</label>
                <select name="id_pegawai" id="id_pegawai" required>
                    <!-- Option values should be dynamically populated from Pegawai table -->
                    <?php
                    $pegawai_sql = "SELECT * FROM Pegawai";
                    $pegawai_result = $conn->query($pegawai_sql);
                    while ($pegawai = $pegawai_result->fetch_assoc()) {
                        echo "<option value=\"{$pegawai['id_pegawai']}\"" . ($tunjangan && $tunjangan['id_pegawai'] == $pegawai['id_pegawai'] ? ' selected' : '') . ">{$pegawai['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit"><?php echo $tunjangan ? 'Update' : 'Tambah'; ?></button>
        </form>

        <h2 align="center">Daftar Tunjangan</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID</th><th>Jenis Tunjangan</th><th>Jumlah Tunjangan</th><th>Pegawai</th><th>Aksi</th></tr>";
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
                    <td>
                        <a href='tunjangan.php?edit={$row['id_tunjangan']}'>Edit</a> | 
                        <a href='tunjangan.php?delete={$row['id_tunjangan']}' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                    </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data tunjangan.";
        }
        ?>
    </div>
</body>
</html>
