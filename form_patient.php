<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Record — Ivor Paine Memorial Hospital</title>
<link rel="stylesheet" href="forms.css">
</head>
<body>

<!-- ── Topbar ──────────────────────────────────────────── -->
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
    <a class="nav-link" href="form_ward.php">Ward Record</a>
    <a class="nav-link" href="form_consultant.php">Consultant Team</a>
    <button class="btn-print" onclick="window.print()">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print
    </button>
  </div>
</div>

<!-- ── Search Card ─────────────────────────────────────── -->
<div class="search-card">
  <span class="search-label">Patient Record</span>
  <span class="sep">·</span>
  <span style="font-size:12px;color:var(--text-3)">Input Patient No</span>
  <input class="search-input" type="number" id="pno" placeholder="e.g. 1" min="1"
         onkeydown="if(event.key==='Enter') load()">
  <button class="btn-load" onclick="load()">Load Record</button>
</div>

<!-- ── Form Card ───────────────────────────────────────── -->
<div class="form-card" id="form-card">
  <div class="form-header">
    <div class="form-header-title">IVOR PAINE MEMORIAL HOSPITAL</div>
    <div class="form-header-sub">Patient Record</div>
  </div>
  <div id="form-body">
    <div class="state-box">
      <div class="state-icon">📋</div>
      <div class="state-text">Enter a <strong>Patient No</strong> above and click <strong>Load Record</strong></div>
    </div>
  </div>
</div>

<script>
const API = 'api.php';

async function load() {
  const pno = document.getElementById('pno').value.trim();
  if (!pno) { alert('Please enter a Patient No.'); return; }

  document.getElementById('form-body').innerHTML = `
    <div class="state-box">
      <div class="spinner-ring"></div>
      <div class="state-text">Loading patient record…</div>
    </div>`;

  try {
    const res  = await fetch(`${API}?resource=query&name=q10_patient_detail&patient_no=${encodeURIComponent(pno)}`);
    const data = await res.json();

    if (data.error) { showError(data.error); return; }
    if (!Array.isArray(data) || data.length === 0) {
      showError(`No patient found with Patient No: <strong>${pno}</strong>`); return;
    }
    render(data);
  } catch(e) {
    showError('Could not reach api.php. Is the server running?');
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
  if (!d) return null;
  try { return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}); }
  catch { return d; }
}
function v(x) { return x ?? '—'; }

function render(rows) {
  const p = rows[0];

  const histRows = rows.map(r => `
    <tr>
      <td class="td-mono">${v(r.ComplaintName)}</td>
      <td class="td-mono">${v(r.TreatmentName)}</td>
      <td class="td-name">${v(r.PrimaryDoctor)}</td>
      <td>${fmt(r.DateStarted) ?? '—'}</td>
      <td class="${r.DateEnded ? 'td-ended' : 'td-ongoing'}">${r.DateEnded ? fmt(r.DateEnded) : 'Ongoing'}</td>
    </tr>`).join('');

  document.getElementById('form-body').innerHTML = `

    <!-- Row 1: Patient No | Doctor No + Name + Consultant -->
    <div class="fields-row fields-row-2">
      <div class="field-group">
        <div class="field-label">Patient No</div>
        <div class="field-value mono">#${v(p.PatientNo)}</div>
      </div>
      <div class="field-group">
        <div class="field-label">Doctor No</div>
        <div class="field-value mono">${v(p.PrimaryDoctorNo ?? '—')}</div>
      </div>
    </div>

    <!-- Row 2: Ward + Bed | Doctor Name + Consultant -->
    <div class="fields-row fields-row-2">
      <div class="field-group">
        <div class="field-label">Ward</div>
        <div class="field-value">${v(p.WardName)}</div>
      </div>
      <div class="field-group">
        <div class="field-label">Doctor Name</div>
        <div class="field-value">${v(p.PrimaryDoctor)}</div>
      </div>
    </div>

    <!-- Row 3: Patient Name | DOB | Consultant -->
    <div class="fields-row fields-row-3">
      <div class="field-group span-2">
        <div class="field-label">Patient Name</div>
        <div class="field-value">${v(p.PatientName)}</div>
      </div>
      <div class="field-group">
        <div class="field-label">Consultant</div>
        <div class="field-value ${!p.ConsultantSpecialty ? 'empty' : ''}">${p.ConsultantSpecialty ?? 'None assigned'}</div>
      </div>
    </div>

    <!-- Row 4: DOB | Admitted | Bed -->
    <div class="fields-row fields-row-3">
      <div class="field-group no-border-bottom">
        <div class="field-label">Date of Birth</div>
        <div class="field-value">${fmt(p.DateOfBirth) ?? '—'}</div>
      </div>
      <div class="field-group no-border-bottom">
        <div class="field-label">Date Admitted</div>
        <div class="field-value">${fmt(p.DateAdmitted) ?? '—'}</div>
      </div>
      <div class="field-group no-border-bottom">
        <div class="field-label">Bed · Care Unit</div>
        <div class="field-value mono">B-${v(p.BedNo)} &nbsp;·&nbsp; CU-${v(p.CareUnitNo)}</div>
      </div>
    </div>

    <!-- Medical History section -->
    <div class="section-divider">
      <div class="section-divider-line"></div>
      <div class="section-divider-title">Medical History</div>
      <div class="section-divider-line right"></div>
    </div>

    <div class="data-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Complaint</th>
            <th>Treatment</th>
            <th>Doctor</th>
            <th>Date Started</th>
            <th>Date Ended</th>
          </tr>
        </thead>
        <tbody>
          ${histRows || `<tr><td colspan="5" class="td-muted" style="text-align:center;padding:24px">No treatment records found</td></tr>`}
        </tbody>
      </table>
    </div>
  `;
}

// auto load from url param
const urlPno = new URLSearchParams(location.search).get('pno');
if (urlPno) { document.getElementById('pno').value = urlPno; load(); }
</script>
</body>
</html>