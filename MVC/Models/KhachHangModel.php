<?php
class KhachHangModel extends connectDB {

  public function all(){
    return mysqli_query($this->con, "SELECT * FROM khachhang ORDER BY makh DESC");
  }

  public function find($makh){
    $id = mysqli_real_escape_string($this->con, $makh);
    return mysqli_query($this->con, "SELECT * FROM khachhang WHERE makh='$id' LIMIT 1");
  }

  public function search($keyword){
    $k = mysqli_real_escape_string($this->con, $keyword);
    return mysqli_query($this->con, "SELECT * FROM khachhang
      WHERE tenkh LIKE '%$k%' OR email LIKE '%$k%' OR sdt LIKE '%$k%' ORDER BY makh DESC");
  }

  public function insert($makh,$tenkh,$email,$sdt,$diachi){
    $makh=mysqli_real_escape_string($this->con,$makh);
    $tenkh=mysqli_real_escape_string($this->con,$tenkh);
    $email=mysqli_real_escape_string($this->con,$email);
    $sdt=mysqli_real_escape_string($this->con,$sdt);
    $diachi=mysqli_real_escape_string($this->con,$diachi);
    return mysqli_query($this->con, "INSERT INTO khachhang(makh,tenkh,email,sdt,diachi)
      VALUES('$makh','$tenkh','$email','$sdt','$diachi')");
  }

  public function update($makh,$tenkh,$email,$sdt,$diachi){
    $makh=mysqli_real_escape_string($this->con,$makh);
    $tenkh=mysqli_real_escape_string($this->con,$tenkh);
    $email=mysqli_real_escape_string($this->con,$email);
    $sdt=mysqli_real_escape_string($this->con,$sdt);
    $diachi=mysqli_real_escape_string($this->con,$diachi);
    return mysqli_query($this->con, "UPDATE khachhang SET tenkh='$tenkh',email='$email',sdt='$sdt',diachi='$diachi' WHERE makh='$makh'");
  }

  public function delete($makh){
    $makh=mysqli_real_escape_string($this->con,$makh);
    return mysqli_query($this->con, "DELETE FROM khachhang WHERE makh='$makh'");
  }

  public function toArrayRows(){
    $rs = $this->all();
    $out=[];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['makh'],$r['tenkh'],$r['email'],$r['sdt'],$r['diachi']];
    }
    return $out;
  }
}

?>
