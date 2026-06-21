const formMsg = document.getElementById('formMsg');
const pendingList = document.getElementById('pendingList');
const allEventsList = document.getElementById('allEventsList');
const statusFilter = document.getElementById('statusFilter');
const announcementsList = document.getElementById('announcementsList');
const studentsList = document.getElementById('studentsList');
const studentSearch = document.getElementById('studentSearch');

// --- Tabs ---
document.querySelectorAll('.tab-btn').forEach((btn) => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach((b) => b.classList.remove('active'));
    document.querySelectorAll('.panel').forEach((p) => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(`panel-${btn.dataset.tab}`).classList.add('active');
  });
});

function statusBadge(status) {
  return `<span class="badge badge-${status}">${status}</span>`;
}

function adminEventCard(evt, { showApprove }) {
  const item = document.createElement('article');
  item.className = 'admin-event-item';
  const seatInfo = evt.capacity !== null
    ? `${evt.registration_count}/${evt.capacity} registered`
    : `${evt.registration_count} registered`;

  let actions = `
    <button class="btn btn-outline btn-sm" data-action="edit" data-id="${evt.event_id}">Edit</button>
    <button class="btn btn-danger btn-sm" data-action="delete" data-id="${evt.event_id}">Delete</button>
  `;
  if (showApprove) {
    actions = `
      <button class="btn btn-success btn-sm" data-action="approve" data-id="${evt.event_id}">Approve</button>
      <button class="btn btn-danger btn-sm" data-action="reject" data-id="${evt.event_id}">Reject</button>
    ` + actions;
  }

  item.innerHTML = `
    <h3>${escapeHtml(evt.title)} ${statusBadge(evt.status)}</h3>
    <p>${escapeHtml(evt.description || 'No description provided.')}</p>
    <div class="admin-event-meta">
      <span><strong>Date:</strong> ${formatDate(evt.event_date)}</span>
      <span><strong>Time:</strong> ${formatTime(evt.start_time)}${evt.end_time ? ' – ' + formatTime(evt.end_time) : ''}</span>
      <span><strong>Location:</strong> ${escapeHtml(evt.location)}</span>
      <span><strong>Created by:</strong> ${escapeHtml(evt.creator_name)} (${escapeHtml(evt.created_by)})</span>
      <span>${seatInfo}</span>
    </div>
    <div class="admin-event-actions">${actions}</div>
  `;
  return item;
}

let allEventsCache = [];
let studentsCache = [];

async function refreshStats() {
  const res = await api.get('events/list.php?scope=all');
  if (!res.success) return;
  const events = res.events;
  document.getElementById('statPending').textContent = events.filter((e) => e.status === 'pending').length;
  document.getElementById('statApproved').textContent = events.filter((e) => e.status === 'approved').length;
  document.getElementById('statRejected').textContent = events.filter((e) => e.status === 'rejected').length;
  document.getElementById('statTotal').textContent = events.length;
}

async function loadPending() {
  const res = await api.get('events/list.php?scope=all&status=pending');
  if (!res.success) {
    pendingList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load events.')}</div>`;
    return;
  }
  pendingList.innerHTML = '';
  if (!res.events.length) {
    pendingList.innerHTML = '<div class="empty-state">Nothing waiting for review right now.</div>';
    return;
  }
  res.events.forEach((evt) => pendingList.appendChild(adminEventCard(evt, { showApprove: true })));
}

async function loadAllEvents() {
  const status = statusFilter.value;
  const res = await api.get(`events/list.php?scope=all${status ? `&status=${status}` : ''}`);
  if (!res.success) {
    allEventsList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load events.')}</div>`;
    return;
  }
  allEventsCache = res.events;
  allEventsList.innerHTML = '';
  if (!res.events.length) {
    allEventsList.innerHTML = '<div class="empty-state">No events found.</div>';
    return;
  }
  res.events.forEach((evt) => allEventsList.appendChild(adminEventCard(evt, { showApprove: evt.status === 'pending' })));
}

statusFilter.addEventListener('change', loadAllEvents);

