<?php
class Shop extends Controller {

  public function Index(){
    $msp = $this->model("SanPhamModel");
    $mdm = $this->model("DanhMucModel");

    $kw = trim($_GET['kw'] ?? '');
    $madm = trim($_GET['madm'] ?? '');

    $rs = ($kw!=='' || $madm!=='') ? $msp->search($kw,$madm) : $msp->all();
    $rows=[];
    if($rs) while($r=mysqli_fetch_assoc($rs)) $rows[]=$r;

    $this->view("UserMaster", [
      "page" => "shop",
      "rows" => $rows,
      "danhmuc" => $mdm->all(),
      "kw" => $kw,
      "madm" => $madm
    ]);
  }

  public function Add($masp=""){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if($masp===''){ $this->redirect("Shop/Index"); return; }

    $msp = $this->model("SanPhamModel");
    $rs = $msp->find($masp);
    if (!$rs || mysqli_num_rows($rs) === 0) {
      $_SESSION['flash_error'] = "Sản phẩm không tồn tại";
      $this->redirect("Shop/Index"); return;
    }
    $sp = mysqli_fetch_assoc($rs);
    $stock = (int)$sp['soluong'];
    $current = (int)($_SESSION['cart'][$masp] ?? 0);
    if ($stock <= $current) {
      $_SESSION['flash_error'] = "Số lượng trong giỏ đã đạt mức tồn kho";
      $this->redirect("Cart/Index"); return;
    }

    if(!isset($_SESSION['cart'])) $_SESSION['cart']=[];
    $_SESSION['cart'][$masp] = $current + 1;

    $_SESSION['flash_success']="Đã thêm vào giỏ hàng";
    $this->redirect("Cart/Index");
  }
}
?>
