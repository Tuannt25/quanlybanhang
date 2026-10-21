-- ==============================
-- ==============================
DROP DATABASE IF EXISTS qlbanhang;
CREATE DATABASE qlbanhang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qlbanhang;

-- ==============================
-- 1) DANH MỤC
-- ==============================
CREATE TABLE danhmuc (
  madm VARCHAR(20) PRIMARY KEY,
  tendm VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- ==============================
-- 2) SẢN PHẨM
-- ==============================
CREATE TABLE sanpham (
  masp VARCHAR(20) PRIMARY KEY,
  tensp VARCHAR(255) NOT NULL,
  gia DECIMAL(12,2) NOT NULL DEFAULT 0,
  soluong INT NOT NULL DEFAULT 0,
  hinhanh VARCHAR(255) NULL,
  xuatxu VARCHAR(100) NULL,
  madm VARCHAR(20) NOT NULL,
  CONSTRAINT fk_sp_dm FOREIGN KEY (madm) REFERENCES danhmuc(madm)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==============================
-- 3) KHÁCH HÀNG
-- ==============================
CREATE TABLE khachhang (
  makh VARCHAR(20) PRIMARY KEY,
  tenkh VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  sdt VARCHAR(20) NULL,
  diachi VARCHAR(255) NULL
) ENGINE=InnoDB;

-- ==============================
-- 4) NHÂN VIÊN
-- ==============================
CREATE TABLE nhanvien (
  manv INT AUTO_INCREMENT PRIMARY KEY,
  hoten VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  sdt VARCHAR(20) NULL,
  diachi VARCHAR(255) NULL,
  chucvu VARCHAR(255) NULL
) ENGINE=InnoDB;

-- ==============================
-- 5) TÀI KHOẢN
-- ==============================
CREATE TABLE taikhoan (
  matk INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  role ENUM('ADMIN','NHANVIEN','KHACHHANG') NOT NULL DEFAULT 'NHANVIEN',
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  manv INT NULL,
  makh VARCHAR(20) NULL,

  CONSTRAINT fk_tk_nv FOREIGN KEY (manv) REFERENCES nhanvien(manv)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_tk_kh FOREIGN KEY (makh) REFERENCES khachhang(makh)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==============================
-- 6) ĐƠN HÀNG
-- ✅ da_tru_kho: 0 chưa trừ kho | 1 đã trừ kho
-- ==============================
CREATE TABLE donhang (
  madh VARCHAR(20) PRIMARY KEY,
  makh VARCHAR(20) NOT NULL,
  ngaydat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tongtien DECIMAL(14,2) NOT NULL DEFAULT 0,
  trangthai VARCHAR(50) NOT NULL DEFAULT 'Chờ xử lý',
  da_tru_kho TINYINT(1) NOT NULL DEFAULT 0,

  CONSTRAINT fk_dh_kh FOREIGN KEY (makh) REFERENCES khachhang(makh)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==============================
-- 7) CHI TIẾT ĐƠN HÀNG
-- ==============================
CREATE TABLE chitietdonhang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  madh VARCHAR(20) NOT NULL,
  masp VARCHAR(20) NOT NULL,
  soluong INT NOT NULL DEFAULT 1,
  dongia DECIMAL(12,2) NOT NULL DEFAULT 0,

  CONSTRAINT fk_ctdh_dh FOREIGN KEY (madh) REFERENCES donhang(madh)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ctdh_sp FOREIGN KEY (masp) REFERENCES sanpham(masp)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==============================
-- INDEX (tối ưu tìm kiếm)
-- ==============================
CREATE INDEX idx_sp_tensp ON sanpham(tensp);
CREATE INDEX idx_dh_ngay ON donhang(ngaydat);
CREATE INDEX idx_ctdh_madh ON chitietdonhang(madh);
-- ==============================
-- SAMPLE DATA
-- ==============================

INSERT INTO danhmuc(madm, tendm) VALUES
('DM01','Điện thoại'),
('DM02','Laptop'),
('DM03','Thiết bị gia dụng'),
('DM04','Phụ kiện điện tử');


INSERT INTO sanpham(masp, tensp, gia, soluong, hinhanh, madm) VALUES
('SP01','iPhone 15 Pro Max',32990000,15,'iphone15.png','DM01'),
('SP02','Samsung Galaxy S24 Ultra',28990000,20,'s24ultra.png','DM01'),

('SP03','Laptop Dell Inspiron 15',17990000,10,'dell15.png','DM02'),
('SP04','MacBook Air M2',26990000,8,'macbookairm2.png','DM02'),

('SP05','Máy giặt LG Inverter 9kg',8490000,6,'maygiatlg.png','DM03'),
('SP06','Tivi Samsung 55 inch 4K',12990000,5,'tivisamsung55.png','DM03'),

('SP07','Tai nghe Bluetooth AirPods Pro',5490000,25,'airpodspro.png','DM04'),
('SP08','Chuột không dây Logitech MX Master 3',2490000,18,'logitechmx3.png','DM04');


INSERT INTO khachhang(makh,tenkh,email,sdt,diachi) VALUES
('KH001','Nguyễn Văn A','a@gmail.com','0900000001','Hà Nội'),
('KH002','Trần Thị B','b@gmail.com','0900000002','Hà Nam');

INSERT INTO nhanvien(hoten,email,sdt,diachi,chucvu) VALUES
('Admin System','admin@local','0900000000','VN','Quản trị'),
('Nhân viên 01','nv01@local','0900000009','VN','Nhân viên');

-- ADMIN account: username=admin, password=admin123 (hash sẵn)
INSERT INTO taikhoan(username,password,email,role,is_locked,manv,makh) VALUES
('admin','$2y$10$mtbYnvIh9eH0upvPNBK.M.xTam7xEgUxx5pXcgXERVFqhOe00qhrW','admin@local','ADMIN',0,1,NULL);

-- NHANVIEN: username=nhanvien1, password=123456 (hash bcrypt ví dụ)
INSERT INTO taikhoan(username,password,email,role,is_locked,manv,makh) VALUES
('nhanvien1','$2y$10$7HU5LrByTx78xkVyy0qvXuljsLo4zmNwRJxnVme1VBnghujkwO9mW','nv01@local','NHANVIEN',0,2,NULL);

-- KHACHHANG: username=khach1, password=123456 (hash bcrypt ví dụ)
INSERT INTO taikhoan(username,password,email,role,is_locked,manv,makh) VALUES
('khach1','$2y$10$7HU5LrByTx78xkVyy0qvXuljsLo4zmNwRJxnVme1VBnghujkwO9mW','khach1@local','KHACHHANG',0,NULL,'KH001');
-- ==============================
-- DEMO ORDER
-- ==============================
INSERT INTO donhang(madh,makh,ngaydat,tongtien,trangthai,da_tru_kho) VALUES
('DH0001','KH001',NOW(),0,'Chờ xử lý',0);

INSERT INTO chitietdonhang(madh,masp,soluong,dongia) VALUES
('DH0001','SP01',2,25000),
('DH0001','SP03',1,35000);

-- tính tổng tiền đơn
UPDATE donhang
SET tongtien = (
  SELECT SUM(soluong*dongia)
  FROM chitietdonhang
  WHERE madh='DH0001'
)
WHERE madh='DH0001';
