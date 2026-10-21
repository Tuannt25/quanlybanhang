<?php
class DangKy extends Controller {

  public function Get_data(){
    $this->view("AuthMaster", ["page"=>"dangky"]);
  }

  public function Register(){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $tenkh = trim($_POST['tenkh'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');

    if($username==='' || $password==='' || $tenkh===''){
      $this->view("AuthMaster", ["page"=>"dangky","msg"=>"Vui lòng nhập đủ thông tin bắt buộc","msgType"=>"error"]);
      return;
    }

    $m=$this->model("AuthModel");
    $rs=$m->findByUsername($username);
    if($rs && mysqli_num_rows($rs)>0){
      $this->view("AuthMaster", ["page"=>"dangky","msg"=>"Username đã tồn tại","msgType"=>"error"]);
      return;
    }

    [$ok,$msg] = $m->createCustomerAccount($username,$password,$email,$tenkh,$sdt,$diachi);
    if($ok){
      // show success then go login
      $this->view("AuthMaster", ["page"=>"dangnhap","msg"=>"Đăng ký hoàn tất. Vui lòng đăng nhập!","msgType"=>"success"]);
    }else{
      $this->view("AuthMaster", ["page"=>"dangky","msg"=>$msg,"msgType"=>"error"]);
    }
  }
}
?>
