const eventsList = document.getElementById('eventsList');
const searchInput = document.getElementById('searchInput');
const totalCount = document.getElementById('totalCount');
const todayCount = document.getElementById('todayCount');
const upcomingCount = document.getElementById('upcomingCount');

let currentUser = null;
let allEvents = [];

function updateStats(events) {
  const today = new Date().toISOString().slice(0, 10);
  totalCount.textContent = events.length;
  todayCount.textContent = events.filter((evt) => evt.event_date === today).length;
  upcomingCount.textContent = events.filter((evt) => evt.event_date >= today).length;
}

function eventCard(evt) {
  const isStudent = currentUser && currentUser.role === 'student';
  const full = evt.capacity !== null && evt.registration_count >= evt.capacity;
  const date = new Date(evt.event_date + 'T00:00:00');
  const month = date.toLocaleDateString(undefined, { month: 'short' });
  const day = date.toLocaleDateString(undefined, { day: '2-digit' });

  let actionHtml = '';
  if (isStudent) {
    if (evt.is_registered) {
      actionHtml = `<button class="btn btn-outline btn-sm" data-action="unregister" data-id="${evt.event_id}">Cancel registration</button>`;
    } else if (full) {
      actionHtml = `<button class="btn btn-sm" disabled>Event full</button>`;
    } else {
      actionHtml = `<button class="btn btn-sm" data-action="register" data-id="${evt.event_id}">Register</button>`;
    }
  }

  const seatInfo = evt.capacity !== null
    ? `${evt.registration_count}/${evt.capacity} seats filled`
    : `${evt.registration_count} registered`;


  const item = document.createElement('article');
  item.className = 'event-item';
  item.innerHTML = `
    <img class="event-thumb" src="../images/event.jpg" alt="Campus event thumbnail">
    <div class="event-date">
      <span>${month}</span>
      <strong>${day}</strong>
    </div>
    <div class="event-body">
      <div class="event-title-row">
        <h3>${escapeHtml(evt.title)}</h3>
        <span class="badge" style="background:var(--accent-soft);color:var(--accent-2)">${escapeHtml(evt.category || 'General')}</span>
      </div>
      <p>${escapeHtml(evt.description || 'No description provided.')}</p>
      <div class="event-meta">
        <span><strong>Time:</strong> ${formatTime(evt.start_time)}${evt.end_time ? ' - ' + formatTime(evt.end_time) : ''}</span>
        <span><strong>Location:</strong> ${escapeHtml(evt.location)}</span>
        <span><strong>Organizer:</strong> ${escapeHtml(evt.organizer || evt.creator_name)}</span>
        <span><strong>Seats:</strong> ${seatInfo}</span>
      </div>
      <div class="event-actions">${actionHtml}</div>
    </div>
  `;
  return item;
}

function render() {
  const query = searchInput.value.trim().toLowerCase();
  const filtered = query
    ? allEvents.filter((evt) =>
        (evt.title + ' ' + evt.location + ' ' + (evt.description || '')).toLowerCase().includes(query)
      )
    : allEvents;

  eventsList.innerHTML = '';
  if (!filtered.length) {
    eventsList.innerHTML = `<div class="empty-state">${
      allEvents.length ? 'No events match your search.' : 'No approved events yet. Check back soon!'
    }</div>`;
  } else {
    filtered.forEach((evt) => eventsList.appendChild(eventCard(evt)));
  }
  updateStats(allEvents);
}

async function loadEvents() {
  const res = await api.get('events/list.php?scope=public');
  if (!res.success) {
    eventsList.innerHTML = `<div class="empty-state">${escapeHtml(res.message || 'Could not load events.')}</div>`;
    return;
  }
  allEvents = res.events;
  render();
}

eventsList.addEventListener('click', async (e) => {
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  const id = btn.dataset.id;
  const action = btn.dataset.action;
  btn.disabled = true;
  const res = await api.post('events/register.php', { event_id: id, action });
  if (!res.success) {
    alert(res.message || 'Something went wrong.');
  }
  await loadEvents();
});

searchInput.addEventListener('input', render);

(async function init() {
  currentUser = await loadNavUser();
  await loadEvents();
})();
