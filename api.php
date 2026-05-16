<?php


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/includes/db.php';

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? '';
$id       = $_GET['id']       ?? null;
$body     = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($resource) {
    case 'dashboard':          dashboardStats();                         break;
    case 'query':              runNamedQuery($_GET['name'] ?? '');       break;
    case 'patients':           crudPatients($method, $id, $body);       break;
    case 'doctors':            crudDoctors($method, $id, $body);        break;
    case 'nurses':             crudNurses($method, $id, $body);         break;
    case 'wards':              crudWards($method, $id, $body);          break;
    case 'beds':               crudBeds($method, $id, $body);           break;
    case 'complaints':         crudComplaints($method, $id, $body);     break;
    case 'treatments':         crudTreatments($method, $id, $body);     break;
    case 'patient_treatment':  crudPatientTreatment($method, $id, $body); break;
    case 'performance':        crudPerformance($method, $id, $body);    break;
    case 'prev_experience':    crudPrevExperience($method, $id, $body); break;
    case 'consultants':        crudConsultants($method, $id, $body);    break;
    case 'specialties':        crudSpecialties($method, $id, $body);    break;
    case 'care_units':         crudCareUnits($method, $id, $body);      break;
    default:
        jsonResponse(['error' => 'Unknown resource: ' . $resource], 404);
}

// dashboard
function dashboardStats(): void {
    $stats = [
        'total_patients'    => dbScalar("SELECT COUNT(*) FROM PATIENT"),
        'total_doctors'     => dbScalar("SELECT COUNT(*) FROM DOCTOR"),
        'total_nurses'      => dbScalar("SELECT COUNT(*) FROM NURSE"),
        'total_beds'        => dbScalar("SELECT COUNT(*) FROM BED"),
        'occupied_beds'     => dbScalar("SELECT COUNT(*) FROM BED WHERE BedStatus='Occupied'"),
        'available_beds'    => dbScalar("SELECT COUNT(*) FROM BED WHERE BedStatus='Available'"),
        'total_wards'       => dbScalar("SELECT COUNT(*) FROM WARD"),
        'total_consultants' => dbScalar("SELECT COUNT(*) FROM CONSULTANT"),
        'active_treatments' => dbScalar("SELECT COUNT(*) FROM PATIENT_TREATMENT WHERE DateEnded IS NULL"),

        'recent_admissions' => dbQuery(
            "SELECT TOP 5 p.PatientNo,
                    p.FName + ' ' + p.LName AS PatientName,
                    p.DateAdmitted, w.WardName
             FROM PATIENT p
             JOIN WARD w ON w.WardNo = p.WardNo
             ORDER BY p.DateAdmitted DESC"
        ),

        'bed_usage_by_ward' => dbQuery(
            "SELECT w.WardName,
                    SUM(CASE WHEN b.BedStatus='Occupied'  THEN 1 ELSE 0 END) AS Occupied,
                    SUM(CASE WHEN b.BedStatus='Available' THEN 1 ELSE 0 END) AS Available
             FROM WARD w
             LEFT JOIN BED b ON b.WardNo = w.WardNo
             GROUP BY w.WardName
             ORDER BY w.WardName"
        ),

        'doctor_positions' => dbQuery(
            "SELECT Position, COUNT(*) AS cnt FROM DOCTOR GROUP BY Position"
        ),
    ];

    jsonResponse($stats);
}

// queries

