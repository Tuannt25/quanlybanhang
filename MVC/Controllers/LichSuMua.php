<?php
class LichSuMua extends Controller {

  public function Get_data(){
    $this->requireStaff();
    if (session_status() === PHP_SESSION_NONE) session_start();
    $role=$_SESSION['role'] ?? '';
    $makh=$_SESSION['makh'] ?? '';

    $m=$this->model("DonHangModel");

    if($role==='KHACHHANG' && $makh){
      $rows=$m->ordersByCustomer($makh);
    } else {
      $rows=$m->allOrders();
    }

    $this->view("Master", ["page"=>"lichsumuacuakhachhang","rows"=>$rows]);
  }

  public function Search(){
    $this->requireStaff();
    $m=$this->model("DonHangModel");
    $k=$_POST['keyword'] ?? '';
    $this->view("Master", ["page"=>"lichsumuacuakhachhang","rows"=>$m->searchOrders($k)]);
  }

  public function Export(){
    $this->requireStaff();
    $m=$this->model("DonHangModel");
    $rs=$m->allOrders();
    ExcelHelper::export("lichsu_muahang", ["Mã đơn","Mã KH","Tên KH","Ngày đặt","Tổng tiền","Trạng thái"], $m->exportOrdersToArray($rs));
  }
}
?>
