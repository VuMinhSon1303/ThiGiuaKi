<?php
function getDatabaseConnection()
{
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $database = 'QLHocPhan';

    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        die('Không thể kết nối MySQL: ' . $conn->connect_error);
    }

    $conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
    $conn->select_db($database);

    $conn->query("
        CREATE TABLE IF NOT EXISTS GiangVien (
            IDGV VARCHAR(20) PRIMARY KEY,
            Fullname VARCHAR(255) NOT NULL,
            Image VARCHAR(255) NOT NULL DEFAULT '',
            SumClass INT NOT NULL DEFAULT 0
        )
    ");

    $legacyMapping = [
        'GV001' => 'assets/avatars/gv001.jpg',
        'GV002' => 'assets/avatars/gv002.jpg',
        'GV003' => 'assets/avatars/gv003.jpg',
        'GV004' => 'assets/avatars/gv004.jpg',
    ];

    $existingRows = $conn->query('SELECT IDGV, Image FROM GiangVien');
    if ($existingRows) {
        while ($row = $existingRows->fetch_assoc()) {
            $currentImage = trim((string) $row['Image']);
            $idgv = $row['IDGV'];
            $fallback = $legacyMapping[$idgv] ?? null;

            if ($fallback !== null && ($currentImage === '' || !file_exists(__DIR__ . '/' . $currentImage))) {
                $conn->query("UPDATE GiangVien SET Image = '" . $fallback . "' WHERE IDGV = '" . $idgv . "'");
            }
        }
    }

    $count = $conn->query("SELECT COUNT(*) AS total FROM GiangVien");
    $countData = $count ? $count->fetch_assoc() : ['total' => 0];

    if ((int) $countData['total'] === 0) {
        $sampleData = [
            ['GV001', 'Philippe Troussier', 'assets/avatars/gv001.jpg', 4],
            ['GV002', 'Park Hang-seo', 'assets/avatars/gv002.jpg', 5],
            ['GV003', 'Kim Sang-sik', 'assets/avatars/gv003.jpg', 3],
            ['GV004', 'Henrique Calisto', 'assets/avatars/gv004.jpg', 6],
        ];

        $insertSql = "INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);

        foreach ($sampleData as $row) {
            $stmt->bind_param('sssi', $row[0], $row[1], $row[2], $row[3]);
            $stmt->execute();
        }

        $stmt->close();
    }

    return $conn;
}

$conn = getDatabaseConnection();

function formatMessage(string $type, string $message): string
{
    if ($type === 'success') {
        return '<div class="alert success">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    return '<div class="alert error">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function saveUploadedImage(array $file): string
{
    if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedTypes, true)) {
        return '';
    }

    $fileName = uniqid('gv_', true) . '.' . $extension;
    $targetPath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return '';
    }

    return 'uploads/' . $fileName;
}
