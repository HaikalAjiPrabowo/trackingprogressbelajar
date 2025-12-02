<?php
require "../api/src/Utils/DB.php";

use Utils\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../api/src/PHPmailer/src/Exception.php';
require '../api/src/PHPmailer/src/PHPMailer.php';
require '../api/src/PHPmailer/src/SMTP.php';

if (isset($_POST['submit'])) {

    $email = trim($_POST['email']);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email salah!'); window.location='../form/lupa_pw.html';</script>";
        exit;
    }

    // Ambil koneksi PDO
    $conn = DB::conn();

    // Cek apakah email ada
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindValue(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "<script>alert('Email tidak ditemukan!'); window.location='../form/lupa_pw.html';</script>";
        exit;
    }

    // Buat kode OTP
    $kode = rand(100000, 999999);

    date_default_timezone_set('Asia/Jakarta');
    $dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $dt->modify('+1 minutes');
    $expired = $dt->format('Y-m-d H:i:s');

    // Update kode dan expired
    $update = $conn->prepare("
        UPDATE user 
        SET reset_code = :kode, code_expired = :exp 
        WHERE email = :email
    ");

    $update->execute([
        ":kode"  => $kode,
        ":exp"   => $expired,
        ":email" => $email
    ]);

    // Kirim email
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Ganti email SMTP kamu
        $mail->Username = 'trackingbelajar@gmail.com';
        $mail->Password = 'skoaosffldhdxind';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('trackingbelajar@gmail.com', 'Reset Password');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Kode Reset Password Anda";
        $mail->Body = "
            <h3>Kode Reset Password</h3>
            <p>Gunakan kode berikut untuk reset password Anda:</p>
            <h1 style='font-size:40px;'>$kode</h1>
            <p>Kode berlaku 1 menit.</p>
        ";

        $mail->send();

        echo "<script>alert('Kode OTP telah dikirim!'); window.location='../form/verifikasi.php?email=$email';</script>";

    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
    }
}
?>
