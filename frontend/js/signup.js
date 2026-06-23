const formMsg = document.getElementById('formMsg');
const studentIdPattern = /^[A-Z]{2,5}\/[0-9]{4}\/[0-9]{3}$/;
const universityEmailPattern = /^[A-Za-z0-9._%+\-]+@stu\.kln\.ac\.lk$/;

function studentIdIsCurrent(studentId) {
  const year = Number(studentId.split('/')[1]);
  const currentYear = new Date().getFullYear();
  return year <= currentYear && currentYear - year <= 4;
}

document.getElementById('signupForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideMessage(formMsg);

  const user_id = document.getElementById('studentId').value.trim().toUpperCase();
  const full_name = document.getElementById('name').value.trim();
  const university_email = document.getElementById('email').value.trim().toLowerCase();
  const password = document.getElementById('password').value;
  const confirm_password = document.getElementById('confirm').value;

  if (!studentIdPattern.test(user_id) || !studentIdIsCurrent(user_id)) {
    showMessage(formMsg, 'Student ID must look like EC/2022/049 and be within 4 years of enrollment.', 'error');
    return;
  }

  if (!universityEmailPattern.test(university_email)) {
    showMessage(formMsg, 'Use your university student email, for example hijaz@stu.kln.ac.lk.', 'error');
    return;
  }

  if (password !== confirm_password) {
    showMessage(formMsg, 'Passwords do not match.', 'error');
    return;
  }

  const btn = document.getElementById('signupBtn');
  btn.disabled = true;
  btn.textContent = 'Creating account...';

  const res = await api.post('auth/signup.php', {
    user_id, full_name, university_email, password, confirm_password,
  });

  btn.disabled = false;
  btn.textContent = 'Sign up';

  if (!res.success) {
    showMessage(formMsg, res.message || 'Could not create your account.', 'error');
    return;
  }

  showMessage(formMsg, res.message + ' Redirecting to login...', 'success');
  setTimeout(() => { window.location.href = 'login.html'; }, 1200);
});
