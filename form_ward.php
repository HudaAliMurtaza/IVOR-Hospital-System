<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ward Record — Ivor Paine Memorial Hospital</title>
<link rel="stylesheet" href="forms.css">
<style>
    
.nurse-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-family: var(--font-body);
  margin: 3px 3px 3px 0;
  border: 1px solid var(--border);
  background: var(--navy-3);
  color: var(--text-2);
}
.nurse-pill .dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}
.dot-day   { background: #f59e0b; }
.dot-night { background: #818cf8; }
.dot-staff { background: #34d399; }
.dot-nonreg{ background: #94a3b8; }
.nurse-empty { color: var(--text-3); font-style: italic; font-size: 12px; padding: 6px 0; }

.cu-badge {
  display: inline-flex; align-items: center;
  padding: 3px 10px;
  background: rgba(59,127,255,.1);
  border: 1px solid rgba(59,127,255,.2);
  border-radius: 20px;
  font-size: 11px;
  font-family: var(--font-mono);
  color: #93c5fd;
  margin: 2px;
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
    <a class="nav-link" href="form_consultant.php">Consultant Team</a>
    <button class="btn-print" onclick="window.print()">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
  </div>
</div>
<div class="search-card">
  <span class="search-label">Ward Record</span>
  <span class="sep">·</span>
  <span style="font-size:12px;color:var(--text-3)">Select Ward</span>
  <select class="search-select" id="ward-select">
    <option value="">Loading wards…</option>
  </select>
  <button class="btn-load" onclick="load()">Load Record</button>
</div>

<div class="form-card">
  <div class="form-header">
    <div class="form-header-title">IVOR PAINE MEMORIAL HOSPITAL</div>
    <div class="form-header-sub">Ward Record</div>
  </div>
  <div id="form-body">
    <div class="state-box">
      <div class="state-icon">🏥</div>
      <div class="state-text">Select a <strong>Ward</strong> above and click <strong>Load Record</strong></div>
    </div>
  </div>
</div>

<script>
const API = 'api.php';

// Populate ward dropdown
(async () => {
  try {
    const data = await fetch(`${API}?resource=wards`).then(r => r.json());
    const sel  = document.getElementById('ward-select');
    sel.innerHTML = '<option value="">— Select a Ward —</option>';
    if (Array.isArray(data)) {
      data.forEach(w => sel.appendChild(new Option(`${w.WardName}  ·  ${w.SpecialtyName}`, w.WardNo)));
    }
    const urlW = new URLSearchParams(location.search).get('ward');
    if (urlW) { sel.value = urlW; load(); }
  } catch(e) {
    document.getElementById('ward-select').innerHTML = '<option>Error loading wards</option>';
  }
})();

async function load() {
  const wardNo = document.getElementById('ward-select').value;
  if (!wardNo) { alert('Please select a ward.'); return; }

  document.getElementById('form-body').innerHTML = `
    <div class="state-box">
      <div class="spinner-ring"></div>
      <div class="state-text">Loading ward record…</div>
    </div>`;

  try {
    const [wardsData, nurses, patients] = await Promise.all([
      fetch(`${API}?resource=wards`).then(r => r.json()),
      fetch(`${API}?resource=nurses&ward=${wardNo}`).then(r => r.json()),
      fetch(`${API}?resource=patients&ward=${wardNo}`).then(r => r.json()),
    ]);

    const wardInfo = Array.isArray(wardsData) ? wardsData.find(w => String(w.WardNo) === String(wardNo)) : null;
    if (!wardInfo) { showError(`Ward not found.`); return; }

    const nursesByType = t => (Array.isArray(nurses) ? nurses : []).filter(n => n.NurseType === t);
    const daySisters   = nursesByType('DaySister');
    const nightSisters = nursesByType('NightSister');
    const staffNurses  = nursesByType('StaffNurse');
    const nonReg       = nursesByType('NonRegistered');

    const pillList = (arr, dotClass) => arr.length
      ? arr.map(n => `<span class="nurse-pill"><span class="dot ${dotClass}"></span>${n.FName} ${n.LName}</span>`).join('')
      : `<span class="nurse-empty">None assigned</span>`;

    const patRows = (Array.isArray(patients) ? patients : []).map(p => `
      <tr>
        <td class="td-mono">#${p.PatientNo}</td>
        <td class="td-name">${p.FullName || `${p.FName} ${p.LName}`}</td>
        <td><span class="cu-badge">CU-${p.CareUnitNo ?? '—'}</span></td>
        <td class="td-mono">B-${p.BedNo ?? '—'}</td>
        <td class="td-muted">${p.PrimaryDoctorName ?? '—'}</td>
        <td>${fmt(p.DateAdmitted)}</td>
      </tr>`).join('');

    document.getElementById('form-body').innerHTML = `

      <!-- Row 1: Ward Name | Specialty -->
      <div class="fields-row fields-row-2">
        <div class="field-group">
          <div class="field-label">Ward Name</div>
          <div class="field-value">${wardInfo.WardName}</div>
        </div>
        <div class="field-group">
          <div class="field-label">Specialty</div>
          <div class="field-value">${wardInfo.SpecialtyName ?? '—'}</div>
        </div>
      </div>

      <!-- Row 2: Day Sister | Night Sister -->
      <div class="fields-row fields-row-2">
        <div class="field-group">
          <div class="field-label">Day Sister</div>
          <div class="field-value">${pillList(daySisters, 'dot-day')}</div>
        </div>
        <div class="field-group">
          <div class="field-label">Night Sister</div>
          <div class="field-value">${pillList(nightSisters, 'dot-night')}</div>
        </div>
      </div>

      <!-- Row 3: Staff Nurses | Non-Registered Nurses -->
      <div class="fields-row fields-row-2">
        <div class="field-group no-border-bottom">
          <div class="field-label">Staff Nurses</div>
          <div class="field-value">${pillList(staffNurses, 'dot-staff')}</div>
        </div>
        <div class="field-group no-border-bottom">
          <div class="field-label">Non-Registered Nurses</div>
          <div class="field-value">${pillList(nonReg, 'dot-nonreg')}</div>
        </div>
      </div>

      <!-- Patient Information -->
      <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-title">Patient Information</div>
        <div class="section-divider-line right"></div>
      </div>

      <div class="data-table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Patient No</th>
              <th>Patient Name</th>
              <th>Care Unit</th>
              <th>Bed No</th>
              <th>Consultant / Doctor</th>
              <th>Date Admitted</th>
            </tr>
          </thead>
          <tbody>
            ${patRows || `<tr><td colspan="6" class="td-muted" style="text-align:center;padding:28px">No patients currently in this ward</td></tr>`}
          </tbody>
        </table>
      </div>
    `;
  } catch(e) {
    showError(`Error loading ward data.<br><small>${e.message}</small>`);
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