async function handleAction(action, id) {
  if (action === 'approve' || action === 'reject') {
    const res = await api.post('events/approve.php', { event_id: id, action });
    if (!res.success) alert(res.message || 'Could not update the event.');
  } else if (action === 'delete') {
    if (!confirm('Delete this event? This cannot be undone.')) return;
    const res = await api.post('events/delete.php', { event_id: id });
    if (!res.success) alert(res.message || 'Could not delete the event.');
  } else if (action === 'edit') {
    const evt = allEventsCache.find((x) => String(x.event_id) === id)
      || (await api.get(`events/get.php?id=${id}`)).event;
    if (!evt) return;
    const newTitle = prompt('Edit title:', evt.title);
    if (newTitle === null) return;
    const res = await api.post('events/update.php', {
      event_id: id,
      title: newTitle,
      event_date: evt.event_date,
      start_time: evt.start_time,
      end_time: evt.end_time,
      location: evt.location,
      category: evt.category,
      description: evt.description,
      capacity: evt.capacity,
      organizer: evt.organizer,
    });
    if (!res.success) alert(res.message || 'Could not update the event.');
  }

  await Promise.all([loadPending(), loadAllEvents(), refreshStats()]);
}

pendingList.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  handleAction(btn.dataset.action, btn.dataset.id);
});

allEventsList.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  handleAction(btn.dataset.action, btn.dataset.id);
});

function studentCard(student) {
  const item = document.createElement('article');
  item.className = 'student-item';
  item.innerHTML = `
    <div>
      <h3>${escapeHtml(student.full_name)}</h3>
      <div class="student-meta">
        <span><strong>ID:</strong> ${escapeHtml(student.user_id)}</span>
        <span><strong>Email:</strong> ${escapeHtml(student.email)}</span>
        <span>${student.event_count} events</span>
        <span>${student.registration_count} registrations</span>
      </div>
    </div>
    <button class="btn btn-danger btn-sm" data-action="delete-student" data-id="${escapeHtml(student.user_id)}">Delete</button>
  `;
  return item;
}

function renderStudents() {
  const query = studentSearch.value.trim().toLowerCase();
  const students = query
    ? studentsCache.filter((student) =>
        `${student.user_id} ${student.full_name} ${student.email}`.toLowerCase().includes(query)
      )
    : studentsCache;

  studentsList.innerHTML = '';
  if (!students.length) {
    studentsList.innerHTML = `<div class="empty-state">${studentsCache.length ? 'No students match your search.' : 'No student users found.'}</div>`;
    return;
  }
  students.forEach((student) => studentsList.appendChild(studentCard(student)));
}

async function loadStudents() {
  const res = await api.get('users/list.php');
  if (!res.success) {
    studentsList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load students.')}</div>`;
    return;
  }
  studentsCache = res.students;
  document.getElementById('statStudents').textContent = studentsCache.length;
  renderStudents();
}

studentSearch.addEventListener('input', renderStudents);

studentsList.addEventListener('click', async (e) => {
  const btn = e.target.closest('button[data-action="delete-student"]');
  if (!btn) return;
  const userId = btn.dataset.id;
  if (!confirm(`Delete student ${userId}? Their events and registrations will also be removed.`)) return;

  const res = await api.post('users/delete.php', { user_id: userId });
  if (!res.success) {
    alert(res.message || 'Could not delete this student.');
    return;
  }
  await Promise.all([loadStudents(), loadPending(), loadAllEvents(), refreshStats()]);
});

// --- Announcements ---
async function loadAnnouncements() {
  const res = await api.get('announcements/list.php');
  if (!res.success) {
    announcementsList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load announcements.')}</div>`;
    return;
  }
  announcementsList.innerHTML = '';
  if (!res.announcements.length) {
    announcementsList.innerHTML = '<div class="empty-state">No announcements posted yet.</div>';
    return;
  }
  res.announcements.forEach((a) => {
    const item = document.createElement('article');
    item.className = 'admin-event-item';
    item.innerHTML = `
      <h3>${escapeHtml(a.title)}</h3>
      <p>${escapeHtml(a.message)}</p>
      <div class="admin-event-meta">
        <span><strong>Posted by:</strong> ${escapeHtml(a.posted_by_name)}</span>
        <span>${new Date(a.created_at).toLocaleString()}</span>
      </div>
    `;
    announcementsList.appendChild(item);
  });
}

document.getElementById('announcementForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideMessage(formMsg);
  const title = document.getElementById('annTitle').value.trim();
  const message = document.getElementById('annMessage').value.trim();
  const btn = document.getElementById('annSubmitBtn');
  btn.disabled = true;

  const res = await api.post('announcements/create.php', { title, message });
  btn.disabled = false;

  if (!res.success) {
    showMessage(formMsg, res.message || 'Could not post the announcement.', 'error');
    return;
  }
  showMessage(formMsg, res.message, 'success');
  document.getElementById('announcementForm').reset();
  await loadAnnouncements();
});

(async function init() {
  const user = await loadNavUser();
  if (!user || user.role !== 'admin') {
    window.location.href = 'login.html';
    return;
  }
  await Promise.all([loadPending(), loadAllEvents(), refreshStats(), loadStudents(), loadAnnouncements()]);
})();
