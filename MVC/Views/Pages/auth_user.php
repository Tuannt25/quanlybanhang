<?php
// Tự động kiểm tra mode để giữ panel đúng vị trí khi load trang
$isRegister = (isset($data['mode']) && $data['mode'] === 'register');
?>

<div class="auth-container <?= $isRegister ? 'right-panel-active' : '' ?>" id="authContainer">

  <div class="form-container sign-up-container">
    <form method="post" action="<?= BASE_URL ?>Auth/DoRegister" autocomplete="off">
      <h1>Tạo tài khoản</h1>
      <div class="auth-subtitle">Nhập đầy đủ thông tin để đăng ký</div>

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
    <form method="post" action="<?= BASE_URL ?>Auth/DoLogin" autocomplete="off">
      <h1>Đăng nhập</h1>
      <div class="auth-subtitle">Vui lòng nhập tài khoản</div>

      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>

     <a href="<?= BASE_URL ?>Auth/Forgot">Quên mật khẩu?</a>
      <button class="btn" type="submit">Đăng nhập</button>
    </form>
  </div>

  <div class="overlay-container">
    <div class="overlay">
      <div class="overlay-panel overlay-left">
        <h1>Chào mừng!</h1>
        <p>Đã có tài khoản? Quay lại đăng nhập để mua sắm.</p>
        <button class="btn ghost" id="signInBtn" type="button">Đăng nhập ngay</button>
      </div>
      <div class="overlay-panel overlay-right">
        <h1>Xin chào!</h1>
        <p>Chưa có tài khoản? Đăng ký để bắt đầu đặt hàng.</p>
        <button class="btn ghost" id="signUpBtn" type="button">Đăng ký ngay</button>
      </div>
    </div>
  </div>
</div>

<script>
    // JS xử lý chuyển đổi mượt mà
    const container = document.getElementById('authContainer');
    const signUpBtn = document.getElementById('signUpBtn');
    const signInBtn = document.getElementById('signInBtn');

    if(signUpBtn && signInBtn) {
        signUpBtn.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInBtn.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    }
</script>
