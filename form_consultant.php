<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultant Team Record — Ivor Paine Memorial Hospital</title>
<link rel="stylesheet" href="forms.css">
<style>

.pos-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 500;
  font-family: var(--font-body);
}
.pos-Registrar         { background:rgba(52,211,153,.15); color:#34d399; border:1px solid rgba(52,211,153,.3); }
.pos-AssistantRegistrar{ background:rgba(251,191,36,.15); color:#fbbf24; border:1px solid rgba(251,191,36,.3); }
.pos-SeniorHouseman    { background:rgba(20,184,166,.15); color:#2dd4bf; border:1px solid rgba(20,184,166,.3); }
.pos-JuniorHouseman    { background:rgba(99,179,237,.15); color:#93c5fd; border:1px solid rgba(99,179,237,.3); }
.pos-Student           { background:rgba(168,85,247,.15); color:#c084fc; border:1px solid rgba(168,85,247,.3); }


.timeline { padding: 4px 0 0 0; }
.timeline-item {
  display: flex;
  gap: 0;
  position: relative;
}
.timeline-item + .timeline-item::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0; width: 1px;
  background: var(--border);
}
</style>
</head>
<body>

<div class="topbar">
  <div class="hospital-badge">
    <div class="cross-icon">✚</div>
    <div class="hospital-name">
      <strong>Ivor Paine Memorial Hospital</strong><br>
      Management System
    </div>
  </div>
  <div class="topbar-right">
    <a class="nav-link" href="index.html">← Dashboard</a>
    <a class="nav-link" href="form_patient.php">Patient Record</a>
    <a class="nav-link" href="form_ward.php">Ward Record</a>
    <button class="btn-print" onclick="window.print()">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
  </div>
</div>

<div class="search-card">
  <span class="search-label">Consultant Team Record</span>
  <span class="sep">·</span>
  <span style="font-size:12px;color:var(--text-3)">Input Staff No</span>
  <input class="search-input" type="number" id="sno" placeholder="e.g. 101" min="1"
         onkeydown="if(event.key==='Enter') load()">
  <span style="font-size:12px;color:var(--text-3)">or pick</span>
  <select class="search-select" id="doc-select" style="min-width:200px"
          onchange="if(this.value) document.getElementById('sno').value=this.value">
    <option value="">— Select Doctor —</option>
  </select>
  <button class="btn-load" onclick="load()">Load Record</button>
</div>

<div class="form-card">
  <div class="form-header">
    <div class="form-header-title">IVOR PAINE MEMORIAL HOSPITAL</div>
    <div class="form-header-sub">Consultant Team Record</div>
  </div>
  <div id="form-body">
    <div class="state-box">
      <div class="state-icon">👨‍⚕️</div>
      <div class="state-text">Enter a <strong>Staff No</strong> above and click <strong>Load Record</strong></div>
    </div>
  </div>
</div>

<script>
const API = 'api.php';

// populate doctor dropdown
(async () => {
  try {
    const data = await fetch(`${API}?resource=doctors`).then(r => r.json());
    const sel  = document.getElementById('doc-select');
    if (Array.isArray(data)) {
      data.forEach(d => sel.appendChild(
        new Option(`${d.StaffNo}  ·  ${d.FullName}  (${d.Position})`, d.StaffNo)
      ));
    }
    const urlSno = new URLSearchParams(location.search).get('sno');
    if (urlSno) { document.getElementById('sno').value = urlSno; load(); }
  } catch(e) {}
})();

async function load() {
  const sno = document.getElementById('sno').value.trim();
  if (!sno) { alert('Please enter a Staff No.'); return; }

  document.getElementById('form-body').innerHTML = `
    <div class="state-box">
      <div class="spinner-ring"></div>
      <div class="state-text">Loading staff record…</div>
    </div>`;

  try {
    const [docArr, exps, perfs] = await Promise.all([
      fetch(`${API}?resource=doctors&id=${encodeURIComponent(sno)}`).then(r => r.json()),
      fetch(`${API}?resource=prev_experience&staff_no=${encodeURIComponent(sno)}`).then(r => r.json()),
      fetch(`${API}?resource=performance&staff_no=${encodeURIComponent(sno)}`).then(r => r.json()),
    ]);

    const doc = Array.isArray(docArr) ? docArr[0] : docArr;
    if (!doc || doc.error) { showError(`No doctor found with Staff No: <strong>${sno}</strong>`); return; }

    const expList  = Array.isArray(exps)  ? exps  : [];
    const perfList = Array.isArray(perfs) ? perfs : [];

    const posClass = (doc.Position || '').replace(/\s/g,'');

    const expRows = expList.length
      ? expList.map(e => `
          <tr>
            <td>${fmt(e.FromDate)}</td>
            <td>${fmt(e.ToDate)}</td>
            <td>${e.Position}</td>
            <td class="td-name">${e.Establishment}</td>
          </tr>`).join('')
      : `<tr><td colspan="4" class="td-muted" style="text-align:center;padding:20px">No previous experience on record</td></tr>`;

    const perfRows = perfList.length
      ? perfList.map(p => `
          <tr>
            <td>${fmt(p.Date)}</td>
            <td><span class="grade grade-${p.Grade}">${p.Grade}</span></td>
          </tr>`).join('')
      : `<tr><td colspan="2" class="td-muted" style="text-align:center;padding:20px">No performance records on record</td></tr>`;

    document.getElementById('form-body').innerHTML = `

      <!-- Row 1: Staff No | Name -->
      <div class="fields-row fields-row-2">
        <div class="field-group">
          <div class="field-label">Staff No</div>
          <div class="field-value mono">${doc.StaffNo}</div>
        </div>
        <div class="field-group">
          <div class="field-label">Name</div>
          <div class="field-value">${doc.FullName}</div>
        </div>
      </div>

      <!-- Row 2: Position | Date Joined Team -->
      <div class="fields-row fields-row-2">
        <div class="field-group">
          <div class="field-label">Position</div>
          <div class="field-value">
            <span class="pos-badge pos-${posClass}">${doc.Position}</span>
          </div>
        </div>
        <div class="field-group">
          <div class="field-label">Date Joined Team</div>
          <div class="field-value">${fmt(doc.DateJoinedTeam)}</div>
        </div>
      </div>

      <!-- Row 3: Consultant + Specialty -->
      <div class="fields-row fields-row-2">
        <div class="field-group no-border-bottom">
          <div class="field-label">Consultant</div>
          <div class="field-value ${!doc.ConsultantName ? 'empty' : ''}">${doc.ConsultantName ?? 'Not assigned to a team'}</div>
        </div>
        <div class="field-group no-border-bottom">
          <div class="field-label">Specialty</div>
          <div class="field-value ${!doc.ConsultantSpecialty ? 'empty' : ''}">${doc.ConsultantSpecialty ?? '—'}</div>
        </div>
      </div>

      <!-- Previous Experience -->
      <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-title">Previous Experience</div>
        <div class="section-divider-line right"></div>
      </div>

      <div class="data-table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>From Date</th>
              <th>To Date</th>
              <th>Position</th>
              <th>Establishment</th>
            </tr>
          </thead>
          <tbody>${expRows}</tbody>
        </table>
      </div>

      <!-- Progress / Performance -->
      <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-title">Progress — Performance History</div>
        <div class="section-divider-line right"></div>
      </div>

      <div class="data-table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Performance Grade</th>
            </tr>
          </thead>
          <tbody>${perfRows}</tbody>
        </table>
      </div>
    `;
  } catch(e) {
    showError(`Error loading record.<br><small>${e.message}</small>`);
  }
}

function showError(msg) {
  document.getElementById('form-body').innerHTML = `
    <div class="state-box error">
      <div class="state-icon">⚠</div>
      <div class="state-text">${msg}</div>
    </div>`;
}

function fmt(d) {
  if (!d) return '—';
  try { return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}); }
  catch { return d; }
}
</script>
</body>
</html>