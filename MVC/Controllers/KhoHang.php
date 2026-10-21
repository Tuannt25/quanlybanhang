<?php
class KhoHang extends Controller {

  public function Get_data(){
    $this->requireStaff();
    $m=$this->model("SanPhamModel");
    $rows=$m->all();
    $this->view("Master", ["page"=>"khohang","rows"=>$rows]);
  }

  // Trong class KhoHang

public function UpdateQty(){
    $this->requireStaff();
    $m = $this->model("SanPhamModel");

    $masp = $_POST['masp'] ?? '';
    $qty = $_POST['soluong'] ?? 0;
    
    // SỬA Ở ĐÂY: Gọi hàm addStock (cộng thêm) thay vì updateQty (ghi đè)
    // Nếu nhập số âm (ví dụ -2) thì nó sẽ tự trừ đi.
    $ok = $m->addStock($masp, $qty);

    $this->view("Master", [
        "page" => "khohang",
        "rows" => $m->all(),
        "msg" => $ok ? "Đã nhập thêm $qty sản phẩm vào kho" : "Cập nhật thất bại",
        "msgType" => $ok ? "success" : "error"
    ]);
}

  public function Search(){
    $this->requireStaff();
    $m=$this->model("SanPhamModel");
    $k=$_POST['keyword'] ?? '';
    $this->view("Master", ["page"=>"khohang","rows"=>$m->search($k,'ALL')]);
  }

  public function Export(){
    $this->requireStaff();
    $m=$this->model("SanPhamModel");
    ExcelHelper::export("khohang", ["Mã SP","Tên SP","Giá","Số lượng","Ảnh","Mã DM"], $m->toArrayRows());
  }

  public function Import(){
    $this->requireStaff();
    if(empty($_FILES['file']['tmp_name'])) $this->redirect("KhoHang/Get_data");
    $m=$this->model("SanPhamModel");
    $data = ExcelHelper::import($_FILES['file']['tmp_name']);
    for($i=0;$i<count($data);$i++){
      $row=$data[$i];
      if(count($row)<4) continue;
      [$masp,$tensp,$gia,$soluong] = $row;
      if(!$masp) continue;
      $m->updateQty($masp,(int)$soluong);
    }
    $this->redirect("KhoHang/Get_data");
  }
}
?>
