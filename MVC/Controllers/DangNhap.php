<?php
class DangNhap extends Controller {

    public function Get_data(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Nếu đã đăng nhập rồi thì chuyển hướng thẳng vào trang quản trị hoặc shop
        if(isset($_SESSION['username'])) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }
        $this->view("AuthMaster", ["page"=>"dangnhap"]);
    }

    public function Login(){
        if (session_status() === PHP_SESSION_NONE) session_start();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $m = $this->model("AuthModel");
        
        // SỬA TẠI ĐÂY: Đổi verifyLogin thành login để khớp với AuthModel
        [$ok, $res] = $m->login($username, $password);

        if(!$ok){
            // $res lúc này là chuỗi thông báo lỗi từ Model
            $this->view("AuthMaster", ["page"=>"dangnhap", "msg"=>$res, "msgType"=>"error"]);
            return;
        }

        // Đăng nhập thành công: $res là mảng thông tin tài khoản ($acc)
        $acc = $res;
        session_regenerate_id(true);
        unset($_SESSION['manv'], $_SESSION['makh']);
        $_SESSION['username'] = $acc['username'];
        $_SESSION['matk']     = $acc['matk'];
        $_SESSION['role']     = $acc['role'];
        if ($acc['role'] === 'KHACHHANG') {
            $_SESSION['makh'] = $acc['makh'] ?? '';
        } else {
            $_SESSION['manv'] = $acc['manv'] ?? '';
        }

        // Chuyển hướng thông minh dựa trên quyền
        $this->redirectByRole($acc['role']);
    }

    // Hàm phụ trợ để điều hướng đúng trang
    private function redirectByRole($role){
        if($role === 'ADMIN' || $role === 'NHANVIEN'){
            $this->redirect("ThongKe/NgayThang");
        } else {
            $this->redirect("Shop/Index");
        }
    }

    public function Logout(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        $this->redirect("DangNhap/Get_data");
    }
}
?>
