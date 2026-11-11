CREATE DATABASE IF NOT EXISTS QLHocPhan;
USE QLHocPhan;

CREATE TABLE IF NOT EXISTS GiangVien (
    IDGV VARCHAR(20) PRIMARY KEY,
    Fullname VARCHAR(255) NOT NULL,
    Image VARCHAR(255) NOT NULL DEFAULT '',
    SumClass INT NOT NULL DEFAULT 0
);

INSERT INTO GiangVien (IDGV, Fullname, Image, SumClass)
VALUES
('GV001', 'Philippe Troussier', 'assets/avatars/gv001.jpg', 4),
('GV002', 'Park Hang-seo', 'assets/avatars/gv002.jpg', 5),
('GV003', 'Kim Sang-sik', 'assets/avatars/gv003.jpg', 3),
('GV004', 'Henrique Calisto', 'assets/avatars/gv004.jpg', 6)
ON DUPLICATE KEY UPDATE
    Fullname = VALUES(Fullname),
    Image = VALUES(Image),
    SumClass = VALUES(SumClass);
