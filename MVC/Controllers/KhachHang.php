<?php
class KhachHang extends Controller {

  public function Get_data(){
    $this->requireStaff();
    $m=$this->model("KhachHangModel");
    $rows=$m->all();

    $edit=null;
    if(isset($_GET['edit'])){
      $rs=$m->find($_GET['edit']);
      if($rs && mysqli_num_rows($rs)>0) $edit=mysqli_fetch_assoc($rs);
    }

    $this->view("Master", ["page"=>"khachhang","rows"=>$rows,"edit"=>$edit]);
  }

  public function Edit($makh=""){
    $this->requireStaff();
    $this->redirect("KhachHang/Get_data?edit=".$makh);
  }

  public function Save(){
    $this->requireStaff();
    $m = $this->model("KhachHangModel");

    $makh = $_POST['makh'] ?? '';      // Mã ẩn (dùng để xác định là sửa hay thêm)
    $new = $_POST['new_makh'] ?? '';   // Mã nhập trong input
    $tenkh = $_POST['tenkh'] ?? '';
    $email = $_POST['email'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $diachi = $_POST['diachi'] ?? '';

    $ok = false;
    $msg = "";

    if($makh === ''){
      // --- TRƯỜNG HỢP THÊM MỚI ---
      
      // 1. Kiểm tra trùng mã trước
      $check = $m->find($new);
      if($check && mysqli_num_rows($check) > 0){
          // Nếu tìm thấy dòng nào đó -> Trùng mã
          $ok = false;
          $msg = "Mã khách hàng ($new) đã tồn tại, vui lòng chọn mã khác!";
      } else {
          // 2. Nếu không trùng thì mới thêm
          $ok = $m->insert($new, $tenkh, $email, $sdt, $diachi);
          $msg = $ok ? "Thêm khách hàng thành công" : "Thêm thất bại";
      }

    } else {
      // --- TRƯỜNG HỢP CẬP NHẬT ---
      // Lưu ý: Thường update sẽ dựa trên $makh cũ, không cho sửa mã.
      $ok = $m->update($makh, $tenkh, $email, $sdt, $diachi);
      $msg = $ok ? "Cập nhật thành công" : "Cập nhật thất bại";
    }

    // Load lại view kèm thông báo
    $this->view("Master", [
        "page" => "khachhang",
        "rows" => $m->all(),
        "msg" => $msg,
        "msgType" => $ok ? "success" : "error" // Nếu trùng mã ($ok=false) sẽ hiện màu đỏ (error)
    ]);
  }

  public function Delete($makh=""){
    $this->requireStaff();
    $m=$this->model("KhachHangModel");
    $ok=$m->delete($makh);

    $this->view("Master", ["page"=>"khachhang","rows"=>$m->all(),"msg"=>$ok?"Xóa thành công":"Xóa thất bại","msgType"=>$ok?"success":"error"]);
  }

  public function Search(){
    $this->requireStaff();
    $m=$this->model("KhachHangModel");
    $k=$_POST['keyword'] ?? '';
    $this->view("Master", ["page"=>"khachhang","rows"=>$m->search($k)]);
  }

  public function Export(){
    $this->requireStaff();
    $m=$this->model("KhachHangModel");
    ExcelHelper::export("khachhang", ["Mã KH","Tên KH","Email","SĐT","Địa chỉ"], $m->toArrayRows());
  }

  public function Import(){
    $this->requireStaff();
    if(empty($_FILES['file']['tmp_name'])) $this->redirect("KhachHang/Get_data");
    $m=$this->model("KhachHangModel");
$data = ExcelHelper::import($_FILES['file']['tmp_name']);

    for($i=0;$i<count($data);$i++){
      $row=$data[$i];
      if(count($row)<5) continue;
      [$makh,$tenkh,$email,$sdt,$diachi]=$row;
      if(!$makh) continue;

      $rs=$m->find($makh);
      if($rs && mysqli_num_rows($rs)>0) $m->update($makh,$tenkh,$email,$sdt,$diachi);
      else $m->insert($makh,$tenkh,$email,$sdt,$diachi);
    }
    $this->redirect("KhachHang/Get_data");
  }
}
?>
