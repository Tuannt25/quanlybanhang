<?php
class TaiKhoan extends Controller
{

  public function Get_data()
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $rows = $m->all();

    $edit = null;
    if (isset($_GET['edit'])) {
      $rs = $m->find($_GET['edit']);
      if ($rs && mysqli_num_rows($rs) > 0)
        $edit = mysqli_fetch_assoc($rs);
    }

    $this->view("Master", [
      "page" => "taikhoan",
      "rows" => $rows,
      "edit" => $edit,
      "nvs" => $m->listNhanVien(),
      "khs" => $m->listKhachHang()
    ]);
  }

  public function Edit($matk = "")
  {
    $this->requireAdmin();
    $this->redirect("TaiKhoan/Get_data?edit=" . $matk);
  }

  public function Save()
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");

    $matk = $_POST['matk'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'NHANVIEN';

    // ĐÃ XÓA PHẦN KIỂM TRA INSERT (THÊM MỚI)
    // Chỉ thực hiện Update nếu có mã tài khoản
    if ($matk !== '') {
      $ok = $m->updateBasic($matk, $email, $role);
      $msg = $ok ? "Cập nhật thành công" : "Cập nhật thất bại";
    } else {
      $ok = false;
      $msg = "Dữ liệu không hợp lệ";
    }

    $this->view("Master", [
      "page" => "taikhoan",
      "rows" => $m->all(),
      "nvs" => $m->listNhanVien(),
      "khs" => $m->listKhachHang(),
      "msg" => $msg,
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  public function ResetPass($matk = "")
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $new = $_POST['newpass'] ?? '123456';
    $ok = $m->resetPassword($matk, $new);

    $this->view("Master", [
      "page" => "taikhoan",
      "rows" => $m->all(),
      "nvs" => $m->listNhanVien(),
      "khs" => $m->listKhachHang(),
      "msg" => $ok ? "Reset mật khẩu thành công" : "Reset thất bại",
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  public function Lock($matk = "")
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $m->setLock($matk, 1);
    $this->redirect("TaiKhoan/Get_data");
  }

  public function Unlock($matk = "")
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $m->setLock($matk, 0);
    $this->redirect("TaiKhoan/Get_data");
  }

  public function Delete($matk = "")
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $ok = $m->delete($matk);

    $this->view("Master", [
      "page" => "taikhoan",
      "rows" => $m->all(),
      "nvs" => $m->listNhanVien(),
      "khs" => $m->listKhachHang(),
      "msg" => $ok ? "Xóa thành công" : "Xóa thất bại",
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  public function Search()
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    $k = $_POST['keyword'] ?? '';
    $this->view("Master", [
      "page" => "taikhoan",
      "rows" => $m->search($k),
      "nvs" => $m->listNhanVien(),
      "khs" => $m->listKhachHang()
    ]);
  }

  public function Export()
  {
    $this->requireAdmin();
    $m = $this->model("TaiKhoanModel");
    ExcelHelper::export("taikhoan", ["Mã TK", "Username", "Email", "Role", "Locked", "manv", "makh"], $m->toArrayRows());
  }

  // ĐÃ XÓA HÀM IMPORT (VÌ IMPORT LÀ MỘT HÀNH ĐỘNG THÊM MỚI TÀI KHOẢN)
}
?>
