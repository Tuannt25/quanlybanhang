<?php
class DanhMuc extends Controller {

  public function Get_data(){
    $this->requireStaff();
    $m=$this->model("DanhMucModel");
    $rows=$m->all();

    $edit=null;
    if(isset($_GET['edit'])){
      $rs=$m->find($_GET['edit']);
      if($rs && mysqli_num_rows($rs)>0) $edit=mysqli_fetch_assoc($rs);
    }

    $this->view("Master", ["page"=>"danhmuc","rows"=>$rows,"edit"=>$edit]);
  }

  public function Edit($madm=""){
    $this->requireStaff();
    $this->redirect("DanhMuc/Get_data?edit=".$madm);
  }

  public function Save(){
    $this->requireStaff();
    $m = $this->model("DanhMucModel");

    $madm = $_POST['madm'] ?? '';       // Mã cũ (ẩn, dùng khi sửa)
    $new_madm = $_POST['new_madm'] ?? ''; // Mã mới (người dùng nhập khi thêm)
    $tendm = $_POST['tendm'] ?? '';

    // Phân luồng: Nếu $madm rỗng => Đang THÊM MỚI
    if($madm === ''){
        
        // --- BẮT ĐẦU KIỂM TRA TRÙNG MÃ ---
        $check = $m->find($new_madm);
        if(mysqli_num_rows($check) > 0){
            echo "<script>alert('Lỗi: Mã danh mục \"$new_madm\" đã tồn tại!'); window.history.back();</script>";
            return; // Dừng chạy hàm
        }
        // --- KẾT THÚC KIỂM TRA ---

        $ok = $m->insert($new_madm, $tendm);
        $msg = $ok ? "Thêm danh mục thành công" : "Thêm thất bại";

    } else {
        // Nếu $madm có dữ liệu => Đang CẬP NHẬT
        $ok = $m->update($madm, $tendm);
        $msg = $ok ? "Cập nhật thành công" : "Cập nhật thất bại";
    }

    $this->view("Master", [
        "page" => "danhmuc",
        "rows" => $m->all(),
        "msg" => $msg,
        "msgType" => $ok ? "success" : "error"
    ]);
}
  public function Delete($madm=""){
    $this->requireStaff();
    $m=$this->model("DanhMucModel");
    $ok=$m->delete($madm);

    $this->view("Master", [
      "page"=>"danhmuc",
      "rows"=>$m->all(),
      "msg"=>$ok?"Xóa thành công":"Xóa thất bại (có thể đang có sản phẩm thuộc danh mục)",
      "msgType"=>$ok?"success":"error"
    ]);
  }

  public function Search(){
    $this->requireStaff();
    $m=$this->model("DanhMucModel");
    $k=$_POST['keyword'] ?? '';
    $this->view("Master", ["page"=>"danhmuc","rows"=>$m->search($k)]);
  }

  public function Export(){
    $this->requireStaff();
    $m=$this->model("DanhMucModel");
    ExcelHelper::export("danhmuc", ["Mã DM","Tên DM"], $m->toArrayRows());
  }

  public function Import(){
    $this->requireStaff();
    if(empty($_FILES['file']['tmp_name'])) $this->redirect("DanhMuc/Get_data");
    $m=$this->model("DanhMucModel");
    $data = ExcelHelper::import($_FILES['file']['tmp_name']);

    for($i=0;$i<count($data);$i++){
      $row=$data[$i];
      if(count($row)<2) continue;
      [$madm,$tendm] = $row;
      if(!$madm) continue;

      $rs=$m->find($madm);
      if($rs && mysqli_num_rows($rs)>0) $m->update($madm,$tendm);
      else $m->insert($madm,$tendm);
    }
    $this->redirect("DanhMuc/Get_data");
  }
}
?>
