<?php
session_start();
include 'db.php'; // Pastikan ini adalah koneksi ke database

$message = '';

// Tambah atau Edit Gaji
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $id_gaji = $_POST['id_gaji'] ?? null;
    $jumlah_gaji = $_POST['jumlah_gaji'];
    $id_pegawai = $_POST['id_pegawai'];
    $tanggal_gajian = $_POST['tanggal_gajian']; // Ambil tanggal gajian dari form

    if ($action == 'add') {
        $sql = "INSERT INTO Gaji (jumlah_gaji, id_pegawai, tanggal_gajian) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("dis", $jumlah_gaji, $id_pegawai, $tanggal_gajian);
            if ($stmt->execute()) {
                $message = "Gaji berhasil ditambahkan.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    } elseif ($action == 'update') {
        $sql = "UPDATE Gaji SET jumlah_gaji = ?, id_pegawai = ?, tanggal_gajian = ? WHERE id_gaji = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("diss", $jumlah_gaji, $id_pegawai, $tanggal_gajian, $id_gaji);
            if ($stmt->execute()) {
                $message = "Gaji berhasil diupdate.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    }
}

// Hapus Gaji
if (isset($_GET['delete'])) {
    $id_gaji = $_GET['delete'];
    $sql = "DELETE FROM Gaji WHERE id_gaji = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_gaji);
        if ($stmt->execute()) {
            $message = "Gaji berhasil dihapus.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Query database gagal.";
    }
}

// Ambil Data Gaji untuk Edit
$gaji = null;
if (isset($_GET['edit'])) {
    $id_gaji = $_GET['edit'];
    $sql = "SELECT * FROM Gaji WHERE id_gaji = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_gaji);
        $stmt->execute();
        $result = $stmt->get_result();
        $gaji = $result->fetch_assoc();
        $stmt->close();
    }
}

// Ambil Daftar Gaji untuk Tampilkan
$sql = "SELECT * FROM Gaji";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Gaji</title>
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
        <h2 align="center">Manajemen Gaji</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="<?php echo $gaji ? 'update' : 'add'; ?>">
            <?php if ($gaji): ?>
                <input type="hidden" name="id_gaji" value="<?php echo htmlspecialchars($gaji['id_gaji']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="jumlah_gaji">Jumlah Gaji:</label>
                <input type="number" step="0.01" name="jumlah_gaji" id="jumlah_gaji" value="<?php echo $gaji ? htmlspecialchars($gaji['jumlah_gaji']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="id_pegawai">Pegawai:</label>
                <select name="id_pegawai" id="id_pegawai" required>
                    <!-- Option values should be dynamically populated from Pegawai table -->
                    <?php
                    $pegawai_sql = "SELECT * FROM Pegawai";
                    $pegawai_result = $conn->query($pegawai_sql);
                    while ($pegawai = $pegawai_result->fetch_assoc()) {
                        echo "<option value=\"{$pegawai['id_pegawai']}\"" . ($gaji && $gaji['id_pegawai'] == $pegawai['id_pegawai'] ? ' selected' : '') . ">{$pegawai['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_gajian">Tanggal Gajian:</label>
                <input type="date" name="tanggal_gajian" id="tanggal_gajian" value="<?php echo $gaji ? htmlspecialchars($gaji['tanggal_gajian']) : ''; ?>" required>
            </div>
            <button type="submit"><?php echo $gaji ? 'Update' : 'Tambah'; ?></button>
        </form>

        <h2 align="center">Daftar Gaji</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID Gaji</th><th>Jumlah Gaji</th><th>Pegawai</th><th>Tanggal Gajian</th><th>Aksi</th></tr>";
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
                    <td>
                        <a href='gaji.php?edit={$row['id_gaji']}'>Edit</a> | 
                        <a href='gaji.php?delete={$row['id_gaji']}' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                    </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data gaji.";
        }
        ?>
    </div>
</body>
</html>
