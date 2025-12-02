<?php

    // ==========================
    // CEK PASSWORD KONFIRMASI
    // ==========================
    if ($password !== $konfirmasi_pw) {
        echo "<script>
                alert('Password dan konfirmasi password tidak sama!');
                window.history.back();
              </script>";
        exit;
    }

    // ==========================
    // KONEKSI DATABASE (PDO)
    // ==========================
    $db = DB::conn();

    // ==========================
    // CEK EMAIL SUDAH DIPAKAI ?
    // ==========================
    $check = $db->prepare("SELECT id FROM user WHERE Email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        echo "<script>
                alert('Email sudah terdaftar!');
                window.location.href = '../form/register.html';
              </script>";
        exit;
    }

    // INSERT KE DATABASE
    
    $sql = "INSERT INTO user (Nama, Tanggal_lahir, Prodi, Email, Password)
            VALUES (:nama, :tl, :prodi, :email, :password)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":nama", $nama);
    $stmt->bindParam(":tl", $tl);
    $stmt->bindParam(":prodi", $prodi);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Register berhasil! Silakan login.');
                window.location.href = '../form/login.html';
              </script>";
    } else {
        echo "<script>
                alert('Gagal Register! Error database.');
                window.history.back();
              </script>";
    }
}
?>
