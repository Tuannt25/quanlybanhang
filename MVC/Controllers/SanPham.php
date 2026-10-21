<?php
class SanPham extends Controller
{
//hàm hiển thị danh sách
  public function Get_data()
  {
    $this->requireStaff();
    $m = $this->model("SanPhamModel");
    $rows = $m->all();
    $dms = $m->listDanhMuc();

    $edit = null;
    //dùng hàm find lấy dữ liệu đổ vào edit để khi sửa tự động điền luôn
    if (isset($_GET['edit'])) {
      $rs = $m->find($_GET['edit']);
      if ($rs && mysqli_num_rows($rs) > 0)
        $edit = mysqli_fetch_assoc($rs);
    }

    $this->view("Master", ["page" => "sanpham", "rows" => $rows, "dms" => $dms, "edit" => $edit]);
  }
  //do ko sửa mã sp nên là dùng cái này để mang theo khi ấn sửa
  public function Edit($masp = "")
  {
    $this->requireStaff();
    $this->redirect("SanPham/Get_data?edit=" . $masp);
  }

  private function handleUploadImage()
  {
    if (!isset($_FILES['hinhanh']) || ($_FILES['hinhanh']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
      return null;
    if ($_FILES['hinhanh']['error'] !== UPLOAD_ERR_OK || $_FILES['hinhanh']['size'] > 5 * 1024 * 1024)
      return "";

    $dir = __DIR__ . "/../../Public/Picture/";
    if (!is_dir($dir) && !mkdir($dir, 0755, true))
      return "";

    $imageInfo = @getimagesize($_FILES['hinhanh']['tmp_name']);
    $extensions = [
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
      'image/webp' => 'webp'
    ];
    $mime = $imageInfo['mime'] ?? '';
    if (!isset($extensions[$mime]))
      return "";

    $newName = "sp_" . bin2hex(random_bytes(8)) . "." . $extensions[$mime];
    $path = $dir . $newName;
  //di chuyển ảnh từ bộ nhớ tạm và muc chính thức dự án
    if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $path)) {
      return $newName; // lưu vào DB
    }
    return "";
  }

  public function Save()
  {
    $this->requireStaff();
    $m = $this->model("SanPhamModel"); // Biến model là $m

    $masp = $_POST['masp'] ?? '';       // Mã để update (hidden)
    $new_masp = $_POST['new_masp'] ?? ''; // Mã người dùng nhập (khi thêm mới)
    $tensp = $_POST['tensp'] ?? '';
    $gia = $_POST['gia'] ?? 0;
    $soluong = $_POST['soluong'] ?? 0;
    $madm = $_POST['madm'] ?? '';
    $xuatxu = trim($_POST['xuatxu'] ?? '');

    $targetMasp = $masp === '' ? trim($new_masp) : trim($masp);
    if ($targetMasp === '' || trim($tensp) === '' || trim($madm) === '' || (float)$gia < 0 || (int)$soluong < 0) {
      $this->view("Master", [
        "page" => "sanpham",
        "rows" => $m->all(),
        "dms" => $m->listDanhMuc(),
        "msg" => "Vui lòng nhập đầy đủ thông tin; giá và số lượng không được âm",
        "msgType" => "error"
      ]);
      return;
    }

    if ($masp === '') {
      $check = $m->find($new_masp);
      if ($check && mysqli_num_rows($check) > 0) {
        $this->view("Master", [
          "page" => "sanpham",
          "rows" => $m->all(),
          "dms" => $m->listDanhMuc(),
          "msg" => "Mã sản phẩm $new_masp đã tồn tại",
          "msgType" => "error"
        ]);
        return;
      }
    }

    // 1. Xử lý ảnh
    $upload = $this->handleUploadImage();
    if ($upload === "") {
      $this->view("Master", [
        "page" => "sanpham",
        "rows" => $m->all(),
        "dms" => $m->listDanhMuc(),
        "msg" => "Ảnh không hợp lệ (chỉ jpg/png/webp)",
        "msgType" => "error"
      ]);
      return;
    }

    // 2. Logic chính
    if ($masp === '') {
      // --- TRƯỜNG HỢP THÊM MỚI ---

    //insert nếu mã sản phẩm chưa có
      $ok = $m->insert($new_masp, $tensp, $gia, $soluong, $upload, $madm,$xuatxu);
      $msg = $ok ? "Thêm sản phẩm thành công" : "Thêm thất bại";
    } else {
      //nếu có r  là udate
      $ok = $m->update($masp, $tensp, $gia, $soluong, $upload, $madm,$xuatxu);
      $msg = $ok ? "Cập nhật thành công" : "Cập nhật thất bại";
    }

    // 3. Trả về view
    $this->view("Master", [
      "page" => "sanpham",
      "rows" => $m->all(),
      "dms" => $m->listDanhMuc(),
      "msg" => $msg,
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  public function Delete($masp = "")
  {
    $this->requireStaff();
    $m = $this->model("SanPhamModel");
    $ok = $m->delete($masp);

    $this->view("Master", [
      "page" => "sanpham",
      "rows" => $m->all(),
      "dms" => $m->listDanhMuc(),
      "msg" => $ok ? "Xóa thành công" : "Xóa thất bại (SP có thể đã nằm trong đơn hàng)",
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  public function Search()
  {
    $this->requireStaff();
    $m = $this->model("SanPhamModel");
    $k = $_POST['keyword'] ?? '';
    $madm = $_POST['madm'] ?? null;
    $this->view("Master", [
      "page" => "sanpham",
      "rows" => $m->search($k, $madm),
      "dms" => $m->listDanhMuc()
    ]);
  }

  public function Export()
  {
    $this->requireStaff();
    $m = $this->model("SanPhamModel");
    ExcelHelper::export("sanpham", ["Mã SP", "Tên SP", "Giá", "Số lượng", "Ảnh", "Mã DM", "Xuất xứ"], $m->toArrayRows());
  }

  public function Import()
  {
    $this->requireStaff();
    if (empty($_FILES['file']['tmp_name'])) 
      $this->redirect("SanPham/Get_data");
    $m = $this->model("SanPhamModel");
    $data = ExcelHelper::import($_FILES['file']['tmp_name']);

    for ($i = 0; $i < count($data); $i++) {
      $row = $data[$i];
      if (count($row) < 6)
        continue;
      [$masp, $tensp, $gia, $soluong, $hinhanh, $madm, $xuatxu] = array_pad($row, 7, '');
      if (!$masp)
        continue;

      $rs = $m->find($masp);
      if ($rs && mysqli_num_rows($rs) > 0) {
        $m->update($masp, $tensp, $gia, $soluong, $hinhanh, $madm, $xuatxu);
      } else {
        $m->insert($masp, $tensp, $gia, $soluong, $hinhanh, $madm, $xuatxu);
      }
    }
    $this->redirect("SanPham/Get_data");
  }
}
?>
