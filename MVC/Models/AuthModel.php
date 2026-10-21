<?php
class AuthModel extends connectDB {

  public function findByUsername($username){
    $u = mysqli_real_escape_string($this->con,$username);
    return mysqli_query($this->con,"SELECT * FROM taikhoan WHERE username='$u' LIMIT 1");
  }

  public function findActiveSessionAccount($matk, $username){
    $id = (int)$matk;
    $u = mysqli_real_escape_string($this->con, $username);
    $rs = mysqli_query($this->con, "SELECT matk, username, role, is_locked, manv, makh FROM taikhoan WHERE matk=$id AND username='$u' LIMIT 1");
    if (!$rs || mysqli_num_rows($rs) === 0) return null;
    return mysqli_fetch_assoc($rs);
  }

  public function createCustomerAccount($username,$password,$email,$tenkh,$sdt,$diachi){
    mysqli_begin_transaction($this->con);
    try{
      $u = mysqli_real_escape_string($this->con,$username);
      $e = mysqli_real_escape_string($this->con,$email);
      $tenkh = mysqli_real_escape_string($this->con,$tenkh);
      $sdt = mysqli_real_escape_string($this->con,$sdt);
      $diachi = mysqli_real_escape_string($this->con,$diachi);

      // create customer
      $makh = $this->generateCustomerId();
      $makhEsc = mysqli_real_escape_string($this->con,$makh);
      $ok1 = mysqli_query($this->con,"INSERT INTO khachhang(makh,tenkh,email,sdt,diachi)
                     VALUES('$makhEsc','$tenkh','$e','$sdt','$diachi')");
      if(!$ok1) throw new Exception('Không tạo được khách hàng');

      // create account
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $ok2 = mysqli_query($this->con,"INSERT INTO taikhoan(username,password,email,role,is_locked,manv,makh)
                     VALUES('$u','$hash','$e','KHACHHANG',0,NULL,'$makhEsc')");
      if(!$ok2) throw new Exception('Không tạo được tài khoản');

      mysqli_commit($this->con);
      return [true, 'Đăng ký thành công'];
    }catch(Exception $ex){
      mysqli_rollback($this->con);
      return [false, $ex->getMessage()];
    }
  }

  private function generateCustomerId(){
    for ($attempt = 0; $attempt < 10; $attempt++) {
      $candidate = 'KH' . date('ymd') . random_int(100000, 999999);
      $escaped = mysqli_real_escape_string($this->con, $candidate);
      $rs = mysqli_query($this->con, "SELECT 1 FROM khachhang WHERE makh='$escaped' LIMIT 1");
      if ($rs && mysqli_num_rows($rs) === 0) return $candidate;
    }
    throw new Exception('Không thể tạo mã khách hàng duy nhất');
  }

  // Đổi tên từ verifyLogin thành login
  public function login($username, $password){
      $rs = $this->findByUsername($username);
      if(!$rs || mysqli_num_rows($rs)==0) return [false, 'Sai Username hoặc Password'];

      $acc = mysqli_fetch_assoc($rs);
      if((int)($acc['is_locked'] ?? 0) === 1) return [false, 'Tài khoản đang bị khóa'];

      $ok = password_verify($password, $acc['password']);
      if(!$ok) return [false, 'Sai Username hoặc Password'];

      return [true, $acc];
  }

}
?>
