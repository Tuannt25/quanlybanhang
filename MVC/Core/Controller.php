<?php
class Controller{

    public function model($model){
        include_once './MVC/Models/'.$model.'.php';
        return new $model;
    }

    public function view($view, $data = []){
    // Giải nén mảng để biến thành biến tự do (ví dụ: ['mode'=>'login'] thành $mode)
    extract($data); 
    include_once './MVC/Views/'.$view.'.php';
}

    protected function redirect($path){
    // Xóa dấu / ở đầu nếu có, sau đó nối với BASE_URL
    $location = BASE_URL . ltrim($path, '/');
    header("Location: " . $location);
    exit;
}

    protected function requireLogin(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // Debug: Xóa dòng này sau khi tìm được lỗi
    // var_dump($_SESSION); die(); 

    if(!isset($_SESSION['username'], $_SESSION['matk'])){
        $this->redirect("DangNhap/Get_data");
    }

    $auth = $this->model("AuthModel");
    $account = $auth->findActiveSessionAccount($_SESSION['matk'], $_SESSION['username']);
    if(!$account || (int)$account['is_locked'] === 1 || $account['role'] !== ($_SESSION['role'] ?? '')){
        session_unset();
        session_regenerate_id(true);
        $this->redirect("DangNhap/Get_data");
    }
}

    protected function requireStaff(){
      $this->requireLogin();
      $role = $_SESSION['role'] ?? '';
      if(!in_array($role, ['ADMIN', 'NHANVIEN'], true)){
        $this->redirect("Shop/Index");
      }
    }

    protected function requireAdmin(){
      $this->requireLogin();
      $role = $_SESSION['role'] ?? '';
      if($role !== 'ADMIN'){
        $this->redirect("Shop/Index");
      }
    }
}
?>
