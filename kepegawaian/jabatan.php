<?php
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    $nama_jabatan = isset($_POST['nama_jabatan']) ? $_POST['nama_jabatan'] : '';
    $deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';

    if ($action == 'add') {
        $sql = "INSERT INTO jabatan (nama_jabatan, deskripsi) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $nama_jabatan, $deskripsi);
            if ($stmt->execute()) {
                $message = "Jabatan berhasil ditambahkan.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    } elseif ($action == 'update') {
        $id_jabatan = $_POST['id_jabatan'];
        $sql = "UPDATE jabatan SET nama_jabatan = ?, deskripsi = ? WHERE id_jabatan = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssi", $nama_jabatan, $deskripsi, $id_jabatan);
            if ($stmt->execute()) {
                $message = "Jabatan berhasil diupdate.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Query database gagal.";
        }
    }
}

if (isset($_GET['delete'])) {
    $id_jabatan = $_GET['delete'];
    $sql = "DELETE FROM jabatan WHERE id_jabatan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_jabatan);
        if ($stmt->execute()) {
            $message = "Jabatan berhasil dihapus.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Query database gagal.";
    }
}

$jabatan = null;
if (isset($_GET['edit'])) {
    $id_jabatan = $_GET['edit'];
    $sql = "SELECT * FROM jabatan WHERE id_jabatan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_jabatan);
        $stmt->execute();
        $result = $stmt->get_result();
        $jabatan = $result->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jabatan</title>
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
        <h2 align="center">Manajemen Jabatan</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="<?php echo $jabatan ? 'update' : 'add'; ?>">
            <?php if ($jabatan): ?>
                <input type="hidden" name="id_jabatan" value="<?php echo $jabatan['id_jabatan']; ?>">
            <?php endif; ?>
            <div class="form-group">
                Nama Jabatan: <input type="text" name="nama_jabatan" value="<?php echo $jabatan['nama_jabatan'] ?? ''; ?>" required><br>
                Deskripsi: <textarea name="deskripsi" required><?php echo $jabatan['deskripsi'] ?? ''; ?></textarea><br>
                <button type="submit"><?php echo $jabatan ? 'Update' : 'Tambah'; ?></button>
            </div>
        </form>

        <h2 align="center">Daftar Jabatan</h2>
        <?php
        $sql = "SELECT * FROM jabatan";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID Jabatan</th><th>Nama Jabatan</th><th>Deskripsi</th><th>Aksi</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>".$row["id_jabatan"]."</td>
                    <td>".$row["nama_jabatan"]."</td>
                    <td>".$row["deskripsi"]."</td>
                    <td>
                        <a href='jabatan.php?edit=".$row["id_jabatan"]."'>Edit</a> | 
                        <a href='jabatan.php?delete=".$row["id_jabatan"]."' onclick='return confirm(\"Yakin ingin menghapus?\")'>Delete</a>
                    </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data jabatan.";
        }
        ?>
    </div>
</body>
</html>
