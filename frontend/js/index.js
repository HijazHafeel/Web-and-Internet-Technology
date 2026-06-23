// Simple gate: entering a Student ID here is NOT a real login.
// It only checks that the ID looks valid before sending the visitor
// to the public events page. Real authentication (with a password)
// happens on login.html and is required before anyone can add or
// manage events.

document.getElementById('gateForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const msg = document.getElementById('formMsg');
  const id = document.getElementById('studentId').value.trim().toUpperCase();
  const parts = id.split('/');
  const enrolledYear = Number(parts[1]);
  const currentYear = new Date().getFullYear();

  if (!/^[A-Z]{2,5}\/[0-9]{4}\/[0-9]{3}$/.test(id) || enrolledYear > currentYear || currentYear - enrolledYear > 4) {
    showMessage(msg, 'Please enter a current Student ID like EC/2022/049.', 'error');
    return;
  }

  window.location.href = 'showevents.html';
});
