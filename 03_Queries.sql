use IvorPaineHospital
go

-- query 1: consultants and doctors in their team
select
    c.StaffNo as ConsultantID,
    d1.FName + ' ' + d1.LName as ConsultantName,
    c.Specialty,
    d2.StaffNo as DoctorID,
    d2.FName + ' ' + d2.LName as DoctorName,
    d2.Position
from CONSULTANT c
join DOCTOR d1 on c.StaffNo = d1.StaffNo
join DOCTOR d2 on d2.ConsultantNo = c.StaffNo
order by c.StaffNo;

-- query 2: wards with sisters, care units and staff nurses in charge
select
    w.WardNo,
    w.WardName,
    ds.FName + ' ' + ds.LName as DaySister,
    ns.FName + ' ' + ns.LName as NightSister,
    cu.CareUnitNo,
    sn.FName + ' ' + sn.LName as StaffNurseInCharge
from WARD w
left join NURSE ds on ds.WardNo = w.WardNo and ds.NurseType = 'DaySister'
left join NURSE ns on ns.WardNo = w.WardNo and ns.NurseType = 'NightSister'
left join CARE_UNIT cu on cu.WardNo = w.WardNo
left join NURSE sn on sn.CareUnitNo = cu.CareUnitNo and sn.NurseType = 'StaffNurse'
order by w.WardNo, cu.CareUnitNo;

-- query 3: patients with complaints, treatments and dates
select
    p.PatientNo,
    p.FName + ' ' + p.LName as PatientName,
    c.ComplaintName,
    t.TreatmentName,
    pt.DateStarted,
    pt.DateEnded
from PATIENT p
join PATIENT_TREATMENT pt on pt.PatientNo = p.PatientNo
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
order by p.PatientNo;

-- query 4: junior housemen, their patients and staff nurse of care unit
select
    d.StaffNo as DoctorID,
    d.FName + ' ' + d.LName as JuniorHouseman,
    p.PatientNo,
    p.FName + ' ' + p.LName as PatientName,
    n.FName + ' ' + n.LName as StaffNurse,
    n.CareUnitNo
from DOCTOR d
join PATIENT p on p.PrimaryDoctorNo = d.StaffNo
join NURSE n on n.CareUnitNo = p.CareUnitNo and n.NurseType = 'StaffNurse'
where d.Position = 'JuniorHouseman'
order by d.StaffNo;

-- query 5: consultants with a unique specialty
select
    c.StaffNo,
    d.FName + ' ' + d.LName as ConsultantName,
    c.Specialty
from CONSULTANT c
join DOCTOR d on d.StaffNo = c.StaffNo
where c.Specialty in (
    select Specialty
    from CONSULTANT
    group by Specialty
    having count(*) = 1
)
order by c.Specialty;

-- query 6: complaints, treatments and experience history of treating doctor
select
    c.ComplaintName,
    t.TreatmentName,
    d.FName + ' ' + d.LName as DoctorName,
    pe.FromDate,
    pe.ToDate,
    pe.Position as PreviousPosition,
    pe.Establishment
from PATIENT_TREATMENT pt
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
join DOCTOR d on d.StaffNo = pt.StaffNo
join PREV_EXPERIENCE pe on pe.StaffNo = pt.StaffNo
order by c.ComplaintName, t.TreatmentName;

-- query 7: patients with more than one complaint and their treatments
select
    p.PatientNo,
    p.FName + ' ' + p.LName as PatientName,
    c.ComplaintName,
    t.TreatmentName
from PATIENT p
join PATIENT_TREATMENT pt on pt.PatientNo = p.PatientNo
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
where p.PatientNo in (
    select PatientNo
    from PATIENT_TREATMENT
    group by PatientNo
    having count(distinct ComplaintCode) > 1
)
order by p.PatientNo;

-- query 8: patients grouped by treatment within complaint
select
    c.ComplaintName,
    t.TreatmentName,
    p.PatientNo,
    p.FName + ' ' + p.LName as PatientName
from PATIENT_TREATMENT pt
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
join PATIENT p on p.PatientNo = pt.PatientNo
order by c.ComplaintName, t.TreatmentName, p.PatientNo;

-- query 9: performance history for a particular doctor
-- replace 101 with any staffno
select
    d.FName + ' ' + d.LName as DoctorName,
    d.Position,
    pf.SNO,
    pf.Date,
    pf.Grade
from PERFORMANCE pf
join DOCTOR d on d.StaffNo = pf.StaffNo
where pf.StaffNo = 101
order by pf.Date;

-- query 10: full medical details for a particular patient
-- replace 1 with any patientno
select
    p.PatientNo,
    p.FName + ' ' + p.LName as PatientName,
    p.DateOfBirth,
    p.DateAdmitted,
    w.WardName,
    cu.CareUnitNo,
    b.BedNo,
    b.BedType,
    d.FName + ' ' + d.LName as PrimaryDoctor,
    con.Specialty as ConsultantSpecialty,
    c.ComplaintName,
    t.TreatmentName,
    pt.DateStarted,
    pt.DateEnded
from PATIENT p
join WARD w on w.WardNo = p.WardNo
join CARE_UNIT cu on cu.CareUnitNo = p.CareUnitNo
join BED b on b.BedNo = p.BedNo
join DOCTOR d on d.StaffNo = p.PrimaryDoctorNo
left join CONSULTANT con on con.StaffNo = d.ConsultantNo
join PATIENT_TREATMENT pt on pt.PatientNo = p.PatientNo
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
where p.PatientNo = 1;

-- query 11: treatments for a complaint between two dates ordered by treatment
-- replace complaint code and dates as needed
select
    c.ComplaintName,
    t.TreatmentName,
    p.FName + ' ' + p.LName as PatientName,
    pt.DateStarted,
    pt.DateEnded
from PATIENT_TREATMENT pt
join COMPLAINT c on c.ComplaintCode = pt.ComplaintCode
join TREATMENT t on t.TreatmentCode = pt.TreatmentCode
join PATIENT p on p.PatientNo = pt.PatientNo
where pt.ComplaintCode = 2
and pt.DateStarted between '2026-01-01' and '2026-03-31'
order by t.TreatmentName;

-- query 12: positions held by staff and count per position
select Position, count(*) as StaffCount
from DOCTOR
group by Position
union all
select NurseType, count(*)
from NURSE
group by NurseType
order by Position;