<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_pkk_bpurba";

$backup_file = 'backup_pkk_' . date('Y-m-d_H-i-s') . '.sql';

exec("mysqldump --user=$user --password=$pass --host=$host $db > $backup_file");

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
header('Content-Length: ' . filesize($backup_file));
readfile($backup_file);

unlink($backup_file);
exit;
