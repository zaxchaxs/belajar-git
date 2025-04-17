<?php
include('db.php');

// Insert Data
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $foto = $_FILES['foto']['name'];
    $foto_temp = $_FILES['foto']['tmp_name'];
    $foto_path = "uploads/" . $foto;

    // Pindahkan file foto ke folder uploads
    if (move_uploaded_file($foto_temp, $foto_path)) {
        $sql = "INSERT INTO users (name, email, foto) VALUES ('$name', '$email', '$foto')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}

// Delete Data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Ambil nama file foto untuk dihapus dari folder uploads
    $sql = "SELECT foto FROM users WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $foto_path = "uploads/" . $row['foto'];

    // Hapus foto jika ada
    if (file_exists($foto_path)) {
        unlink($foto_path);
    }

    // Hapus data dari database
    $sql = "DELETE FROM users WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Update Data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Cek apakah foto baru diunggah
    if ($_FILES['foto']['name'] != "") {
        $foto = $_FILES['foto']['name'];
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_path = "uploads/" . $foto;

        // Pindahkan foto baru ke folder uploads
        move_uploaded_file($foto_temp, $foto_path);

        // Hapus foto lama dari folder uploads
        $sql = "SELECT foto FROM users WHERE id=$id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $old_foto_path = "uploads/" . $row['foto'];
        if (file_exists($old_foto_path)) {
            unlink($old_foto_path);
        }

        // Update data dengan foto baru
        $sql = "UPDATE users SET name='$name', email='$email', foto='$foto' WHERE id=$id";
    } else {
        // Jika foto tidak diubah, hanya update name dan email
        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Mendapatkan data pengguna untuk ditampilkan
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD PHP dengan Foto</title>
</head>

<body>

    <h2>Daftar Pengguna</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Foto</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><img src="uploads/<?php echo $row['foto']; ?>" width="100"></td>
                    <td>
                        <a href="index.php?delete=<?php echo $row['id']; ?>">Delete</a>
                        <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Tambah Pengguna</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <label for="foto">Foto:</label><br>
        <input type="file" id="foto" name="foto" accept="image/*" required><br><br>
        <input type="submit" name="insert" value="Tambah">
    </form>

</body>

</html>

<?php
$conn->close();
?>