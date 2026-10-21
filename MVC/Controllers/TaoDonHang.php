<?php
class TaoDonHang extends Controller {

  public function Get_data(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();

    $kh=$this->model("KhachHangModel");
    $sp=$this->model("SanPhamModel");

    $cart = $_SESSION['cart'] ?? [];

    [$cartRows, $tong] = $this->buildCartRows($cart, $sp);

    $this->view("Master", [
      "page"=>"taodonhang",
      "khs"=>$kh->all(),
      "sps"=>$sp->allSimple(),
      "cartRows"=>$cartRows,
      "tong"=>$tong
    ]);
  }

  public function Add(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();
    $masp=$_POST['masp'] ?? '';
    $qty=(int)($_POST['qty'] ?? 1);
    if($qty<=0) $qty=1;

    if(!isset($_SESSION['cart'])) $_SESSION['cart']=[];
    $_SESSION['cart'][$masp] = (($_SESSION['cart'][$masp] ?? 0) + $qty);

    $this->redirect("TaoDonHang/Get_data");
  }

  public function Update(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();
    $masp=$_POST['masp'] ?? '';
    $qty=(int)($_POST['qty'] ?? 1);
    if($qty<=0) unset($_SESSION['cart'][$masp]);
    else $_SESSION['cart'][$masp]=$qty;
    $this->redirect("TaoDonHang/Get_data");
  }

  public function Remove($masp=""){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();
    if(isset($_SESSION['cart'][$masp])) unset($_SESSION['cart'][$masp]);
    $this->redirect("TaoDonHang/Get_data");
  }

  public function Clear(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['cart']=[];
    $this->redirect("TaoDonHang/Get_data");
  }

  public function Create(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();

    $makh=$_POST['makh'] ?? '';
    $cart=$_SESSION['cart'] ?? [];

    $sp=$this->model("SanPhamModel");
    $dh=$this->model("DonHangModel");

    [$ok,$res] = $dh->createOrderFromCart($makh,$cart);
    if($ok){
      $_SESSION['cart']=[];
      $this->redirect("QuanLyDonHang/Get_data?msg=tao_ok&madh=".$res);
    }else{
      [$cartRows, $tong] = $this->buildCartRows($cart, $sp);
      $this->view("Master", [
        "page"=>"taodonhang",
        "khs"=>$this->model("KhachHangModel")->all(),
        "sps"=>$sp->allSimple(),
        "cartRows"=>$cartRows,
        "tong"=>$tong,
        "msg"=>$res,
        "msgType"=>"error"
      ]);
    }
  }

  private function buildCartRows($cart, $sp){
    $cartRows = [];
    $tong = 0;
    foreach($cart as $masp=>$qty){
      $rs = $sp->find($masp);
      if(!$rs || mysqli_num_rows($rs) === 0) continue;
      $row = mysqli_fetch_assoc($rs);
      $qty = (int)$qty;
      if($qty <= 0) continue;
      $thanhtien = (float)$row['gia'] * $qty;
      $tong += $thanhtien;
      $cartRows[] = [
        "masp"=>$row['masp'],
        "tensp"=>$row['tensp'],
        "gia"=>$row['gia'],
        "qty"=>$qty,
        "thanhtien"=>$thanhtien,
        "ton"=>$row['soluong']
      ];
    }
    return [$cartRows, $tong];
  }
}
?>
