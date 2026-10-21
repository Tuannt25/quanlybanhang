<div class="auth-container">
  <div class="form-container sign-up-container" style="opacity:1; z-index:2; width:100%;">
    <form method="post" action="<?= BASE_URL ?>QuenMatKhau/Reset">
      <h1>Quên mật khẩu</h1>
      <span>Nhập username + email để yêu cầu hỗ trợ</span>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <button type="submit">Gửi yêu cầu hỗ trợ</button>
      <a href="<?= BASE_URL ?>DangNhap/Get_data">← Quay lại đăng nhập</a>
    </form>
  </div>
</div>
