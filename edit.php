<?php
session_start();
require __DIR__ . '/db.php';

$id = $_GET['id'] ?? '';
if ($id === '') {
    redirect('index.php');
}

$current = $conn->prepare('SELECT * FROM GiangVien WHERE IDGV = ?');
$current->bind_param('s', $id);
$current->execute();
$result = $current->get_result();
$teacher = $result->fetch_assoc();
$current->close();

if (!$teacher) {
    $_SESSION['message'] = 'Không tìm thấy giảng viên cần sửa.';
    $_SESSION['message_type'] = 'error';
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idgv = trim($_POST['idgv'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $sumClass = (int) ($_POST['sumclass'] ?? 0);

    if ($idgv === '' || $fullname === '') {
        $_SESSION['message'] = 'Vui lòng nhập đầy đủ mã giảng viên và họ tên.';
        $_SESSION['message_type'] = 'error';
        redirect('edit.php?id=' . urlencode($id));
    }

    $imagePath = trim((string) ($_POST['image_url'] ?? ''));
    $uploadedImage = saveUploadedImage($_FILES['image_upload'] ?? []);
    if ($uploadedImage !== '') {
        $imagePath = $uploadedImage;
    }

    if ($imagePath === '') {
        $imagePath = $teacher['Image'] ?: 'assets/avatars/default-teacher.svg';
    }

    $stmt = $conn->prepare('UPDATE GiangVien SET IDGV = ?, Fullname = ?, Image = ?, SumClass = ? WHERE IDGV = ?');
    $stmt->bind_param('sssis', $idgv, $fullname, $imagePath, $sumClass, $id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        $_SESSION['message'] = 'Cập nhật thông tin giảng viên thành công.';
        $_SESSION['message_type'] = 'success';
        redirect('index.php');
    }

    $_SESSION['message'] = 'Không thể cập nhật giảng viên.';
    $_SESSION['message_type'] = 'error';
    redirect('edit.php?id=' . urlencode($id));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa giảng viên</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>Sửa thông tin giảng viên</h1>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <?= formatMessage($_SESSION['message_type'] ?? 'success', $_SESSION['message']) ?>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card">
            <form action="edit.php?id=<?= urlencode($teacher['IDGV']) ?>" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="idgv">Mã giảng viên (IDGV)</label>
                        <input type="text" id="idgv" name="idgv" value="<?= htmlspecialchars($teacher['IDGV'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($teacher['Fullname'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sumclass">Tổng số lớp</label>
                        <input type="number" id="sumclass" name="sumclass" min="0" value="<?= (int) $teacher['SumClass'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Link hình ảnh (nếu có)</label>
                        <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($teacher['Image'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="form-group full">
                        <label for="image_upload">Tải ảnh mới lên</label>
                        <input type="file" id="image_upload" name="image_upload" accept="image/*">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
