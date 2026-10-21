<?php
class Auth extends Controller {
  public function Index(){
    $this->Login();
  }

  public function Login(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $this->view("AuthMaster", ["page"=>"auth_user","mode"=>"login"]);
  }

  public function Register(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Gửi mode là register
    $this->view("AuthMaster", ["page"=>"auth_user", "mode"=>"register"]);
}

  public function DoLogin(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    $m = $this->model("AuthModel");
    [$ok, $acc] = $m->login($u, $p);

    if(!$ok){
        $_SESSION['flash_error'] = $acc;
        $this->redirect("Auth/Login"); return;
    }

    session_regenerate_id(true);
    unset($_SESSION['manv'], $_SESSION['makh']);

    // --- LƯU SESSION CHUNG ---
    $_SESSION['username'] = $acc['username'];
    $_SESSION['matk']     = $acc['matk'];
    $_SESSION['role']     = $acc['role'];
    $_SESSION['email']    = $acc['email'] ?? '';

    // --- PHÂN LUỒNG ĐIỀU HƯỚNG ---
    if($acc['role'] === 'ADMIN' || $acc['role'] === 'NHANVIEN'){
        // Nếu là Admin/NV: Lưu mã nhân viên và sang trang quản trị
        $_SESSION['manv'] = $acc['manv'] ?? '';
        $_SESSION['flash_success'] = "Chào mừng Admin/Nhân viên quay lại";
        $this->redirect("ThongKe/NgayThang"); 
    } else {
        // Nếu là Khách hàng: Lưu mã khách hàng và sang trang Shop
        $_SESSION['makh'] = $acc['makh'] ?? '';
        $_SESSION['flash_success'] = "Đăng nhập thành công";
        $this->redirect("Shop/Index");
    }
}

  public function DoRegister(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $tenkh = trim($_POST['tenkh'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if($tenkh==='' || $username==='' || $password===''){
      $_SESSION['flash_error']="Vui lòng nhập tên, username và mật khẩu";
      $this->redirect("Auth/Register"); return;
    }
    if(strlen($password) < 6){
      $_SESSION['flash_error']="Mật khẩu phải có ít nhất 6 ký tự";
      $this->redirect("Auth/Register"); return;
    }
    if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
      $_SESSION['flash_error']="Email không hợp lệ";
      $this->redirect("Auth/Register"); return;
    }

    $m = $this->model("AuthModel");
    [$ok, $msg] = $m->createCustomerAccount($username,$password,$email,$tenkh,$sdt,$diachi);
    if(!$ok){
      $_SESSION['flash_error']=$msg;
      $this->redirect("Auth/Register"); return;
    }

    $_SESSION['flash_success']="Đăng ký thành công! Hãy đăng nhập.";
    $this->redirect("Auth/Login");
  }

  public function Forgot(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $this->view("AuthMaster", ["page"=>"quenmatkhau_user"]);
  }

  public function DoForgot(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $u = trim($_POST['username'] ?? '');
    $e = trim($_POST['email'] ?? '');
    if($u==='' || $e===''){
      $_SESSION['flash_error']="Vui lòng nhập Username và Email";
      $this->redirect("Auth/Forgot"); return;
    }

    $_SESSION['flash_error']="Đặt lại mật khẩu công khai đã được khóa để bảo vệ tài khoản. Vui lòng liên hệ quản trị viên.";
    $this->redirect("Auth/Forgot");
  }

  public function Logout(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['flash_success']="Đã đăng xuất";
    $this->redirect("Auth/Login");
  }
}
?>
