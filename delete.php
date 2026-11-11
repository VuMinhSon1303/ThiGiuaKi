<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = trim((string) ($_POST['id'] ?? ''));
if ($id === '') {
    $_SESSION['message'] = 'Không có dữ liệu để xóa.';
    $_SESSION['message_type'] = 'error';
    redirect('index.php');
}

$stmt = $conn->prepare('DELETE FROM GiangVien WHERE IDGV = ?');
$stmt->bind_param('s', $id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    $_SESSION['message'] = 'Xóa giảng viên thành công.';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Không thể xóa giảng viên.';
    $_SESSION['message_type'] = 'error';
}

redirect('index.php');
