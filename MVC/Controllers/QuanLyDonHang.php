<?php
class QuanLyDonHang extends Controller {

  // =========================
  // 1) DANH SÁCH ĐƠN HÀNG
  // =========================
  public function Get_data(){
    $this->requireStaff();

    $m = $this->model("DonHangModel");
    $rows = $m->allOrders();

    $msg = null;
    $msgType = null;

    if(($_GET['msg'] ?? '') === 'tao_ok'){
      $msg = "Tạo đơn thành công: " . ($_GET['madh'] ?? '');
      $msgType = "success";
    }

    $this->view("Master", [
      "page" => "quanlydonhang",
      "rows" => $rows,
      "msg" => $msg,
      "msgType" => $msgType
    ]);
  }

  // =========================
  // 2) CẬP NHẬT TRẠNG THÁI
  // (Hoàn thành mới trừ kho)
  // =========================
  public function UpdateStatus(){
    $this->requireStaff();

    $m = $this->model("DonHangModel");

    $madh   = $_POST['madh'] ?? '';
    $status = $_POST['trangthai'] ?? 'Chờ xử lý';

    if($madh === ""){
      $this->view("Master", [
        "page" => "quanlydonhang",
        "rows" => $m->allOrders(),
        "msg" => "Thiếu mã đơn hàng!",
        "msgType" => "error"
      ]);
      return;
    }

    // ✅ CHỈ GỌI 1 HÀM DUY NHẤT:
    // updateTrangThai() sẽ update status + trừ kho đúng lúc (khi Hoàn thành)
    $ok = $m->updateTrangThai($madh, $status);

    $this->view("Master", [
      "page" => "quanlydonhang",
      "rows" => $m->allOrders(),
      "msg" => $ok ? "Cập nhật trạng thái thành công" : "Cập nhật thất bại",
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  // =========================
  // 3) XÓA ĐƠN HÀNG
  // =========================
  public function Delete($madh = ""){
    $this->requireStaff();

    $m = $this->model("DonHangModel");
    $ok = $m->deleteOrder($madh);

    $this->view("Master", [
      "page" => "quanlydonhang",
      "rows" => $m->allOrders(),
      "msg" => $ok ? "Xóa đơn thành công" : "Xóa thất bại",
      "msgType" => $ok ? "success" : "error"
    ]);
  }

  // =========================
  // 4) TÌM KIẾM ĐƠN HÀNG
  // =========================
  public function Search(){
    $this->requireStaff();

    $m = $this->model("DonHangModel");
    $k = $_POST['keyword'] ?? '';

    $this->view("Master", [
      "page" => "quanlydonhang",
      "rows" => $m->searchOrders($k)
    ]);
  }

  // =========================
  // 5) XUẤT CSV
  // =========================
  public function Export(){
    $this->requireStaff();

    $m = $this->model("DonHangModel");
    $rs = $m->allOrders();

    ExcelHelper::export(
      "donhang",
      ["Mã đơn","Mã KH","Tên KH","Ngày đặt","Tổng tiền","Trạng thái"],
      $m->exportOrdersToArray($rs)
    );
  }
}
?>
