const formMsg = document.getElementById('formMsg');
const loginForm = document.getElementById('loginForm');
const loginBtn = document.getElementById('loginBtn');

function togglePassword() {
  const pw = document.getElementById('password');
  const btn = document.querySelector('.pw-toggle');
  const shouldShow = pw.type === 'password';
  pw.type = shouldShow ? 'text' : 'password';
  btn.textContent = shouldShow ? 'Hide' : 'Show';
}

loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  hideMessage(formMsg);

  const user_id = document.getElementById('identifier').value.trim();
  const password = document.getElementById('password').value;

  if (!user_id || !password) {
    showMessage(formMsg, 'Please enter your user ID and password.', 'error');
    return;
  }

  loginBtn.disabled = true;
  loginBtn.textContent = 'Signing in...';

  const data = await api.post('auth/login.php', { user_id, password });

  loginBtn.disabled = false;
  loginBtn.textContent = 'Sign in';

  if (!data.success) {
    showMessage(formMsg, data.message || 'Login failed. Please try again.', 'error');
    return;
  }

  const user = data.user;
  localStorage.setItem('user', JSON.stringify(user));
  window.location.href = user.role === 'admin' ? 'admin.html' : 'event.html';
});
