<?php
// proses_kontak.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama   = htmlspecialchars($_POST['nama']);
    $email  = htmlspecialchars($_POST['email']);
    $pesan  = htmlspecialchars($_POST['pesan']);

    $text = "Nama: $nama\nEmail: $email\nPesan: $pesan";

    header('Location: https://wa.me/6285155288330?text='.$text.'');

} else {
    header('Location: kontak.php');
}