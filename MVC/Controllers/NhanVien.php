<?php
class NhanVien extends Controller {

  public function Get_data(){
    $this->requireAdmin();
    $m=$this->model("NhanVienModel");
    $rows=$m->all();

    $edit=null;
    if(isset($_GET['edit'])){
      $rs=$m->find($_GET['edit']);
      if($rs && mysqli_num_rows($rs)>0) $edit=mysqli_fetch_assoc($rs);
    }

    $this->view("Master", ["page"=>"nhanvien","rows"=>$rows,"edit"=>$edit]);
  }

  public function Edit($manv=""){
    $this->requireAdmin();
    $this->redirect("NhanVien/Get_data?edit=".$manv);
  }

  public function Save(){
    $this->requireAdmin();
    $nv=$this->model("NhanVienModel");
    $tk=$this->model("TaiKhoanModel");

    $manv=$_POST['manv'] ?? '';
    $hoten=$_POST['hoten'] ?? '';
    $email=$_POST['email'] ?? '';
    $sdt=$_POST['sdt'] ?? '';
    $diachi=$_POST['diachi'] ?? '';
    $chucvu=$_POST['chucvu'] ?? '';

    if($manv===''){
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';
      $role = $_POST['role'] ?? 'NHANVIEN';
      if(!in_array($role, ['ADMIN', 'NHANVIEN'], true)) $role = 'NHANVIEN';

      if($hoten === '' || $username === '' || strlen($password) < 6 || $tk->usernameExists($username)){
        $ok = false;
        $msg = $tk->usernameExists($username)
          ? "Username đã tồn tại"
          : "Vui lòng nhập họ tên, username và mật khẩu ít nhất 6 ký tự";
        $this->view("Master", ["page"=>"nhanvien","rows"=>$nv->all(),"msg"=>$msg,"msgType"=>"error"]);
        return;
      }

      $ok=$nv->insert($hoten,$email,$sdt,$diachi,$chucvu);
      if($ok){
        $newManv = $nv->lastInsertId();
        if(!$tk->insertStaffAccount($username,$password,$email,$role,$newManv)){
          $nv->delete($newManv);
          $ok = false;
        }
      }
      $msg=$ok?"Thêm nhân viên thành công":"Thêm thất bại";
    }else{
      $ok=$nv->update($manv,$hoten,$email,$sdt,$diachi,$chucvu);
      $msg=$ok?"Cập nhật thành công":"Cập nhật thất bại";
    }

    $this->view("Master", ["page"=>"nhanvien","rows"=>$nv->all(),"msg"=>$msg,"msgType"=>$ok?"success":"error"]);
  }

  public function Delete($manv=""){
    $this->requireAdmin();
    $m=$this->model("NhanVienModel");
    $ok=$m->delete($manv);

    $this->view("Master", ["page"=>"nhanvien","rows"=>$m->all(),"msg"=>$ok?"Xóa thành công":"Xóa thất bại","msgType"=>$ok?"success":"error"]);
  }

  public function Search(){
    $this->requireAdmin();
    $m=$this->model("NhanVienModel");
    $k=$_POST['keyword'] ?? '';
    $this->view("Master", ["page"=>"nhanvien","rows"=>$m->search($k)]);
  }

  public function Export(){
    $this->requireAdmin();
    $m=$this->model("NhanVienModel");
    ExcelHelper::export("nhanvien", ["Mã NV","Họ tên","Email","SĐT","Địa chỉ","Chức vụ"], $m->toArrayRows());
  }

  public function Import(){
    $this->requireAdmin();
    if(empty($_FILES['file']['tmp_name'])) $this->redirect("NhanVien/Get_data");
    $m=$this->model("NhanVienModel");
    $data=ExcelHelper::import($_FILES['file']['tmp_name']);

    for($i=0;$i<count($data);$i++){
      $row=$data[$i];
      if(count($row)<6) continue;
      [$manv,$hoten,$email,$sdt,$diachi,$chucvu]=$row;
      if(!$hoten) continue;

      if($manv){
        $m->update($manv,$hoten,$email,$sdt,$diachi,$chucvu);
      }else{
        $m->insert($hoten,$email,$sdt,$diachi,$chucvu);
      }
    }
    $this->redirect("NhanVien/Get_data");
  }
}
?>
