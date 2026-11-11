<?php
session_start();
require __DIR__ . '/db.php';

$result = $conn->query("SELECT * FROM GiangVien ORDER BY IDGV ASC");
$lecturers = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý giảng viên</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>Danh sách giảng viên</h1>
            <a href="add.php" class="btn btn-primary">+ Thêm giảng viên</a>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            echo formatMessage($_SESSION['message_type'] ?? 'success', $_SESSION['message']);
            unset($_SESSION['message'], $_SESSION['message_type']);
        }
        ?>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>IDGV</th>
                            <th>Hình ảnh</th>
                            <th>Họ và tên</th>
                            <th>Tổng số lớp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lecturers): ?>
                            <?php foreach ($lecturers as $teacher): ?>
                                <tr>
                                    <td><?= htmlspecialchars($teacher['IDGV'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php
                                        $imagePath = trim((string) $teacher['Image']);
                                        $imageIsValid = $imagePath !== '' && (
                                            str_starts_with($imagePath, 'http://') ||
                                            str_starts_with($imagePath, 'https://') ||
                                            file_exists(__DIR__ . '/' . $imagePath)
                                        );
                                        $imageSrc = $imageIsValid ? $imagePath : 'assets/avatars/default-teacher.svg';
                                        ?>
                                        <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($teacher['Fullname'], ENT_QUOTES, 'UTF-8') ?>" class="teacher-avatar">
                                    </td>
                                    <td><?= htmlspecialchars($teacher['Fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int) $teacher['SumClass'] ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= urlencode($teacher['IDGV']) ?>" class="btn btn-secondary">Sửa</a>
                                            <form action="delete.php" method="post" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này?');">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($teacher['IDGV'], ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 24px; color: #64748b;">
                                    Chưa có dữ liệu giảng viên.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
