-- ivor paine memorial hospital
-- ddl script - milestone 2
-- course: cs204 database systems
-- instructor: dr. ejaz ahmed
-- tool: sql server

-- create database
if exists (select name from sys.databases where name = 'IvorPaineHospital')
begin
    alter database IvorPaineHospital set single_user with rollback immediate;
    drop database IvorPaineHospital;
end
go

create database IvorPaineHospital;
go

use IvorPaineHospital;
go

-- drop tables in reverse dependency order
if object_id('PATIENT_TREATMENT', 'U') is not null drop table PATIENT_TREATMENT;
if object_id('PERFORMANCE', 'U') is not null drop table PERFORMANCE;
if object_id('PREV_EXPERIENCE', 'U') is not null drop table PREV_EXPERIENCE;
if object_id('PATIENT', 'U') is not null drop table PATIENT;
if object_id('BED', 'U') is not null drop table BED;
if object_id('NURSE', 'U') is not null drop table NURSE;
if object_id('CARE_UNIT', 'U') is not null drop table CARE_UNIT;
if object_id('CONSULTANT', 'U') is not null drop table CONSULTANT;
if object_id('DOCTOR', 'U') is not null drop table DOCTOR;
if object_id('COMPLAINT', 'U') is not null drop table COMPLAINT;
if object_id('TREATMENT', 'U') is not null drop table TREATMENT;
if object_id('WARD', 'U') is not null drop table WARD;
if object_id('SPECIALTY', 'U') is not null drop table SPECIALTY;

-- 1. specialty
create table SPECIALTY (
    SpecialtyID int not null,
    SpecialtyName varchar(100) not null,
    constraint PK_SPECIALTY primary key (SpecialtyID),
    constraint UQ_SPECIALTY_NAME unique (SpecialtyName)
);

-- 2. ward
create table WARD (
    WardNo int not null,
    WardName varchar(100) not null,
    SpecialtyID int not null,
    constraint PK_WARD primary key (WardNo),
    constraint FK_WARD_SPECIALTY foreign key (SpecialtyID)
        references SPECIALTY(SpecialtyID)
);

-- 3. care_unit
create table CARE_UNIT (
    CareUnitNo int not null,
    WardNo int not null,
    constraint PK_CARE_UNIT primary key (CareUnitNo),
    constraint FK_CARE_UNIT_WARD foreign key (WardNo)
        references WARD(WardNo)
);

-- 4. nurse
create table NURSE (
    StaffNo int not null,
    FName varchar(50) not null,
    LName varchar(50) not null,
    NurseType varchar(30) not null,
    WardNo int not null,
    CareUnitNo int not null,
    constraint PK_NURSE primary key (StaffNo),
    constraint FK_NURSE_WARD foreign key (WardNo)
        references WARD(WardNo),
    constraint FK_NURSE_CARE_UNIT foreign key (CareUnitNo)
        references CARE_UNIT(CareUnitNo),
    constraint CHK_NURSE_TYPE check (
        NurseType in ('DaySister','NightSister','StaffNurse','NonRegistered')
    )
);

-- 5. doctor
create table DOCTOR (
    StaffNo int not null,
    FName varchar(50) not null,
    LName varchar(50) not null,
    Position varchar(30) not null,
    DateJoinedTeam date not null,
    ConsultantNo int null,
    constraint PK_DOCTOR primary key (StaffNo),
    constraint CHK_DOCTOR_POSITION check (
        Position in ('Student','JuniorHouseman','SeniorHouseman',
                     'AssistantRegistrar','Registrar')
    )
);

-- 6. consultant
create table CONSULTANT (
    StaffNo int not null,
    Specialty varchar(100) not null,
    constraint PK_CONSULTANT primary key (StaffNo),
    constraint FK_CONSULTANT_DOCTOR foreign key (StaffNo)
        references DOCTOR(StaffNo)
);

-- add fk from doctor back to consultant
alter table DOCTOR
    add constraint FK_DOCTOR_CONSULTANT
    foreign key (ConsultantNo) references CONSULTANT(StaffNo);

