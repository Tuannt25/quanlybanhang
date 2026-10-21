<?php
class ThongKe extends Controller {

  public function NgayThang(){
    $this->requireStaff();
    $m=$this->model("ThongKeModel");

    $from = $_POST['from'] ?? date("Y-m-01");
    $to   = $_POST['to'] ?? date("Y-m-d");
    $madmArr = $_POST['madm'] ?? [];

    $rsTong = $m->tongQuanNgayThang($from,$to,$madmArr);
    $tong = ($rsTong && mysqli_num_rows($rsTong)>0) ? mysqli_fetch_assoc($rsTong) : ["tongdon"=>0,"tongtien"=>0];

    $rows = $m->thongkeNgayThang($from,$to,$madmArr);
    $dms  = $m->listDanhMuc();

    $this->view("Master", [
      "page"=>"thongke_ngaythang",
      "from"=>$from, "to"=>$to,
      "selected"=>$madmArr,
      "tong"=>$tong,
      "rows"=>$rows,
      "dms"=>$dms
    ]);
  }

  public function TopBanChay(){
    $this->requireStaff();
    $m=$this->model("ThongKeModel");
    $rows=$m->topBanChay();
    $this->view("Master", ["page"=>"thongke_topbanchay","rows"=>$rows]);
  }

  public function ExportNgayThang(){
    $this->requireStaff();
    $m=$this->model("ThongKeModel");
    $from=$_GET['from'] ?? date("Y-m-01");
    $to=$_GET['to'] ?? date("Y-m-d");
    $madmArr = isset($_GET['madm']) ? explode(",", $_GET['madm']) : [];

    $rs=$m->thongkeNgayThang($from,$to,$madmArr);
    $out=[];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['masp'],$r['tensp'],$r['tendm'],$r['tongsl'],$r['doanhthu']];
    }
    ExcelHelper::export("thongke_".$from."_".$to, ["Mã SP","Tên SP","Danh mục","Tổng SL","Doanh thu"], $out);
  }

  public function ExportTop(){
    $this->requireStaff();
    $m=$this->model("ThongKeModel");
    $rs=$m->topBanChay();
    $out=[];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['masp'],$r['tensp'],$r['tendm'],$r['tongsl'],$r['doanhthu']];
    }
    ExcelHelper::export("top10_banchay", ["Mã SP","Tên SP","Danh mục","Tổng SL","Doanh thu"], $out);
  }
}
?>
