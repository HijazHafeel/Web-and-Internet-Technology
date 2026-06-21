const formMsg = document.getElementById('formMsg');
const eventForm = document.getElementById('eventForm');
const submitBtn = document.getElementById('submitBtn');
const cancelEditBtn = document.getElementById('cancelEditBtn');
const formTitle = document.getElementById('formTitle');
const myEventsList = document.getElementById('myEventsList');

function fillForm(evt) {
  document.getElementById('eventId').value = evt.event_id;
  document.getElementById('title').value = evt.title;
  document.getElementById('date').value = evt.event_date;
  document.getElementById('time').value = evt.start_time.slice(0, 5);
  document.getElementById('endTime').value = evt.end_time ? evt.end_time.slice(0, 5) : '';
  document.getElementById('location').value = evt.location;
  document.getElementById('category').value = evt.category;
  document.getElementById('desc').value = evt.description || '';
  document.getElementById('capacity').value = evt.capacity ?? '';
  document.getElementById('organizer').value = evt.organizer || '';

  formTitle.textContent = 'Edit event';
  submitBtn.innerHTML = '<b>Save changes</b>';
  cancelEditBtn.style.display = 'inline-block';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
  eventForm.reset();
  document.getElementById('eventId').value = '';
  formTitle.textContent = 'Create an Event';
  submitBtn.innerHTML = '<b>Add event</b>';
  cancelEditBtn.style.display = 'none';
}

cancelEditBtn.addEventListener('click', resetForm);

eventForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  hideMessage(formMsg);

  const payload = {
    title: document.getElementById('title').value.trim(),
    event_date: document.getElementById('date').value,
    start_time: document.getElementById('time').value,
    end_time: document.getElementById('endTime').value,
    location: document.getElementById('location').value.trim(),
    category: document.getElementById('category').value,
    description: document.getElementById('desc').value.trim(),
    capacity: document.getElementById('capacity').value,
    organizer: document.getElementById('organizer').value.trim(),
  };

  const id = document.getElementById('eventId').value;
  submitBtn.disabled = true;

  const res = id
    ? await api.post('events/update.php', { event_id: id, ...payload })
    : await api.post('events/create.php', payload);

  submitBtn.disabled = false;

  if (!res.success) {
    showMessage(formMsg, res.message || 'Could not save the event.', 'error');
    return;
  }

  showMessage(formMsg, res.message, 'success');
  resetForm();
  await loadMyEvents();
});

function statusBadge(status) {
  return `<span class="badge badge-${status}">${status}</span>`;
}

function myEventCard(evt) {
  const item = document.createElement('article');
  item.className = 'event-item';
  const seatInfo = evt.capacity !== null
    ? `${evt.registration_count}/${evt.capacity} registered`
    : `${evt.registration_count} registered`;

  item.innerHTML = `
    <h3>${escapeHtml(evt.title)} ${statusBadge(evt.status)}</h3>
    <p>${escapeHtml(evt.description || 'No description provided.')}</p>
    <div class="event-meta">
      <span><strong>Date:</strong> ${formatDate(evt.event_date)}</span>
      <span><strong>Time:</strong> ${formatTime(evt.start_time)}${evt.end_time ? ' – ' + formatTime(evt.end_time) : ''}</span>
      <span><strong>Location:</strong> ${escapeHtml(evt.location)}</span>
      <span>${seatInfo}</span>
    </div>
    <div style="margin-top:12px;display:flex;gap:8px">
      <button class="btn btn-outline btn-sm" data-action="edit" data-id="${evt.event_id}">Edit</button>
      <button class="btn btn-danger btn-sm" data-action="delete" data-id="${evt.event_id}">Delete</button>
    </div>
  `;
  return item;
}

let myEvents = [];

async function loadMyEvents() {
  const res = await api.get('events/list.php?scope=mine');
  if (!res.success) {
    myEventsList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load your events.')}</div>`;
    return;
  }
  myEvents = res.events;
  myEventsList.innerHTML = '';
  if (!myEvents.length) {
    myEventsList.innerHTML = '<div class="empty-state">You haven\'t created any events yet. Use the form above to add one.</div>';
    return;
  }
  myEvents.forEach((evt) => myEventsList.appendChild(myEventCard(evt)));
}

myEventsList.addEventListener('click', async (e) => {
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  const id = btn.dataset.id;
  const action = btn.dataset.action;

  if (action === 'edit') {
    const evt = myEvents.find((x) => String(x.event_id) === id);
    if (evt) fillForm(evt);
    return;
  }

  if (action === 'delete') {
    if (!confirm('Delete this event? This cannot be undone.')) return;
    const res = await api.post('events/delete.php', { event_id: id });
    if (!res.success) {
      alert(res.message || 'Could not delete the event.');
    }
    await loadMyEvents();
  }
});

(async function init() {
  const user = await loadNavUser();
  if (!user || user.role !== 'student') {
    window.location.href = 'login.html';
    return;
  }
  await loadMyEvents();
})();
