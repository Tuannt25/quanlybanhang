<?php
class SanPhamModel extends connectDB
{


  public function all()
  {
    $sql = "SELECT sp.*, dm.tendm
            FROM sanpham sp 
            JOIN danhmuc dm ON sp.madm = dm.madm
            ORDER BY (sp.soluong <= 0) ASC, sp.masp DESC";
    return mysqli_query($this->con, $sql);
  }

//phát hiện trùng mã
  public function find($masp)
  {
    $id = mysqli_real_escape_string($this->con, $masp);
    $sql = "SELECT sp.*, dm.tendm
            FROM sanpham sp 
            JOIN danhmuc dm ON sp.madm = dm.madm
            WHERE sp.masp='$id' 
            LIMIT 1";
    return mysqli_query($this->con, $sql);
  }

//tìm
  public function search($keyword, $madm = null)
  {
    $k = mysqli_real_escape_string($this->con, trim($keyword));

    $where = " (sp.masp LIKE '%$k%' OR sp.tensp LIKE '%$k%' OR sp.xuatxu LIKE '%$k%') ";

    if ($madm && $madm !== 'ALL' && $madm !== '') {
      $m = mysqli_real_escape_string($this->con, $madm);
      $where .= " AND sp.madm='$m' ";
    }

    $sql = "SELECT sp.*, dm.tendm
            FROM sanpham sp 
            JOIN danhmuc dm ON sp.madm = dm.madm
            WHERE $where
            ORDER BY (sp.soluong <= 0) ASC, sp.masp DESC";
    return mysqli_query($this->con, $sql);
  }


  public function insert($masp, $tensp, $gia, $soluong, $hinhanh, $madm, $xuatxu)
  {
    $masp = mysqli_real_escape_string($this->con, trim($masp));
    $tensp = mysqli_real_escape_string($this->con, trim($tensp));
    $gia = (float) $gia;
    $soluong = (int) $soluong;
    $madm = mysqli_real_escape_string($this->con, $madm);
    $xuatxu= mysqli_real_escape_string($this->con, $xuatxu);

  
    $hinhanh = ($hinhanh !== null && $hinhanh !== '')
      ? mysqli_real_escape_string($this->con, $hinhanh)
      : null;

    $sql = "INSERT INTO sanpham(masp,tensp,gia,soluong,hinhanh,madm,xuatxu)
            VALUES('$masp','$tensp',$gia,$soluong," . ($hinhanh ? "'$hinhanh'" : "NULL") . ",'$madm','$xuatxu')";

    return mysqli_query($this->con, $sql);
  }

  
  public function update($masp, $tensp, $gia, $soluong, $hinhanhNullable, $madm,$xuatxu)
  {
    $masp = mysqli_real_escape_string($this->con, trim($masp));
    $tensp = mysqli_real_escape_string($this->con, trim($tensp));
    $gia = (float) $gia;
    $soluong = (int) $soluong;
    $madm = mysqli_real_escape_string($this->con, $madm);
    $xuatxu= mysqli_real_escape_string($this->con,$xuatxu);

    
    if ($hinhanhNullable === null) {
      $sql = "UPDATE sanpham
              SET tensp='$tensp', gia=$gia, soluong=$soluong, madm='$madm',xuatxu='$xuatxu'
              WHERE masp='$masp'";
      return mysqli_query($this->con, $sql);
    }

    
    $h = ($hinhanhNullable !== '')
      ? mysqli_real_escape_string($this->con, $hinhanhNullable)
      : null;

    $sql = "UPDATE sanpham
            SET tensp='$tensp', gia=$gia, soluong=$soluong, 
                hinhanh=" . ($h ? "'$h'" : "NULL") . ",
                madm='$madm',xuatxu='$xuatxu'
            WHERE masp='$masp'";

    return mysqli_query($this->con, $sql);
  }


  public function delete($masp)
  {
    $masp = mysqli_real_escape_string($this->con, $masp);

   
    $check = mysqli_query($this->con, "SELECT 1 FROM chitietdonhang WHERE masp='$masp' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
      return false; 
    }

   
    return mysqli_query($this->con, "DELETE FROM sanpham WHERE masp='$masp'");
  }

  public function updateQty($masp, $qty)
  {
    $masp = mysqli_real_escape_string($this->con, $masp);
    $qty = (int) $qty;

    if ($qty < 0)
      $qty = 0;

    return mysqli_query($this->con, "UPDATE sanpham SET soluong=$qty WHERE masp='$masp'");
  }
  //thêm 1 hàng mới nếu kho hàng thêm thêm
  public function addStock($masp, $qty)
  {
    $masp = mysqli_real_escape_string($this->con, $masp);
    $qty = (int) $qty;

   
    if ($qty === 0)
      return true;

  
    if ($qty < 0) {
      $abs = abs($qty);
      $sql = "UPDATE sanpham 
              SET soluong = CASE 
                  WHEN (soluong - $abs) < 0 THEN 0
                  ELSE (soluong - $abs)
              END
              WHERE masp='$masp'";
      return mysqli_query($this->con, $sql);
    }

  
    $sql = "UPDATE sanpham 
            SET soluong = soluong + $qty
            WHERE masp='$masp'";
    return mysqli_query($this->con, $sql);
  }

  //trừ kho khi có giao dịch
  public function decreaseStock($masp, $qty)
  {
    $masp = mysqli_real_escape_string($this->con, $masp);
    $qty = (int) $qty;
    if ($qty <= 0)
      return false;

    $sql = "UPDATE sanpham 
            SET soluong = soluong - $qty
            WHERE masp='$masp' AND soluong >= $qty";

    mysqli_query($this->con, $sql);
    return (mysqli_affected_rows($this->con) > 0);
  }


  public function listDanhMuc()
  {
    return mysqli_query($this->con, "SELECT * FROM danhmuc ORDER BY tendm ASC");
  }


  public function toArrayRows()
  {
    $rs = $this->all();
    $out = [];
    while ($r = mysqli_fetch_assoc($rs)) {
      $out[] = [
        $r['masp'],
        $r['tensp'],
        $r['gia'],
        $r['soluong'],
        $r['hinhanh'],
        $r['madm'],
        $r['xuatxu'] ?? ''
      ];
    }
    return $out;
  }

  public function allSimple()
  {
    return mysqli_query($this->con, "SELECT masp, tensp, gia, soluong FROM sanpham ORDER BY tensp ASC");
  }
}
?>
