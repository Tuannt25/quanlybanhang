<?php
class ChiTietDonHang extends Controller {

  public function Get_data(){
    $this->requireStaff();
    $m=$this->model("DonHangModel");

    $madh = $_GET['madh'] ?? '';
    $rows = $madh ? $m->orderDetails($madh) : null;

    $this->view("Master", ["page"=>"chitietdonhang","rows"=>$rows,"madh"=>$madh]);
  }

  public function ByDon($madh=""){
    $this->requireStaff();
    $this->redirect("ChiTietDonHang/Get_data?madh=".$madh);
  }

  public function Search(){
    $this->requireStaff();
    $madh=$_POST['madh'] ?? '';
    $this->redirect("ChiTietDonHang/Get_data?madh=".$madh);
  }

  public function Export(){
    $this->requireStaff();
    $m=$this->model("DonHangModel");
    $madh = $_GET['madh'] ?? '';
    if(!$madh) $this->redirect("ChiTietDonHang/Get_data");
    $rs=$m->orderDetails($madh);
    ExcelHelper::export("chitietdonhang_".$madh, ["Mã đơn","Mã SP","Tên SP","Số lượng","Đơn giá"], $m->exportDetailsToArray($rs));
  }
}
?>
