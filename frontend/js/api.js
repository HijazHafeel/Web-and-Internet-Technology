/**
 * Shared API helper.
 * Backend lives at ../../backend relative to every page in /frontend/html/.
 * All requests are sent with credentials so the PHP session cookie is kept.
 */
const API_BASE = (() => {
  // Get the current page path, e.g., /campus-connect/campus-connect/frontend/html/signup.html
  const pathname = window.location.pathname;
  
  // Find where /frontend/html/ starts
  const htmlIndex = pathname.indexOf('/frontend/html/');
  
  if (htmlIndex !== -1) {
    // Everything before /frontend/html/ is the base, then append /backend
    const basePath = pathname.substring(0, htmlIndex);
    return basePath + '/backend';
  }
  
  // Fallback for typical structure
  return '/campus-connect/campus-connect/backend';
})();

async function apiRequest(path, options = {}) {
  const opts = {
    method: options.method || 'GET',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
  };
  if (options.body) {
    opts.body = JSON.stringify(options.body);
  }

  let response;
  try {
    response = await fetch(`${API_BASE}/${path}`, opts);
  } catch (err) {
    return { success: false, message: 'Could not reach the server. Make sure XAMPP (Apache + MySQL) is running.' };
  }

  let data;
  try {
    data = await response.json();
  } catch (err) {
    return { success: false, message: 'Unexpected server response.' };
  }
  return data;
}

const api = {
  get: (path) => apiRequest(path),
  post: (path, body) => apiRequest(path, { method: 'POST', body }),
};

/** Shows an inline message box (replaces alert()). el is a .form-msg element. */
function showMessage(el, message, type = 'error') {
  if (!el) return;
  el.textContent = message;
  el.className = `form-msg show ${type}`;
}

function hideMessage(el) {
  if (!el) return;
  el.className = 'form-msg';
}

/** Escapes text before inserting into innerHTML. */
function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

/** Formats "HH:MM:SS" -> "h:mm AM/PM" */
function formatTime(value) {
  if (!value) return '';
  const [h, m] = value.split(':');
  const hour = parseInt(h, 10);
  const period = hour >= 12 ? 'PM' : 'AM';
  const hour12 = ((hour + 11) % 12) + 1;
  return `${hour12}:${m} ${period}`;
}

/** Formats "YYYY-MM-DD" -> "Jan 5, 2027" */
function formatDate(value) {
  if (!value) return '';
  const d = new Date(value + 'T00:00:00');
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function htmlPage(page) {
  return `${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1)}${page}`;
}

function setNavLinks(user) {
  const navAuthArea = document.getElementById('navAuthArea');
  if (!navAuthArea) return;

  if (!user) {
    navAuthArea.innerHTML = `
      <a href="login.html" class="btn btn-outline btn-sm">Sign in</a>
      <a href="signup.html" class="btn btn-sm">Sign up</a>
    `;
    return;
  }

  const workspace = user.role === 'admin'
    ? '<a href="admin.html" class="btn btn-outline btn-sm">Admin</a>'
    : '<a href="event.html" class="btn btn-outline btn-sm">My events</a>';

  navAuthArea.innerHTML = `
    <span class="nav-user">${escapeHtml(user.full_name || user.user_id)}</span>
    ${workspace}
    <button type="button" class="btn btn-sm" id="logoutBtn">Log out</button>
  `;

  document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    await api.post('auth/logout.php', {});
    window.location.href = htmlPage('login.html');
  });
}

/** Loads the current session user and updates navAuthArea when present. */
async function loadNavUser() {
  const data = await api.get('auth/check_session.php');
  const user = data.success && data.user && data.user.user_id ? data.user : null;
  setNavLinks(user);
  return user;
}
