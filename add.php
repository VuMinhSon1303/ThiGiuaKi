<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idgv = trim($_POST['idgv'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $sumClass = (int) ($_POST['sumclass'] ?? 0);

    if ($idgv === '' || $fullname === '') {
        $_SESSION['message'] = 'Vui lòng nhập đầy đủ mã giảng viên và họ tên.';
        $_SESSION['message_type'] = 'error';
        redirect('add.php');
    }

    $imagePath = trim((string) ($_POST['image_url'] ?? ''));
    $uploadedImage = saveUploadedImage($_FILES['image_upload'] ?? []);
    if ($uploadedImage !== '') {
        $imagePath = $uploadedImage;
    }

    if ($imagePath === '') {
        $imagePath = 'assets/avatars/default-teacher.svg';
    }

    $stmt = $conn->prepare('INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('sssi', $idgv, $fullname, $imagePath, $sumClass);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        $_SESSION['message'] = 'Thêm giảng viên thành công.';
        $_SESSION['message_type'] = 'success';
        redirect('index.php');
    }

    $_SESSION['message'] = 'Không thể thêm giảng viên. Vui lòng kiểm tra lại dữ liệu.';
    $_SESSION['message_type'] = 'error';
    redirect('add.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm giảng viên</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>Thêm giảng viên mới</h1>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <?= formatMessage($_SESSION['message_type'] ?? 'success', $_SESSION['message']) ?>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card">
            <form action="add.php" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="idgv">Mã giảng viên (IDGV)</label>
                        <input type="text" id="idgv" name="idgv" placeholder="Ví dụ: GV005" required>
                    </div>

                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" placeholder="Nhập tên giảng viên" required>
                    </div>

                    <div class="form-group">
                        <label for="sumclass">Tổng số lớp</label>
                        <input type="number" id="sumclass" name="sumclass" min="0" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Link hình ảnh (nếu có)</label>
                        <input type="text" id="image_url" name="image_url" placeholder="https://... hoặc /images/...">
                    </div>

                    <div class="form-group full">
                        <label for="image_upload">Tải ảnh lên</label>
                        <input type="file" id="image_upload" name="image_upload" accept="image/*">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
