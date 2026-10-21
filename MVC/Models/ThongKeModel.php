<?php
class ThongKeModel extends connectDB {

  public function listDanhMuc(){
    return mysqli_query($this->con,"SELECT * FROM danhmuc ORDER BY tendm ASC");
  }

  public function thongkeNgayThang($from, $to, $madmArr){
    $from = mysqli_real_escape_string($this->con,$from);
    $to = mysqli_real_escape_string($this->con,$to);

    $dmFilter = "";
    if(!empty($madmArr)){
      $safe = [];
      foreach($madmArr as $m){
        $safe[] = "'".mysqli_real_escape_string($this->con,$m)."'";
      }
      $dmFilter = " AND sp.madm IN (".implode(",",$safe).") ";
    }

    $sql="SELECT sp.masp, sp.tensp, dm.tendm, 
                 SUM(ct.soluong) AS tongsl,
                 SUM(ct.soluong * ct.dongia) AS doanhthu
          FROM donhang dh
          JOIN chitietdonhang ct ON dh.madh = ct.madh
          JOIN sanpham sp ON ct.masp = sp.masp
          JOIN danhmuc dm ON sp.madm = dm.madm
          WHERE dh.ngaydat BETWEEN '$from 00:00:00' AND '$to 23:59:59'
                AND dh.trangthai <> 'Huỷ'
                $dmFilter
          GROUP BY sp.masp, sp.tensp, dm.tendm
          ORDER BY doanhthu DESC";
    return mysqli_query($this->con,$sql);
  }

  public function tongQuanNgayThang($from,$to,$madmArr){
    $from = mysqli_real_escape_string($this->con,$from);
    $to = mysqli_real_escape_string($this->con,$to);

    $dmFilter = "";
    if(!empty($madmArr)){
      $safe = [];
      foreach($madmArr as $m){
        $safe[] = "'".mysqli_real_escape_string($this->con,$m)."'";
      }
      $dmFilter = " AND sp.madm IN (".implode(",",$safe).") ";
    }

    $sql="SELECT COUNT(DISTINCT dh.madh) AS tongdon,
                 SUM(ct.soluong * ct.dongia) AS tongtien
          FROM donhang dh
          JOIN chitietdonhang ct ON dh.madh = ct.madh
          JOIN sanpham sp ON ct.masp = sp.masp
          WHERE dh.ngaydat BETWEEN '$from 00:00:00' AND '$to 23:59:59'
                AND dh.trangthai <> 'Huỷ'
                $dmFilter";
    return mysqli_query($this->con,$sql);
  }

  public function topBanChay(){
    $sql="SELECT sp.masp, sp.tensp, dm.tendm,
                 SUM(ct.soluong) AS tongsl,
                 SUM(ct.soluong*ct.dongia) AS doanhthu
          FROM chitietdonhang ct
          JOIN donhang dh ON ct.madh=dh.madh
          JOIN sanpham sp ON ct.masp=sp.masp
          JOIN danhmuc dm ON sp.madm=dm.madm
          WHERE dh.trangthai <> 'Huỷ'
          GROUP BY sp.masp, sp.tensp, dm.tendm
          ORDER BY tongsl DESC
          LIMIT 10";
    return mysqli_query($this->con,$sql);
  }
}

?>
