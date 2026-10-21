<?php
// Kiểm tra xem đang ở chế độ nào để thêm class 'right-panel-active' tự động
$activeClass = (isset($data['mode']) && $data['mode'] == 'register') ? 'right-panel-active' : '';
?>

<div class="auth-container <?= $activeClass ?>" id="authContainer">
  <div class="form-container sign-up-container">
    <form method="post" action="<?= BASE_URL ?>DangKy/Register" autocomplete="off">
      <h1>Đăng ký</h1>
      <div class="auth-subtitle">Tạo tài khoản khách hàng</div>

      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="email" name="email" placeholder="Email">
      <input type="text" name="tenkh" placeholder="Họ tên" required>
      <input type="text" name="sdt" placeholder="Số điện thoại">
      <input type="text" name="diachi" placeholder="Địa chỉ">

      <button class="btn" type="submit">Đăng ký</button>
    </form>
  </div>

  <div class="form-container sign-in-container">
    <form method="post" action="<?= BASE_URL ?>DangNhap/Login" autocomplete="off">
      <h1>Đăng nhập</h1>
      <div class="auth-subtitle">Vui lòng nhập tài khoản</div>

      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>

      <a class="auth-link" href="<?= BASE_URL ?>QuenMatKhau/Get_data">Quên mật khẩu?</a>
      <button class="btn" type="submit">Đăng nhập</button>
    </form>
  </div>

  <div class="overlay-container">
    <div class="overlay">
      <div class="overlay-panel overlay-left">
        <h1>Xin chào!</h1>
        <p>Bạn đã có tài khoản?<br>Nhấn đăng nhập để quay lại.</p>
        <button class="btn ghost" type="button" id="signInBtn">Đăng nhập</button>
      </div>

      <div class="overlay-panel overlay-right">
        <h1>Chưa có tài khoản?</h1>
        <p>Nhấn đăng ký để tạo tài khoản mới.</p>
        <button class="btn ghost" type="button" id="signUpBtn">Đăng ký</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const container = document.getElementById('authContainer');
  const signUpBtn = document.getElementById('signUpBtn');
  const signInBtn = document.getElementById('signInBtn');

  // Chuyển sang panel Đăng ký
  if(signUpBtn) {
    signUpBtn.addEventListener('click', () => {
      container.classList.add('right-panel-active');
    });
  }

  // Chuyển sang panel Đăng nhập
  if(signInBtn) {
    signInBtn.addEventListener('click', () => {
      container.classList.remove('right-panel-active');
    });
  }
})();
</script>
