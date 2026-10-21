<link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/auth.css">
<div class="auth-container" style="min-height:520px">
  <div class="form-container sign-in-container" style="width:100%">
    <form method="post" action="<?= BASE_URL ?>Auth/DoForgot">
      <h1>Quên mật khẩu</h1>
      <span>Nhập Username + Email để yêu cầu hỗ trợ</span>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <button type="submit">Gửi yêu cầu hỗ trợ</button>
      <a href="<?= BASE_URL ?>Auth/Login" style="margin-top:10px">Quay lại đăng nhập</a>
    </form>
  </div>
</div>