-- 7. prev_experience
create table PREV_EXPERIENCE (
    StaffNo int not null,
    SNO int not null,
    FromDate date not null,
    ToDate date not null,
    Position varchar(50) not null,
    Establishment varchar(150) not null,
    constraint PK_PREV_EXPERIENCE primary key (StaffNo, SNO),
    constraint FK_PREV_EXP_DOCTOR foreign key (StaffNo)
        references DOCTOR(StaffNo)
);

-- 8. performance
create table PERFORMANCE (
    StaffNo int not null,
    SNO int not null,
    Date date not null,
    Grade varchar(10) not null,
    constraint PK_PERFORMANCE primary key (StaffNo, SNO),
    constraint FK_PERFORMANCE_DOCTOR foreign key (StaffNo)
        references DOCTOR(StaffNo),
    constraint CHK_PERFORMANCE_GRADE check (
        Grade in ('A','B','C','D','F')
    )
);

-- 9. bed
create table BED (
    BedNo int not null,
    BedType varchar(50) not null,
    BedStatus varchar(20) not null,
    WardNo int not null,
    constraint PK_BED primary key (BedNo),
    constraint FK_BED_WARD foreign key (WardNo)
        references WARD(WardNo),
    constraint CHK_BED_STATUS check (
        BedStatus in ('Occupied','Available')
    ),
    constraint CHK_BED_TYPE check (
        BedType in ('Standard','ICU','Maternity','Pediatric','Isolation','Geriatric')
    )
);

-- 10. patient
create table PATIENT (
    PatientNo int not null,
    FName varchar(50) not null,
    LName varchar(50) not null,
    DateOfBirth date not null,
    DateAdmitted date not null,
    WardNo int not null,
    CareUnitNo int not null,
    BedNo int not null,
    PrimaryDoctorNo int not null,
    constraint PK_PATIENT primary key (PatientNo),
    constraint FK_PATIENT_WARD foreign key (WardNo)
        references WARD(WardNo),
    constraint FK_PATIENT_CARE_UNIT foreign key (CareUnitNo)
        references CARE_UNIT(CareUnitNo),
    constraint FK_PATIENT_BED foreign key (BedNo)
        references BED(BedNo),
    constraint FK_PATIENT_DOCTOR foreign key (PrimaryDoctorNo)
        references DOCTOR(StaffNo),
    constraint UQ_PATIENT_BED unique (BedNo)
);

-- 11. complaint
create table COMPLAINT (
    ComplaintCode int not null,
    ComplaintName varchar(150) not null,
    constraint PK_COMPLAINT primary key (ComplaintCode),
    constraint UQ_COMPLAINT_NAME unique (ComplaintName)
);

-- 12. treatment
create table TREATMENT (
    TreatmentCode int not null,
    TreatmentName varchar(150) not null,
    constraint PK_TREATMENT primary key (TreatmentCode),
    constraint UQ_TREATMENT_NAME unique (TreatmentName)
);

-- 13. patient_treatment
create table PATIENT_TREATMENT (
    PatientNo int not null,
    SNO int not null,
    ComplaintCode int not null,
    TreatmentCode int not null,
    StaffNo int not null,
    DateStarted date not null,
    DateEnded date null,
    constraint PK_PATIENT_TREATMENT primary key (PatientNo, SNO),
    constraint FK_PT_PATIENT foreign key (PatientNo)
        references PATIENT(PatientNo),
    constraint FK_PT_COMPLAINT foreign key (ComplaintCode)
        references COMPLAINT(ComplaintCode),
    constraint FK_PT_TREATMENT foreign key (TreatmentCode)
        references TREATMENT(TreatmentCode),
    constraint FK_PT_DOCTOR foreign key (StaffNo)
        references DOCTOR(StaffNo),
    constraint CHK_PT_DATES check (
        DateEnded is null or DateEnded >= DateStarted
    )
);
go