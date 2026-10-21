<?php
class NhanVienModel extends connectDB {

  public function all(){
    $sql="SELECT nv.*, tk.username, tk.role
          FROM nhanvien nv
          LEFT JOIN taikhoan tk ON tk.manv = nv.manv
          ORDER BY nv.manv DESC";
    return mysqli_query($this->con,$sql);
  }

  public function find($manv){
    $id=(int)$manv;
    return mysqli_query($this->con, "SELECT * FROM nhanvien WHERE manv=$id LIMIT 1");
  }

  public function search($keyword){
    $k=mysqli_real_escape_string($this->con,$keyword);
    $sql="SELECT nv.*, tk.username, tk.role
          FROM nhanvien nv
          LEFT JOIN taikhoan tk ON tk.manv = nv.manv
          WHERE nv.hoten LIKE '%$k%' OR nv.email LIKE '%$k%' OR nv.sdt LIKE '%$k%' OR nv.chucvu LIKE '%$k%'
          ORDER BY nv.manv DESC";
    return mysqli_query($this->con,$sql);
  }

  public function insert($hoten,$email,$sdt,$diachi,$chucvu){
    $hoten=mysqli_real_escape_string($this->con,$hoten);
    $email=mysqli_real_escape_string($this->con,$email);
    $sdt=mysqli_real_escape_string($this->con,$sdt);
    $diachi=mysqli_real_escape_string($this->con,$diachi);
    $chucvu=mysqli_real_escape_string($this->con,$chucvu);
    $sql="INSERT INTO nhanvien(hoten,email,sdt,diachi,chucvu)
          VALUES('$hoten','$email','$sdt','$diachi','$chucvu')";
    return mysqli_query($this->con,$sql);
  }

  public function update($manv,$hoten,$email,$sdt,$diachi,$chucvu){
    $id=(int)$manv;
    $hoten=mysqli_real_escape_string($this->con,$hoten);
    $email=mysqli_real_escape_string($this->con,$email);
    $sdt=mysqli_real_escape_string($this->con,$sdt);
    $diachi=mysqli_real_escape_string($this->con,$diachi);
    $chucvu=mysqli_real_escape_string($this->con,$chucvu);
    $sql="UPDATE nhanvien SET hoten='$hoten',email='$email',sdt='$sdt',diachi='$diachi',chucvu='$chucvu' WHERE manv=$id";
    return mysqli_query($this->con,$sql);
  }

  public function delete($manv){
    $id=(int)$manv;
    return mysqli_query($this->con,"DELETE FROM nhanvien WHERE manv=$id");
  }

  public function lastInsertId(){
    return mysqli_insert_id($this->con);
  }

  public function toArrayRows(){
    $rs=$this->all();
    $out=[];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['manv'],$r['hoten'],$r['email'],$r['sdt'],$r['diachi'],$r['chucvu']];
    }
    return $out;
  }
}

?>
