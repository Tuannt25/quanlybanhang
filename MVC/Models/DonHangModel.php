<?php
class DonHangModel extends connectDB
{
  public function generateMaDH()
  {
    for ($attempt = 0; $attempt < 10; $attempt++) {
      $candidate = "DH" . date("ymd") . random_int(100000, 999999);
      $safe = mysqli_real_escape_string($this->con, $candidate);
      $rs = mysqli_query($this->con, "SELECT 1 FROM donhang WHERE madh='$safe' LIMIT 1");
      if ($rs && mysqli_num_rows($rs) === 0) return $candidate;
    }
    throw new Exception("Không thể tạo mã đơn hàng duy nhất");
  }

  public function allOrders()
  {
    $sql = "SELECT dh.*, kh.tenkh
            FROM donhang dh 
            JOIN khachhang kh ON dh.makh = kh.makh
            ORDER BY dh.ngaydat DESC";
    return mysqli_query($this->con, $sql);
  }

  public function searchOrders($keyword)
  {
    $k = mysqli_real_escape_string($this->con, $keyword);
    $sql = "SELECT dh.*, kh.tenkh
            FROM donhang dh 
            JOIN khachhang kh ON dh.makh = kh.makh
            WHERE dh.madh LIKE '%$k%' 
               OR kh.tenkh LIKE '%$k%' 
               OR dh.trangthai LIKE '%$k%'
            ORDER BY dh.ngaydat DESC";
    return mysqli_query($this->con, $sql);
  }

  public function ordersByCustomer($makh)
  {
    $makh = mysqli_real_escape_string($this->con, $makh);
    $sql = "SELECT dh.*, kh.tenkh
            FROM donhang dh 
            JOIN khachhang kh ON dh.makh = kh.makh
            WHERE dh.makh='$makh'
            ORDER BY dh.ngaydat DESC";
    return mysqli_query($this->con, $sql);
  }

  public function orderDetails($madh)
  {
    $madh = mysqli_real_escape_string($this->con, $madh);
    $sql = "SELECT ct.*, sp.tensp
            FROM chitietdonhang ct 
            JOIN sanpham sp ON ct.masp = sp.masp
            WHERE ct.madh='$madh'
            ORDER BY ct.id ASC";
    return mysqli_query($this->con, $sql);
  }

  public function updateStatus($madh, $status)
  {
    $madh = mysqli_real_escape_string($this->con, $madh);
    $st = mysqli_real_escape_string($this->con, $status);
    return mysqli_query($this->con, "UPDATE donhang SET trangthai='$st' WHERE madh='$madh'");
  }

  public function deleteOrder($madh)
  {
    $madh = mysqli_real_escape_string($this->con, $madh);
    mysqli_begin_transaction($this->con);
    try {
      $rs = mysqli_query($this->con, "SELECT da_tru_kho FROM donhang WHERE madh='$madh' FOR UPDATE");
      $order = $rs ? mysqli_fetch_assoc($rs) : null;
      if (!$order || (int)$order['da_tru_kho'] === 1)
        throw new Exception("Không thể xóa đơn đã hoàn thành");

      mysqli_query($this->con, "DELETE FROM chitietdonhang WHERE madh='$madh'");
      $ok = mysqli_query($this->con, "DELETE FROM donhang WHERE madh='$madh'");
      if (!$ok)
        throw new Exception("Xóa thất bại");
      mysqli_commit($this->con);
      return true;
    } catch (Exception $e) {
      mysqli_rollback($this->con);
      return false;
    }
  }

