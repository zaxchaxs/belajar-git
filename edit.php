<?php
include('db.php');

// Mendapatkan ID pengguna yang akan diedit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id=$id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}

// Menangani proses update data
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Cek apakah foto baru diunggah
    if ($_FILES['foto']['name'] != "") {
        $foto = $_FILES['foto']['name'];
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_path = "uploads/" . $foto;

        // Pindahkan foto baru ke folder uploads
        move_uploaded_file($foto_temp, $foto_path);

        // Hapus foto lama
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
        // Jika tidak ada foto baru, hanya update name dan email
        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
</head>

<body>

    <h2>Edit Pengguna</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>
        <label for="foto">Foto Baru:</label><br>
        <input type="file" id="foto" name="foto" accept="image/*"><br><br>
        <img src="uploads/<?php echo $user['foto']; ?>" width="100"><br><br>
        <input type="submit" name="update" value="Update">
    </form>
</body>
</html>

<?php
$conn->close();
?>