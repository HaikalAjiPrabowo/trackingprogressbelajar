<?php
session_start();
session_unset(); 
session_destroy();

echo "
<script>
    alert('Anda berhasil logout!');
    window.location='../form/login.php';
</script>";
