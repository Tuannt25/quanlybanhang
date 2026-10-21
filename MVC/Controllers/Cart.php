<?php
class Cart extends Controller {

  public function Index(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $cart = $_SESSION['cart'] ?? [];

    $msp = $this->model("SanPhamModel");
    $items=[];
    $tong=0;

    foreach($cart as $masp=>$qty){
      $rs=$msp->find($masp);
      if(!$rs || mysqli_num_rows($rs)==0) continue;
      $sp=mysqli_fetch_assoc($rs);
      $qty=(int)$qty;
      $tt = (float)$sp['gia']*$qty;
      $tong += $tt;
      $items[] = ["sp"=>$sp, "qty"=>$qty, "thanhtien"=>$tt];
    }

    $this->view("UserMaster", [
      "page" => "cart",
      "items" => $items,
      "tong" => $tong
    ]);
  }

  public function Update(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $qty = $_POST['qty'] ?? [];
    if (!is_array($qty)) $qty = [];
    $msp = $this->model("SanPhamModel");
    foreach($qty as $masp=>$q){
      $rs = $msp->find($masp);
      if (!$rs || mysqli_num_rows($rs) === 0) {
        unset($_SESSION['cart'][$masp]);
        continue;
      }
      $sp = mysqli_fetch_assoc($rs);
      $stock = (int)$sp['soluong'];
      if ($stock <= 0) {
        unset($_SESSION['cart'][$masp]);
        continue;
      }
      $_SESSION['cart'][$masp] = min(max(1, (int)$q), $stock);
    }
    $_SESSION['flash_success']="Đã cập nhật giỏ hàng";
    $this->redirect("Cart/Index");
  }

  public function Remove($masp=""){
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['cart'][$masp]);
    $_SESSION['flash_success']="Đã xóa khỏi giỏ";
    $this->redirect("Cart/Index");
  }

  public function Clear(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['cart']);
    $_SESSION['flash_success']="Đã xóa giỏ hàng";
    $this->redirect("Cart/Index");
  }

  public function Checkout(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if(($_SESSION['role'] ?? '') !== 'KHACHHANG'){
      $_SESSION['flash_error']="Bạn cần đăng nhập tài khoản khách hàng để đặt hàng";
      $this->redirect("Auth/Login"); return;
    }
    $makh = $_SESSION['makh'] ?? '';
    if($makh===''){
      $_SESSION['flash_error']="Tài khoản chưa gắn khách hàng (makh)";
      $this->redirect("Cart/Index"); return;
    }

    $cart = $_SESSION['cart'] ?? [];
    $mdh = $this->model("DonHangModel");
    $msp = $this->model("SanPhamModel");

    [$ok,$msg] = $mdh->createOrderFromCart($makh, $cart);
    if(!$ok){
      $_SESSION['flash_error']=$msg;
      $this->redirect("Cart/Index"); return;
    }
    unset($_SESSION['cart']);
    $_SESSION['flash_success']="Đặt hàng thành công! Mã đơn: ".$msg;
    $this->redirect("Orders/Index");
  }
}
?>
