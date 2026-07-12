<?php

declare(strict_types=1);

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'hospis';

$email = 'Admin@gmail.com';
$plainPassword = 'Admin@1234';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$profile = [
    'name_user' => 'Admin',
    'surname_user' => 'Principal',
    'quarter' => 'HQ',
    'telephone' => '600000000',
    'staff_code' => 'ADM001',
];

$mysqli = new mysqli($host, $user, $pass, $dbName);
if ($mysqli->connect_error) {
    fwrite(STDERR, 'Database connection failed: ' . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$mysqli->set_charset('utf8mb4');

// 1) Ensure user exists by email, and always refresh password to requested one.
$escapedEmail = $mysqli->real_escape_string($email);
$result = $mysqli->query("SELECT id_user FROM users WHERE email = '{$escapedEmail}' LIMIT 1");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUser = (int) $row['id_user'];

    $escapedHash = $mysqli->real_escape_string($hashedPassword);
    $mysqli->query("UPDATE users SET password = '{$escapedHash}', updated_at = NOW() WHERE id_user = {$idUser}");
} else {
    $escapedName = $mysqli->real_escape_string($profile['name_user']);
    $escapedSurname = $mysqli->real_escape_string($profile['surname_user']);
    $escapedQuarter = $mysqli->real_escape_string($profile['quarter']);
    $escapedTelephone = $mysqli->real_escape_string($profile['telephone']);
    $escapedHash = $mysqli->real_escape_string($hashedPassword);

    $insertUserSql = "INSERT INTO users (name_user, surname_user, quarter, email, password, telephone, created_at, updated_at, deleted_at) "
        . "VALUES ('{$escapedName}', '{$escapedSurname}', '{$escapedQuarter}', '{$escapedEmail}', '{$escapedHash}', '{$escapedTelephone}', NOW(), NOW(), NULL)";

    if (! $mysqli->query($insertUserSql)) {
        fwrite(STDERR, 'User insert failed: ' . $mysqli->error . PHP_EOL);
        exit(1);
    }

    $idUser = (int) $mysqli->insert_id;
}

// 2) Use existing "Major" function as admin-like role.
$functionResult = $mysqli->query("SELECT id_function FROM `function` WHERE name = 'Major' LIMIT 1");
if (! $functionResult || $functionResult->num_rows === 0) {
    fwrite(STDERR, 'Role "Major" not found in `function` table.' . PHP_EOL);
    exit(1);
}

$functionRow = $functionResult->fetch_assoc();
$idFunction = (int) $functionRow['id_function'];

// 3) Ensure personel record exists for this user.
$personelResult = $mysqli->query("SELECT id_personel FROM personel WHERE id_user = {$idUser} LIMIT 1");
if ($personelResult && $personelResult->num_rows === 0) {
    $escapedStaffCode = $mysqli->real_escape_string($profile['staff_code']);
    $insertPersonelSql = "INSERT INTO personel (staff_code, id_user, id_function, created_at, updated_at, deleted_at) "
        . "VALUES ('{$escapedStaffCode}', {$idUser}, {$idFunction}, NOW(), NOW(), NULL)";

    if (! $mysqli->query($insertPersonelSql)) {
        fwrite(STDERR, 'Personel insert failed: ' . $mysqli->error . PHP_EOL);
        exit(1);
    }

    $idPersonel = (int) $mysqli->insert_id;
} else {
    $personelRow = $personelResult ? $personelResult->fetch_assoc() : ['id_personel' => 0];
    $idPersonel = (int) $personelRow['id_personel'];

    $escapedStaffCode = $mysqli->real_escape_string($profile['staff_code']);
    $mysqli->query("UPDATE personel SET staff_code = '{$escapedStaffCode}', id_function = {$idFunction}, updated_at = NOW() WHERE id_personel = {$idPersonel}");
}

echo 'Admin account ready.' . PHP_EOL;
echo 'Email: ' . $email . PHP_EOL;
echo 'Password: ' . $plainPassword . PHP_EOL;
echo 'Staff code: ' . $profile['staff_code'] . PHP_EOL;
echo 'Role: Major' . PHP_EOL;
