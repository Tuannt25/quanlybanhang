<?php
class TaiKhoanModel extends connectDB {

  public function all(){
    $sql="SELECT tk.*, nv.hoten AS ten_nv, kh.tenkh AS ten_kh
          FROM taikhoan tk
          LEFT JOIN nhanvien nv ON tk.manv = nv.manv
          LEFT JOIN khachhang kh ON tk.makh = kh.makh
          ORDER BY tk.matk DESC";
    return mysqli_query($this->con,$sql);
  }

  public function find($matk){
    $id=(int)$matk;
    return mysqli_query($this->con,"SELECT * FROM taikhoan WHERE matk=$id LIMIT 1");
  }

  public function search($keyword){
    $k=mysqli_real_escape_string($this->con,$keyword);
    $sql="SELECT tk.*, nv.hoten AS ten_nv, kh.tenkh AS ten_kh
          FROM taikhoan tk
          LEFT JOIN nhanvien nv ON tk.manv = nv.manv
          LEFT JOIN khachhang kh ON tk.makh = kh.makh
          WHERE tk.username LIKE '%$k%' OR tk.email LIKE '%$k%' OR tk.role LIKE '%$k%'
          ORDER BY tk.matk DESC";
    return mysqli_query($this->con,$sql);
  }

  public function insertStaffAccount($username,$password,$email,$role,$manv){
    $u=mysqli_real_escape_string($this->con,$username);
    $e=mysqli_real_escape_string($this->con,$email);
    $role=mysqli_real_escape_string($this->con,$role);
    $manv=(int)$manv;
    $hash=password_hash($password, PASSWORD_BCRYPT);

    $sql="INSERT INTO taikhoan(username,password,email,role,is_locked,manv)
          VALUES('$u','$hash','$e','$role',0,$manv)";
    return mysqli_query($this->con,$sql);
  }

  public function usernameExists($username){
    $u = mysqli_real_escape_string($this->con, $username);
    $rs = mysqli_query($this->con, "SELECT 1 FROM taikhoan WHERE username='$u' LIMIT 1");
    return $rs && mysqli_num_rows($rs) > 0;
  }

  public function insertCustom($username,$password,$email,$role,$manvNullable,$makhNullable){
    $u=mysqli_real_escape_string($this->con,$username);
    $e=mysqli_real_escape_string($this->con,$email);
    $role=mysqli_real_escape_string($this->con,$role);
    $hash=password_hash($password, PASSWORD_BCRYPT);

    $manv = $manvNullable ? (int)$manvNullable : "NULL";
    $makh = $makhNullable ? ("'".mysqli_real_escape_string($this->con,$makhNullable)."'") : "NULL";

    $sql="INSERT INTO taikhoan(username,password,email,role,is_locked,manv,makh)
          VALUES('$u','$hash','$e','$role',0,$manv,$makh)";
    return mysqli_query($this->con,$sql);
  }

  public function updateBasic($matk,$email,$role){
    $id=(int)$matk;
    $e=mysqli_real_escape_string($this->con,$email);
    $role=mysqli_real_escape_string($this->con,$role);
    return mysqli_query($this->con,"UPDATE taikhoan SET email='$e', role='$role' WHERE matk=$id");
  }

  public function resetPassword($matk,$newPassword){
    $id=(int)$matk;
    $hash=password_hash($newPassword, PASSWORD_BCRYPT);
    return mysqli_query($this->con,"UPDATE taikhoan SET password='$hash' WHERE matk=$id");
  }

  public function setLock($matk,$locked){
    $id=(int)$matk;
    $locked=(int)$locked;
    return mysqli_query($this->con,"UPDATE taikhoan SET is_locked=$locked WHERE matk=$id");
  }

  public function delete($matk){
    $id=(int)$matk;
    return mysqli_query($this->con,"DELETE FROM taikhoan WHERE matk=$id");
  }

  public function toArrayRows(){
    $rs=$this->all();
    $out=[];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['matk'],$r['username'],$r['email'],$r['role'],$r['is_locked'],$r['manv'],$r['makh']];
    }
    return $out;
  }

  public function listNhanVien(){
    return mysqli_query($this->con,"SELECT manv, hoten FROM nhanvien ORDER BY hoten ASC");
  }

  public function listKhachHang(){
    return mysqli_query($this->con,"SELECT makh, tenkh FROM khachhang ORDER BY tenkh ASC");
  }
}

?>
