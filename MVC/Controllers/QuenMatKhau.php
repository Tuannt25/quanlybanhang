<?php
class QuenMatKhau extends Controller {

  public function Get_data(){
    $this->view("AuthMaster", ["page"=>"quenmatkhau"]);
  }

  public function Reset(){
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if($username==='' || $email===''){
      $this->view("AuthMaster", ["page"=>"quenmatkhau","msg"=>"Vui lòng nhập username và email","msgType"=>"error"]);
      return;
    }

    $this->view("AuthMaster", [
      "page"=>"quenmatkhau",
      "msg"=>"Đặt lại mật khẩu công khai đã được khóa để bảo vệ tài khoản. Vui lòng liên hệ quản trị viên.",
      "msgType"=>"error"
    ]);
  }
}
?>