function runNamedQuery(string $name): void {
    $p = $_GET;

    switch ($name) {

        case 'q1_consultant_teams':
            jsonResponse(dbQuery(
                "SELECT c.StaffNo AS ConsultantID,
                        d1.FName + ' ' + d1.LName AS ConsultantName,
                        c.Specialty,
                        d2.StaffNo AS DoctorID,
                        d2.FName + ' ' + d2.LName AS DoctorName,
                        d2.Position
                 FROM CONSULTANT c
                 JOIN DOCTOR d1 ON c.StaffNo = d1.StaffNo
                 JOIN DOCTOR d2 ON d2.ConsultantNo = c.StaffNo
                 ORDER BY c.StaffNo"
            ));

        case 'q2_ward_nursing':
            jsonResponse(dbQuery(
                "SELECT w.WardNo, w.WardName,
                        ds.FName + ' ' + ds.LName AS DaySister,
                        ns.FName + ' ' + ns.LName AS NightSister,
                        cu.CareUnitNo,
                        sn.FName + ' ' + sn.LName AS StaffNurseInCharge
                 FROM WARD w
                 LEFT JOIN NURSE ds ON ds.WardNo = w.WardNo AND ds.NurseType = 'DaySister'
                 LEFT JOIN NURSE ns ON ns.WardNo = w.WardNo AND ns.NurseType = 'NightSister'
                 LEFT JOIN CARE_UNIT cu ON cu.WardNo = w.WardNo
                 LEFT JOIN NURSE sn ON sn.CareUnitNo = cu.CareUnitNo AND sn.NurseType = 'StaffNurse'
                 ORDER BY w.WardNo, cu.CareUnitNo"
            ));

        case 'q3_patient_treatments':
            jsonResponse(dbQuery(
                "SELECT p.PatientNo,
                        p.FName + ' ' + p.LName AS PatientName,
                        c.ComplaintName, t.TreatmentName,
                        pt.DateStarted, pt.DateEnded
                 FROM PATIENT p
                 JOIN PATIENT_TREATMENT pt ON pt.PatientNo = p.PatientNo
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 ORDER BY p.PatientNo"
            ));

        case 'q4_junior_housemen':
            jsonResponse(dbQuery(
                "SELECT d.StaffNo AS DoctorID,
                        d.FName + ' ' + d.LName AS JuniorHouseman,
                        p.PatientNo,
                        p.FName + ' ' + p.LName AS PatientName,
                        n.FName + ' ' + n.LName AS StaffNurse,
                        n.CareUnitNo
                 FROM DOCTOR d
                 JOIN PATIENT p ON p.PrimaryDoctorNo = d.StaffNo
                 JOIN NURSE n ON n.CareUnitNo = p.CareUnitNo AND n.NurseType = 'StaffNurse'
                 WHERE d.Position = 'JuniorHouseman'
                 ORDER BY d.StaffNo"
            ));

        case 'q5_unique_specialty':
            jsonResponse(dbQuery(
                "SELECT c.StaffNo,
                        d.FName + ' ' + d.LName AS ConsultantName,
                        c.Specialty
                 FROM CONSULTANT c
                 JOIN DOCTOR d ON d.StaffNo = c.StaffNo
                 WHERE c.Specialty IN (
                     SELECT Specialty FROM CONSULTANT
                     GROUP BY Specialty HAVING COUNT(*) = 1
                 )
                 ORDER BY c.Specialty"
            ));

        case 'q6_treatment_experience':
            jsonResponse(dbQuery(
                "SELECT c.ComplaintName, t.TreatmentName,
                        d.FName + ' ' + d.LName AS DoctorName,
                        pe.FromDate, pe.ToDate,
                        pe.Position AS PreviousPosition,
                        pe.Establishment
                 FROM PATIENT_TREATMENT pt
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 JOIN DOCTOR d ON d.StaffNo = pt.StaffNo
                 JOIN PREV_EXPERIENCE pe ON pe.StaffNo = pt.StaffNo
                 ORDER BY c.ComplaintName, t.TreatmentName"
            ));

        case 'q7_multi_complaint':
            jsonResponse(dbQuery(
                "SELECT p.PatientNo,
                        p.FName + ' ' + p.LName AS PatientName,
                        c.ComplaintName, t.TreatmentName
                 FROM PATIENT p
                 JOIN PATIENT_TREATMENT pt ON pt.PatientNo = p.PatientNo
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 WHERE p.PatientNo IN (
                     SELECT PatientNo FROM PATIENT_TREATMENT
                     GROUP BY PatientNo
                     HAVING COUNT(DISTINCT ComplaintCode) > 1
                 )
                 ORDER BY p.PatientNo"
            ));

        case 'q8_grouped_by_treatment':
            jsonResponse(dbQuery(
                "SELECT c.ComplaintName, t.TreatmentName,
                        p.PatientNo,
                        p.FName + ' ' + p.LName AS PatientName
                 FROM PATIENT_TREATMENT pt
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 JOIN PATIENT p ON p.PatientNo = pt.PatientNo
                 ORDER BY c.ComplaintName, t.TreatmentName, p.PatientNo"
            ));

        case 'q9_doctor_performance':
            $staffNo = (int)($p['staff_no'] ?? 101);
            jsonResponse(dbQuery(
                "SELECT d.FName + ' ' + d.LName AS DoctorName,
                        d.Position, pf.SNO, pf.Date, pf.Grade
                 FROM PERFORMANCE pf
                 JOIN DOCTOR d ON d.StaffNo = pf.StaffNo
                 WHERE pf.StaffNo = ?
                 ORDER BY pf.Date",
                [$staffNo]
            ));

        case 'q10_patient_detail':
            $patientNo = (int)($p['patient_no'] ?? 1);
            jsonResponse(dbQuery(
                "SELECT p.PatientNo,
                        p.FName + ' ' + p.LName AS PatientName,
                        p.DateOfBirth, p.DateAdmitted,
                        w.WardName, cu.CareUnitNo,
                        b.BedNo, b.BedType,
                        d.FName + ' ' + d.LName AS PrimaryDoctor,
                        con.Specialty AS ConsultantSpecialty,
                        c.ComplaintName, t.TreatmentName,
                        pt.DateStarted, pt.DateEnded
                 FROM PATIENT p
                 JOIN WARD w ON w.WardNo = p.WardNo
                 JOIN CARE_UNIT cu ON cu.CareUnitNo = p.CareUnitNo
                 JOIN BED b ON b.BedNo = p.BedNo
                 JOIN DOCTOR d ON d.StaffNo = p.PrimaryDoctorNo
                 LEFT JOIN CONSULTANT con ON con.StaffNo = d.ConsultantNo
                 JOIN PATIENT_TREATMENT pt ON pt.PatientNo = p.PatientNo
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 WHERE p.PatientNo = ?",
                [$patientNo]
            ));

        case 'q11_treatments_by_date':
            $code     = (int)($p['complaint_code'] ?? 2);
            $dateFrom = $p['date_from'] ?? '2026-01-01';
            $dateTo   = $p['date_to']   ?? '2026-03-31';
            jsonResponse(dbQuery(
                "SELECT c.ComplaintName, t.TreatmentName,
                        p.FName + ' ' + p.LName AS PatientName,
                        pt.DateStarted, pt.DateEnded
                 FROM PATIENT_TREATMENT pt
                 JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                 JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                 JOIN PATIENT p ON p.PatientNo = pt.PatientNo
                 WHERE pt.ComplaintCode = ?
                   AND pt.DateStarted BETWEEN ? AND ?
                 ORDER BY t.TreatmentName",
                [$code, $dateFrom, $dateTo]
            ));

        case 'q12_staff_positions':
            jsonResponse(dbQuery(
                "SELECT Position, COUNT(*) AS StaffCount FROM DOCTOR GROUP BY Position
                 UNION ALL
                 SELECT NurseType, COUNT(*) FROM NURSE GROUP BY NurseType
                 ORDER BY Position"
            ));

        default:
            jsonResponse(['error' => 'Unknown query: ' . $name], 404);
    }
}

