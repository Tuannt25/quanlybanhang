<?php
class Orders extends Controller {

  // =========================
  // 1) DANH SÁCH ĐƠN CỦA TÔI
  // =========================
  public function Index(){
    if (session_status() === PHP_SESSION_NONE) session_start();

    if(($_SESSION['role'] ?? '') !== 'KHACHHANG'){
      $_SESSION['flash_error'] = "Bạn cần đăng nhập";
      $this->redirect("Auth/Login");
      return;
    }

    $makh = $_SESSION['makh'] ?? '';
    if($makh === ''){
      $_SESSION['flash_error'] = "Tài khoản chưa gắn khách hàng!";
      $this->redirect("Auth/Login");
      return;
    }

    $m = $this->model("DonHangModel");
    $rs = $m->ordersByCustomer($makh);

    // flash msg
    $msg = $_SESSION['flash_msg'] ?? null;
    $msgType = $_SESSION['flash_type'] ?? null;
    unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

    $this->view("UserMaster", [
      "page" => "orders",
      "rows" => $rs,
      "msg" => $msg,
      "msgType" => $msgType
    ]);
  }

  // =========================
  // 2) IN HÓA ĐƠN (Invoice)
  // =========================
  public function Invoice($madh = ""){
    if (session_status() === PHP_SESSION_NONE) session_start();

    if(($_SESSION['role'] ?? '') !== 'KHACHHANG'){
      $_SESSION['flash_error'] = "Bạn cần đăng nhập khách hàng!";
      $this->redirect("Auth/Login");
      return;
    }

    $makh = $_SESSION['makh'] ?? '';
    if($makh === ''){
      $_SESSION['flash_error'] = "Tài khoản chưa gắn khách hàng!";
      $this->redirect("Orders/Index");
      return;
    }

    if($madh === ''){
      $_SESSION['flash_msg'] = "Thiếu mã đơn hàng!";
      $_SESSION['flash_type'] = "error";
      $this->redirect("Orders/Index");
      return;
    }

    $m = $this->model("DonHangModel");

    // ✅ LẤY THÔNG TIN ĐƠN HÀNG
    $order = $m->getOrderByMaDH($madh, $makh);  // array
    $rows  = $m->getOrderItems($madh, $makh);   // mysqli_result

    if(!$order){
      $_SESSION['flash_error'] = "Không tìm thấy đơn hàng!";
      $this->redirect("Orders/Index");
      return;
    }

    $this->view("UserMaster", [
      "page"  => "order_detail",
      "rows"  => $rows,
      "madh"  => $madh,
      "order" => $order
    ]);
  }

  // =========================
  // 3) CHI TIẾT ĐƠN
  // =========================
  public function Detail($madh = ""){
    if (session_status() === PHP_SESSION_NONE) session_start();

    if(($_SESSION['role'] ?? '') !== 'KHACHHANG'){
      $_SESSION['flash_error']="Bạn cần đăng nhập";
      $this->redirect("Auth/Login");
      return;
    }

    if($madh === ""){
      $_SESSION['flash_msg'] = "Thiếu mã đơn hàng!";
      $_SESSION['flash_type'] = "error";
      $this->redirect("Orders/Index");
      return;
    }

    $m = $this->model("DonHangModel");

    // ✅ kiểm tra đơn có thuộc user không (an toàn hơn)
    $makh = $_SESSION['makh'] ?? '';
    $order = $m->getOrderByMaDH($madh, $makh);
    if(!$order){
      $_SESSION['flash_msg'] = "Đơn hàng không tồn tại hoặc không thuộc về bạn!";
      $_SESSION['flash_type'] = "error";
      $this->redirect("Orders/Index");
      return;
    }

    $rs = $m->getOrderItems($madh, $makh);

    $this->view("UserMaster", [
      "page"  => "order_detail",
      "madh"  => $madh,
      "rows"  => $rs,
      "order" => $order
    ]);
  }

  // =========================
  // 4) ✅ USER HỦY ĐƠN HÀNG
  // =========================
  public function Huy($madh = ""){
    if (session_status() === PHP_SESSION_NONE) session_start();

    if(($_SESSION['role'] ?? '') !== 'KHACHHANG'){
      $_SESSION['flash_error']="Bạn cần đăng nhập";
      $this->redirect("Auth/Login");
      return;
    }

    $makh = $_SESSION['makh'] ?? '';
    if($makh === "" || $madh === ""){
      $_SESSION['flash_msg'] = "Không hợp lệ!";
      $_SESSION['flash_type'] = "error";
      $this->redirect("Orders/Index");
      return;
    }

    $m = $this->model("DonHangModel");

    // ✅ chỉ cho hủy nếu đơn thuộc user và đang "Chờ xử lý"
    $ok = $m->userCancelOrder($madh, $makh);

    if($ok){
      $_SESSION['flash_msg']  = "Hủy đơn $madh thành công!";
      $_SESSION['flash_type'] = "success";
    } else {
      $_SESSION['flash_msg']  = "Không thể hủy đơn $madh (chỉ hủy khi đang Chờ xử lý).";
      $_SESSION['flash_type'] = "error";
    }

    $this->redirect("Orders/Index");
  }
}
?>