  // ============================================================
  // ✅ TẠO ĐƠN HÀNG: KHÔNG TRỪ KHO
  // - Chỉ kiểm tra kho có đủ không
  // - Lưu đơn + chi tiết
  // - Trạng thái mặc định: "Chờ xử lý"
  // ============================================================
  public function createOrderFromCart($makh, $cart)
  {
    $makh = mysqli_real_escape_string($this->con, $makh);
    if (empty($cart))
      return [false, "Giỏ hàng trống"];

    mysqli_begin_transaction($this->con);

    try {
      $madh = $this->generateMaDH();

      // 1) Tạo đơn hàng gốc (chưa hoàn thành => KHÔNG trừ kho)
      $resDH = mysqli_query($this->con, "INSERT INTO donhang(madh,makh,ngaydat,tongtien,trangthai,da_tru_kho) 
                                         VALUES('$madh','$makh',NOW(),0,'Chờ xử lý',0)");
      if (!$resDH)
        throw new Exception("Không thể khởi tạo đơn hàng");

      $tong = 0;
      $itemCount = 0;

      foreach ($cart as $masp => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0)
          continue;

        $masp_esc = mysqli_real_escape_string($this->con, $masp);

        // 2) Kiểm tra tồn kho đủ (KHÔNG TRỪ)
        $resSP = mysqli_query($this->con, "SELECT tensp, gia, soluong 
                                           FROM sanpham 
                                           WHERE masp='$masp_esc' FOR UPDATE");
        $sp = mysqli_fetch_assoc($resSP);

        if (!$sp)
          throw new Exception("Sản phẩm $masp không tồn tại");
        if ((int) $sp['soluong'] < $qty)
          throw new Exception("Sản phẩm " . $sp['tensp'] . " không đủ tồn kho");

        $dongia = (float) $sp['gia'];
        $tong += $dongia * $qty;

        // 3) Lưu chi tiết đơn hàng
        $okCT = mysqli_query($this->con, "INSERT INTO chitietdonhang(madh,masp,soluong,dongia) 
                                          VALUES('$madh','$masp_esc',$qty,$dongia)");
        if (!$okCT)
          throw new Exception("Không thể lưu chi tiết đơn");
        $itemCount++;
      }

      if ($itemCount === 0)
        throw new Exception("Giỏ hàng không có sản phẩm hợp lệ");

      // 4) Cập nhật tổng tiền
      $okTong = mysqli_query($this->con, "UPDATE donhang SET tongtien=$tong WHERE madh='$madh'");
      if (!$okTong)
        throw new Exception("Không thể cập nhật tổng tiền");

      mysqli_commit($this->con);
      return [true, $madh];

    } catch (Exception $e) {
      mysqli_rollback($this->con);
      return [false, $e->getMessage()];
    }
  }

  public function exportOrdersToArray($rs)
  {
    $out = [];
    while ($r = mysqli_fetch_assoc($rs)) {
      $out[] = [$r['madh'], $r['makh'], $r['tenkh'], $r['ngaydat'], $r['tongtien'], $r['trangthai']];
    }
    return $out;
  }

  public function exportDetailsToArray($rs)
  {
    $out = [];
    while ($r = mysqli_fetch_assoc($rs)) {
      $out[] = [$r['madh'], $r['masp'], $r['tensp'], $r['soluong'], $r['dongia']];
    }
    return $out;
  }

  // ============================================================
  // ✅ UPDATE TRẠNG THÁI: CHỈ TRỪ KHO KHI "HOÀN THÀNH"
  // - Chờ xử lý / Huỷ: KHÔNG TRỪ KHO
  // - Hoàn thành: trừ kho 1 lần duy nhất
  // - Đã hoàn thành: không cho đổi lại (tránh lệch kho)
  // ============================================================
  public function updateTrangThai($madh, $newStatus)
  {
    $madh = mysqli_real_escape_string($this->con, $madh);
    $newStatus = mysqli_real_escape_string($this->con, $newStatus);

    // Chỉ cho 3 trạng thái
    $allow = ["Chờ xử lý", "Hoàn thành", "Huỷ"];
    if (!in_array($newStatus, $allow))
      return false;

    mysqli_begin_transaction($this->con);

    try {
      // 1) Lấy trạng thái hiện tại + cờ đã trừ kho (khóa dòng để tránh spam hoàn thành)
      $q = mysqli_query($this->con, "SELECT trangthai, da_tru_kho 
                                    FROM donhang 
                                    WHERE madh='$madh' FOR UPDATE");
      $old = mysqli_fetch_assoc($q);
      if (!$old)
        throw new Exception("Không tìm thấy đơn");

      $oldStatus = $old["trangthai"];
      $daTruKho = (int) $old["da_tru_kho"];

      // ✅ Nếu đã hoàn thành rồi thì KHÔNG cho đổi nữa
      if ($oldStatus === "Hoàn thành") {
        throw new Exception("Đơn đã hoàn thành, không thể đổi trạng thái!");
      }

      // 2) Nếu trạng thái mới KHÔNG phải Hoàn thành => chỉ update trạng thái (không trừ kho)
      if ($newStatus === "Chờ xử lý" || $newStatus === "Huỷ") {
        $ok = mysqli_query($this->con, "UPDATE donhang 
                                        SET trangthai='$newStatus' 
                                        WHERE madh='$madh'");
        if (!$ok)
          throw new Exception("Cập nhật trạng thái thất bại");

        mysqli_commit($this->con);
        return true;
      }

      // 3) Nếu chuyển sang "Hoàn thành" => TRỪ KHO (chỉ 1 lần)
      if ($newStatus === "Hoàn thành") {

        if ($daTruKho === 1) {
          // đã trừ rồi thì không trừ lại
          $ok = mysqli_query($this->con, "UPDATE donhang 
                                          SET trangthai='Hoàn thành' 
                                          WHERE madh='$madh'");
          if (!$ok)
            throw new Exception("Cập nhật trạng thái thất bại");

          mysqli_commit($this->con);
          return true;
        }

        // 3.1) Lấy chi tiết đơn
        $rs = mysqli_query($this->con, "SELECT masp, soluong 
                                        FROM chitietdonhang 
                                        WHERE madh='$madh'");
        if (!$rs)
          throw new Exception("Không lấy được chi tiết đơn");

        $items = [];
        while ($ct = mysqli_fetch_assoc($rs)) {
          $items[] = $ct;
        }
        if (count($items) === 0)
          throw new Exception("Đơn không có sản phẩm");

        // 3.2) Check kho đủ trước khi trừ
        foreach ($items as $ct) {
          $masp = mysqli_real_escape_string($this->con, $ct["masp"]);
          $sl = (int) $ct["soluong"];

          $qsp = mysqli_query($this->con, "SELECT soluong 
                                          FROM sanpham 
                                          WHERE masp='$masp' FOR UPDATE");
          $sp = mysqli_fetch_assoc($qsp);
          if (!$sp)
            throw new Exception("Sản phẩm $masp không tồn tại");

          $stock = (int) $sp["soluong"];
          if ($stock < $sl) {
            throw new Exception("Không đủ tồn kho cho sản phẩm $masp");
          }
        }

        // 3.3) Trừ kho
        foreach ($items as $ct) {
          $masp = mysqli_real_escape_string($this->con, $ct["masp"]);
          $sl = (int) $ct["soluong"];

          $okTru = mysqli_query($this->con, "UPDATE sanpham 
                                             SET soluong = soluong - $sl 
                                             WHERE masp='$masp' AND soluong >= $sl");
          if (!$okTru || mysqli_affected_rows($this->con) <= 0) {
            throw new Exception("Trừ kho thất bại cho sản phẩm $masp");
          }
        }

        // 3.4) Update trạng thái + đánh dấu đã trừ kho
        $okDH = mysqli_query($this->con, "UPDATE donhang 
                                          SET trangthai='Hoàn thành', da_tru_kho=1 
                                          WHERE madh='$madh'");
        if (!$okDH)
          throw new Exception("Cập nhật đơn hàng thất bại");

        mysqli_commit($this->con);
        return true;
      }

      mysqli_commit($this->con);
      return true;

    } catch (Exception $e) {
      mysqli_rollback($this->con);
      return false;
    }
  }
  public function userCancelOrder($madh, $makh)
  {
    $madh = mysqli_real_escape_string($this->con, $madh);
    $makh = mysqli_real_escape_string($this->con, $makh);

    // ✅ chỉ hủy đơn của chính khách hàng + chỉ khi đang Chờ xử lý
    $sql = "UPDATE donhang
          SET trangthai='Huỷ'
          WHERE madh='$madh'
            AND makh='$makh'
            AND trangthai='Chờ xử lý'";

    mysqli_query($this->con, $sql);
    return mysqli_affected_rows($this->con) > 0;
  }
  public function getOrderByMaDH($madh, $makh){
  $db = new connectDB();
  $con = $db->con;

  $madh = mysqli_real_escape_string($con, $madh);
  $makh = mysqli_real_escape_string($con, $makh);

  $sql = "SELECT * FROM donhang WHERE madh='$madh' AND makh='$makh' LIMIT 1";
  $rs  = mysqli_query($con, $sql);
  if($rs && mysqli_num_rows($rs) > 0){
    return mysqli_fetch_assoc($rs);
  }
  return null;
}

public function getOrderItems($madh, $makh){
  $db = new connectDB();
  $con = $db->con;

  $madh = mysqli_real_escape_string($con, $madh);
  $makh = mysqli_real_escape_string($con, $makh);

  // ✅ join để có tên sản phẩm
  $sql = "SELECT ct.*, sp.tensp 
          FROM chitietdonhang ct
          JOIN donhang dh ON dh.madh = ct.madh
          JOIN sanpham sp ON sp.masp = ct.masp
          WHERE ct.madh='$madh' AND dh.makh='$makh'
          ORDER BY ct.id ASC";

  return mysqli_query($con, $sql);
}
  
}
?>