// patients
function crudPatients(string $method, ?string $id, array $body): void {
    switch ($method) {

        case 'GET':
            if ($id) {
                $rows = dbQuery(
                    "SELECT p.*, w.WardName,
                            b.BedType, b.BedStatus,
                            d.FName + ' ' + d.LName AS PrimaryDoctorName
                     FROM PATIENT p
                     JOIN WARD w ON w.WardNo = p.WardNo
                     JOIN BED b ON b.BedNo = p.BedNo
                     JOIN DOCTOR d ON d.StaffNo = p.PrimaryDoctorNo
                     WHERE p.PatientNo = ?",
                    [$id]
                );
                jsonResponse($rows[0] ?? null);
            }

            $search = $_GET['search'] ?? '';
            $ward   = $_GET['ward']   ?? '';
            $sql    = "SELECT p.PatientNo, p.FName, p.LName,
                              p.FName + ' ' + p.LName AS FullName,
                              p.DateOfBirth, p.DateAdmitted,
                              w.WardName, p.WardNo, p.BedNo,
                              d.FName + ' ' + d.LName AS PrimaryDoctorName
                       FROM PATIENT p
                       JOIN WARD w ON w.WardNo = p.WardNo
                       JOIN DOCTOR d ON d.StaffNo = p.PrimaryDoctorNo
                       WHERE 1=1";
            $params = [];
            if ($search !== '') {
                $sql .= " AND (p.FName LIKE ? OR p.LName LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($ward !== '') {
                $sql .= " AND p.WardNo = ?";
                $params[] = (int)$ward;
            }
            $sql .= " ORDER BY p.PatientNo";
            jsonResponse(dbQuery($sql, $params));

        case 'POST':
            dbExec(
                "INSERT INTO PATIENT
                 (PatientNo,FName,LName,DateOfBirth,DateAdmitted,WardNo,CareUnitNo,BedNo,PrimaryDoctorNo)
                 VALUES (?,?,?,?,?,?,?,?,?)",
                [
                    (int)$body['PatientNo'],
                    $body['FName'],
                    $body['LName'],
                    $body['DateOfBirth'],
                    $body['DateAdmitted'],
                    (int)$body['WardNo'],
                    (int)$body['CareUnitNo'],
                    (int)$body['BedNo'],
                    (int)$body['PrimaryDoctorNo'],
                ]
            );
            dbExec("UPDATE BED SET BedStatus = 'Occupied' WHERE BedNo = ?", [(int)$body['BedNo']]);
            jsonResponse(['success' => true]);

        case 'PUT':
            dbExec(
                "UPDATE PATIENT
                 SET FName=?, LName=?, DateOfBirth=?, DateAdmitted=?,
                     WardNo=?, CareUnitNo=?, BedNo=?, PrimaryDoctorNo=?
                 WHERE PatientNo=?",
                [
                    $body['FName'], $body['LName'],
                    $body['DateOfBirth'], $body['DateAdmitted'],
                    (int)$body['WardNo'], (int)$body['CareUnitNo'],
                    (int)$body['BedNo'],  (int)$body['PrimaryDoctorNo'],
                    (int)$id,
                ]
            );
            jsonResponse(['success' => true]);

        case 'DELETE':
            // Free the bed first
            dbExec(
                "UPDATE BED SET BedStatus = 'Available'
                 WHERE BedNo = (SELECT BedNo FROM PATIENT WHERE PatientNo = ?)",
                [(int)$id]
            );
            dbExec("DELETE FROM PATIENT_TREATMENT WHERE PatientNo = ?", [(int)$id]);
            dbExec("DELETE FROM PATIENT WHERE PatientNo = ?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// doctors
function crudDoctors(string $method, ?string $id, array $body): void {
    switch ($method) {

        case 'GET':
            $search = $_GET['search']   ?? '';
            $pos    = $_GET['position'] ?? '';
            $sql    = "SELECT d.StaffNo, d.FName, d.LName,
                              d.FName + ' ' + d.LName AS FullName,
                              d.Position, d.DateJoinedTeam, d.ConsultantNo,
                              c.Specialty AS ConsultantSpecialty,
                              cons.FName + ' ' + cons.LName AS ConsultantName
                       FROM DOCTOR d
                       LEFT JOIN CONSULTANT c ON c.StaffNo = d.StaffNo
                       LEFT JOIN DOCTOR cons ON cons.StaffNo = d.ConsultantNo
                       WHERE 1=1";
            $params = [];
            if ($search !== '') {
                $sql .= " AND (d.FName LIKE ? OR d.LName LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($pos !== '') {
                $sql .= " AND d.Position = ?";
                $params[] = $pos;
            }
            if ($id !== null) {
                $sql .= " AND d.StaffNo = ?";
                $params[] = (int)$id;
            }
            $sql .= " ORDER BY d.StaffNo";
            $rows = dbQuery($sql, $params);
            jsonResponse($id ? ($rows[0] ?? null) : $rows);

        case 'POST':
            dbExec(
                "INSERT INTO DOCTOR (StaffNo,FName,LName,Position,DateJoinedTeam,ConsultantNo)
                 VALUES (?,?,?,?,?,?)",
                [
                    (int)$body['StaffNo'],
                    $body['FName'],
                    $body['LName'],
                    $body['Position'],
                    $body['DateJoinedTeam'],
                    $body['ConsultantNo'] ? (int)$body['ConsultantNo'] : null,
                ]
            );
            jsonResponse(['success' => true]);

        case 'PUT':
            dbExec(
                "UPDATE DOCTOR
                 SET FName=?, LName=?, Position=?, DateJoinedTeam=?, ConsultantNo=?
                 WHERE StaffNo=?",
                [
                    $body['FName'], $body['LName'], $body['Position'],
                    $body['DateJoinedTeam'],
                    $body['ConsultantNo'] ? (int)$body['ConsultantNo'] : null,
                    (int)$id,
                ]
            );
            jsonResponse(['success' => true]);

        case 'DELETE':
            dbExec("DELETE FROM PERFORMANCE WHERE StaffNo = ?",     [(int)$id]);
            dbExec("DELETE FROM PREV_EXPERIENCE WHERE StaffNo = ?",  [(int)$id]);
            dbExec("DELETE FROM DOCTOR WHERE StaffNo = ?",           [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

//nurses
function crudNurses(string $method, ?string $id, array $body): void {
    switch ($method) {

        case 'GET':
            $search = $_GET['search'] ?? '';
            $type   = $_GET['type']   ?? '';
            $sql    = "SELECT n.*, w.WardName
                       FROM NURSE n
                       JOIN WARD w ON w.WardNo = n.WardNo
                       WHERE 1=1";
            $params = [];
            if ($search !== '') { $sql .= " AND (n.FName LIKE ? OR n.LName LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
            if ($type   !== '') { $sql .= " AND n.NurseType = ?"; $params[] = $type; }
            if ($id     !== null){ $sql .= " AND n.StaffNo = ?"; $params[] = (int)$id; }
            $sql .= " ORDER BY n.StaffNo";
            $rows = dbQuery($sql, $params);
            jsonResponse($id ? ($rows[0] ?? null) : $rows);

        case 'POST':
            dbExec(
                "INSERT INTO NURSE (StaffNo,FName,LName,NurseType,WardNo,CareUnitNo)
                 VALUES (?,?,?,?,?,?)",
                [(int)$body['StaffNo'], $body['FName'], $body['LName'],
                 $body['NurseType'], (int)$body['WardNo'], (int)$body['CareUnitNo']]
            );
            jsonResponse(['success' => true]);

        case 'PUT':
            dbExec(
                "UPDATE NURSE SET FName=?,LName=?,NurseType=?,WardNo=?,CareUnitNo=? WHERE StaffNo=?",
                [$body['FName'], $body['LName'], $body['NurseType'],
                 (int)$body['WardNo'], (int)$body['CareUnitNo'], (int)$id]
            );
            jsonResponse(['success' => true]);

        case 'DELETE':
            dbExec("DELETE FROM NURSE WHERE StaffNo = ?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// wards
function crudWards(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            jsonResponse(dbQuery(
                "SELECT w.*, s.SpecialtyName,
                        COUNT(DISTINCT b.BedNo)    AS TotalBeds,
                        COUNT(DISTINCT cu.CareUnitNo) AS TotalCareUnits
                 FROM WARD w
                 JOIN SPECIALTY s ON s.SpecialtyID = w.SpecialtyID
                 LEFT JOIN BED b ON b.WardNo = w.WardNo
                 LEFT JOIN CARE_UNIT cu ON cu.WardNo = w.WardNo
                 GROUP BY w.WardNo, w.WardName, w.SpecialtyID, s.SpecialtyName
                 ORDER BY w.WardNo"
            ));
        case 'POST':
            dbExec("INSERT INTO WARD (WardNo,WardName,SpecialtyID) VALUES (?,?,?)",
                   [(int)$body['WardNo'], $body['WardName'], (int)$body['SpecialtyID']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE WARD SET WardName=?,SpecialtyID=? WHERE WardNo=?",
                   [$body['WardName'], (int)$body['SpecialtyID'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM WARD WHERE WardNo=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// beds
function crudBeds(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            $status = $_GET['status'] ?? '';
            $ward   = $_GET['ward']   ?? '';
            $sql    = "SELECT b.*, w.WardName FROM BED b JOIN WARD w ON w.WardNo=b.WardNo WHERE 1=1";
            $params = [];
            if ($status !== '') { $sql .= " AND b.BedStatus=?"; $params[] = $status; }
            if ($ward   !== '') { $sql .= " AND b.WardNo=?";    $params[] = (int)$ward; }
            $sql .= " ORDER BY b.BedNo";
            jsonResponse(dbQuery($sql, $params));
        case 'POST':
            dbExec("INSERT INTO BED (BedNo,BedType,BedStatus,WardNo) VALUES (?,?,?,?)",
                   [(int)$body['BedNo'], $body['BedType'], $body['BedStatus'], (int)$body['WardNo']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE BED SET BedType=?,BedStatus=?,WardNo=? WHERE BedNo=?",
                   [$body['BedType'], $body['BedStatus'], (int)$body['WardNo'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM BED WHERE BedNo=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// complaints
function crudComplaints(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            jsonResponse(dbQuery("SELECT * FROM COMPLAINT ORDER BY ComplaintCode"));
        case 'POST':
            dbExec("INSERT INTO COMPLAINT (ComplaintCode,ComplaintName) VALUES (?,?)",
                   [(int)$body['ComplaintCode'], $body['ComplaintName']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE COMPLAINT SET ComplaintName=? WHERE ComplaintCode=?",
                   [$body['ComplaintName'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM COMPLAINT WHERE ComplaintCode=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// treatments
function crudTreatments(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            jsonResponse(dbQuery("SELECT * FROM TREATMENT ORDER BY TreatmentCode"));
        case 'POST':
            dbExec("INSERT INTO TREATMENT (TreatmentCode,TreatmentName) VALUES (?,?)",
                   [(int)$body['TreatmentCode'], $body['TreatmentName']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE TREATMENT SET TreatmentName=? WHERE TreatmentCode=?",
                   [$body['TreatmentName'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM TREATMENT WHERE TreatmentCode=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// patient treatment
function crudPatientTreatment(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            $pno    = $_GET['patient_no'] ?? '';
            $sql    = "SELECT pt.*,
                              p.FName + ' ' + p.LName AS PatientName,
                              c.ComplaintName,
                              t.TreatmentName,
                              d.FName + ' ' + d.LName AS DoctorName
                       FROM PATIENT_TREATMENT pt
                       JOIN PATIENT   p ON p.PatientNo    = pt.PatientNo
                       JOIN COMPLAINT c ON c.ComplaintCode = pt.ComplaintCode
                       JOIN TREATMENT t ON t.TreatmentCode = pt.TreatmentCode
                       JOIN DOCTOR    d ON d.StaffNo        = pt.StaffNo
                       WHERE 1=1";
            $params = [];
            if ($pno !== '') { $sql .= " AND pt.PatientNo=?"; $params[] = (int)$pno; }
            $sql .= " ORDER BY pt.PatientNo, pt.SNO";
            jsonResponse(dbQuery($sql, $params));

        case 'POST':
            dbExec(
                "INSERT INTO PATIENT_TREATMENT
                 (PatientNo,SNO,ComplaintCode,TreatmentCode,StaffNo,DateStarted,DateEnded)
                 VALUES (?,?,?,?,?,?,?)",
                [
                    (int)$body['PatientNo'], (int)$body['SNO'],
                    (int)$body['ComplaintCode'], (int)$body['TreatmentCode'],
                    (int)$body['StaffNo'],
                    $body['DateStarted'],
                    $body['DateEnded'] ?: null,
                ]
            );
            jsonResponse(['success' => true]);

        case 'PUT':
            dbExec(
                "UPDATE PATIENT_TREATMENT SET DateEnded=? WHERE PatientNo=? AND SNO=?",
                [$body['DateEnded'], (int)$body['PatientNo'], (int)$body['SNO']]
            );
            jsonResponse(['success' => true]);

        case 'DELETE':
            [$pno, $sno] = explode('-', $id);
            dbExec("DELETE FROM PATIENT_TREATMENT WHERE PatientNo=? AND SNO=?",
                   [(int)$pno, (int)$sno]);
            jsonResponse(['success' => true]);
    }
}

// performance
function crudPerformance(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            $staffNo = $_GET['staff_no'] ?? '';
            $sql     = "SELECT pf.*, d.FName + ' ' + d.LName AS DoctorName
                        FROM PERFORMANCE pf
                        JOIN DOCTOR d ON d.StaffNo = pf.StaffNo
                        WHERE 1=1";
            $params  = [];
            if ($staffNo !== '') { $sql .= " AND pf.StaffNo=?"; $params[] = (int)$staffNo; }
            $sql .= " ORDER BY pf.StaffNo, pf.Date";
            jsonResponse(dbQuery($sql, $params));
        case 'POST':
            dbExec("INSERT INTO PERFORMANCE (StaffNo,SNO,Date,Grade) VALUES (?,?,?,?)",
                   [(int)$body['StaffNo'], (int)$body['SNO'], $body['Date'], $body['Grade']]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            [$s, $n] = explode('-', $id);
            dbExec("DELETE FROM PERFORMANCE WHERE StaffNo=? AND SNO=?", [(int)$s, (int)$n]);
            jsonResponse(['success' => true]);
    }
}

// previous experience 
function crudPrevExperience(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            $staffNo = $_GET['staff_no'] ?? '';
            $sql     = "SELECT pe.*, d.FName + ' ' + d.LName AS DoctorName
                        FROM PREV_EXPERIENCE pe
                        JOIN DOCTOR d ON d.StaffNo = pe.StaffNo
                        WHERE 1=1";
            $params  = [];
            if ($staffNo !== '') { $sql .= " AND pe.StaffNo=?"; $params[] = (int)$staffNo; }
            $sql .= " ORDER BY pe.StaffNo, pe.SNO";
            jsonResponse(dbQuery($sql, $params));
        case 'POST':
            dbExec(
                "INSERT INTO PREV_EXPERIENCE (StaffNo,SNO,FromDate,ToDate,Position,Establishment)
                 VALUES (?,?,?,?,?,?)",
                [(int)$body['StaffNo'], (int)$body['SNO'],
                 $body['FromDate'], $body['ToDate'],
                 $body['Position'], $body['Establishment']]
            );
            jsonResponse(['success' => true]);
        case 'DELETE':
            [$s, $n] = explode('-', $id);
            dbExec("DELETE FROM PREV_EXPERIENCE WHERE StaffNo=? AND SNO=?", [(int)$s, (int)$n]);
            jsonResponse(['success' => true]);
    }
}

// consultants
function crudConsultants(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            jsonResponse(dbQuery(
                "SELECT c.StaffNo,
                        d.FName + ' ' + d.LName AS ConsultantName,
                        c.Specialty, d.Position, d.DateJoinedTeam,
                        COUNT(t.StaffNo) AS TeamSize
                 FROM CONSULTANT c
                 JOIN DOCTOR d ON d.StaffNo = c.StaffNo
                 LEFT JOIN DOCTOR t ON t.ConsultantNo = c.StaffNo
                 GROUP BY c.StaffNo, d.FName, d.LName, c.Specialty, d.Position, d.DateJoinedTeam
                 ORDER BY c.StaffNo"
            ));
        case 'POST':
            dbExec("INSERT INTO CONSULTANT (StaffNo,Specialty) VALUES (?,?)",
                   [(int)$body['StaffNo'], $body['Specialty']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE CONSULTANT SET Specialty=? WHERE StaffNo=?",
                   [$body['Specialty'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM CONSULTANT WHERE StaffNo=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

// specialists
function crudSpecialties(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            jsonResponse(dbQuery("SELECT * FROM SPECIALTY ORDER BY SpecialtyID"));
        case 'POST':
            dbExec("INSERT INTO SPECIALTY (SpecialtyID,SpecialtyName) VALUES (?,?)",
                   [(int)$body['SpecialtyID'], $body['SpecialtyName']]);
            jsonResponse(['success' => true]);
        case 'PUT':
            dbExec("UPDATE SPECIALTY SET SpecialtyName=? WHERE SpecialtyID=?",
                   [$body['SpecialtyName'], (int)$id]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM SPECIALTY WHERE SpecialtyID=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}

//careunits 
function crudCareUnits(string $method, ?string $id, array $body): void {
    switch ($method) {
        case 'GET':
            $ward   = $_GET['ward'] ?? '';
            $sql    = "SELECT cu.*, w.WardName FROM CARE_UNIT cu JOIN WARD w ON w.WardNo=cu.WardNo WHERE 1=1";
            $params = [];
            if ($ward !== '') { $sql .= " AND cu.WardNo=?"; $params[] = (int)$ward; }
            $sql .= " ORDER BY cu.CareUnitNo";
            jsonResponse(dbQuery($sql, $params));
        case 'POST':
            dbExec("INSERT INTO CARE_UNIT (CareUnitNo,WardNo) VALUES (?,?)",
                   [(int)$body['CareUnitNo'], (int)$body['WardNo']]);
            jsonResponse(['success' => true]);
        case 'DELETE':
            dbExec("DELETE FROM CARE_UNIT WHERE CareUnitNo=?", [(int)$id]);
            jsonResponse(['success' => true]);
    }
}
?>
