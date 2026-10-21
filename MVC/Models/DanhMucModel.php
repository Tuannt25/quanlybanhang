<?php
class DanhMucModel extends connectDB {

  public function all(){
    return mysqli_query($this->con, "SELECT * FROM danhmuc ORDER BY madm DESC");
  }

  public function find($madm){
    $id = mysqli_real_escape_string($this->con, $madm);
    return mysqli_query($this->con, "SELECT * FROM danhmuc WHERE madm='$id' LIMIT 1");
  }

  public function search($keyword){
    $k = mysqli_real_escape_string($this->con, $keyword);
    return mysqli_query($this->con, "SELECT * FROM danhmuc WHERE madm LIKE '%$k%' OR tendm LIKE '%$k%' ORDER BY madm DESC");
  }

  public function insert($madm,$tendm){
    $madm = mysqli_real_escape_string($this->con,$madm);
    $tendm = mysqli_real_escape_string($this->con,$tendm);
    return mysqli_query($this->con, "INSERT INTO danhmuc(madm,tendm) VALUES('$madm','$tendm')");
  }

  public function update($madm,$tendm){
    $madm = mysqli_real_escape_string($this->con,$madm);
    $tendm = mysqli_real_escape_string($this->con,$tendm);
    return mysqli_query($this->con, "UPDATE danhmuc SET tendm='$tendm' WHERE madm='$madm'");
  }

  public function delete($madm){
    $madm = mysqli_real_escape_string($this->con,$madm);
    return mysqli_query($this->con, "DELETE FROM danhmuc WHERE madm='$madm'");
  }

  public function toArrayRows(){
    $rs = $this->all();
    $out = [];
    while($r=mysqli_fetch_assoc($rs)){
      $out[] = [$r['madm'],$r['tendm']];
    }
    return $out;
  }
}

?>
