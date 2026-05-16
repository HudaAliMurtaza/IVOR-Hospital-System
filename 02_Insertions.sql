use IvorPaineHospital;
go

-- 1. specialty (10 records)
insert into SPECIALTY values (1, 'Orthopedics');
insert into SPECIALTY values (2, 'Geriatrics');
insert into SPECIALTY values (3, 'Cardiology');
insert into SPECIALTY values (4, 'Neurology');
insert into SPECIALTY values (5, 'Pediatrics');
insert into SPECIALTY values (6, 'Oncology');
insert into SPECIALTY values (7, 'Dermatology');
insert into SPECIALTY values (8, 'Gynecology');
insert into SPECIALTY values (9, 'Pulmonology');
insert into SPECIALTY values (10, 'Gastroenterology');
go

-- 2. ward (10 records)
insert into WARD values (1, 'Orthopedics Ward', 1);
insert into WARD values (2, 'Geriatrics Ward', 2);
insert into WARD values (3, 'Cardiology Ward', 3);
insert into WARD values (4, 'Neurology Ward', 4);
insert into WARD values (5, 'Pediatrics Ward', 5);
insert into WARD values (6, 'Oncology Ward', 6);
insert into WARD values (7, 'Dermatology Ward', 7);
insert into WARD values (8, 'Gynecology Ward', 8);
insert into WARD values (9, 'Pulmonology Ward', 9);
insert into WARD values (10, 'Gastroenterology Ward', 10);
go

-- 3. care_unit (20 records)
insert into CARE_UNIT values (1, 1);
insert into CARE_UNIT values (2, 1);
insert into CARE_UNIT values (3, 2);
insert into CARE_UNIT values (4, 2);
insert into CARE_UNIT values (5, 3);
insert into CARE_UNIT values (6, 3);
insert into CARE_UNIT values (7, 4);
insert into CARE_UNIT values (8, 4);
insert into CARE_UNIT values (9, 5);
insert into CARE_UNIT values (10, 5);
insert into CARE_UNIT values (11, 6);
insert into CARE_UNIT values (12, 6);
insert into CARE_UNIT values (13, 7);
insert into CARE_UNIT values (14, 7);
insert into CARE_UNIT values (15, 8);
insert into CARE_UNIT values (16, 8);
insert into CARE_UNIT values (17, 9);
insert into CARE_UNIT values (18, 9);
insert into CARE_UNIT values (19, 10);
insert into CARE_UNIT values (20, 10);
go

-- 4. doctor (13 records)
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (101, 'Ahmed', 'Khan', 'Registrar', '2018-03-15', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (102, 'Sara', 'Malik', 'SeniorHouseman', '2020-06-01', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (103, 'Umar', 'Sheikh', 'JuniorHouseman', '2022-01-10', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (104, 'Fatima', 'Akhtar', 'AssistantRegistrar', '2021-09-05', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (105, 'Bilal', 'Hussain', 'Registrar', '2017-11-20', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (106, 'Zara', 'Qureshi', 'JuniorHouseman', '2023-02-14', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (107, 'Hassan', 'Raza', 'SeniorHouseman', '2019-07-22', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (108, 'Nida', 'Farooq', 'Student', '2024-01-01', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (109, 'Tariq', 'Siddiqui', 'Registrar', '2016-05-30', null);
insert into DOCTOR (StaffNo, FName, LName, Position, DateJoinedTeam, ConsultantNo)
values (110, 'Amna', 'Baig', 'AssistantRegistrar', '2020-12-01', null);
insert into DOCTOR values (112, 'Kamil', 'Dar', 'Registrar', '2019-03-01', null);
insert into DOCTOR values (113, 'Sana', 'Iqbal', 'JuniorHouseman', '2024-02-01', null);
insert into DOCTOR values (114, 'Rehan', 'Mirza', 'Student', '2024-03-01', null);
go

-- 5. consultant (5 records)
insert into CONSULTANT values (101, 'Orthopedics');
insert into CONSULTANT values (105, 'Cardiology');
insert into CONSULTANT values (109, 'Neurology');
insert into CONSULTANT values (107, 'Geriatrics');
insert into CONSULTANT values (112, 'Cardiology');
go

-- 6. update doctor consultantno
update DOCTOR set ConsultantNo = 101 where StaffNo = 102;
update DOCTOR set ConsultantNo = 101 where StaffNo = 103;
update DOCTOR set ConsultantNo = 105 where StaffNo = 104;
update DOCTOR set ConsultantNo = 105 where StaffNo = 106;
update DOCTOR set ConsultantNo = 109 where StaffNo = 108;
update DOCTOR set ConsultantNo = 109 where StaffNo = 110;
update DOCTOR set ConsultantNo = 107 where StaffNo = 113;
update DOCTOR set ConsultantNo = 112 where StaffNo = 114;
go

-- 7. prev_experience (15 records)
insert into PREV_EXPERIENCE values (101, 1, '2012-01-01', '2015-06-30', 'JuniorHouseman', 'City Hospital Lahore');
insert into PREV_EXPERIENCE values (101, 2, '2015-07-01', '2018-02-28', 'SeniorHouseman', 'General Hospital Karachi');
insert into PREV_EXPERIENCE values (102, 1, '2017-03-01', '2020-05-31', 'JuniorHouseman', 'Shaukat Khanum Hospital');
insert into PREV_EXPERIENCE values (103, 1, '2019-06-01', '2021-12-31', 'Student', 'PIMS Hospital Islamabad');
insert into PREV_EXPERIENCE values (104, 1, '2018-01-01', '2021-08-31', 'JuniorHouseman', 'Aga Khan Hospital');
insert into PREV_EXPERIENCE values (105, 1, '2010-05-01', '2013-04-30', 'JuniorHouseman', 'Mayo Hospital Lahore');
insert into PREV_EXPERIENCE values (105, 2, '2013-05-01', '2017-10-31', 'SeniorHouseman', 'Services Hospital Lahore');
insert into PREV_EXPERIENCE values (106, 1, '2020-07-01', '2022-12-31', 'Student', 'Holy Family Hospital');
insert into PREV_EXPERIENCE values (107, 1, '2014-02-01', '2017-01-31', 'JuniorHouseman', 'Jinnah Hospital Lahore');
insert into PREV_EXPERIENCE values (107, 2, '2017-02-01', '2019-06-30', 'SeniorHouseman', 'Liaquat Hospital');
insert into PREV_EXPERIENCE values (108, 1, '2022-06-01', '2023-12-31', 'Student', 'PIMS Hospital Islamabad');
insert into PREV_EXPERIENCE values (109, 1, '2008-01-01', '2012-06-30', 'JuniorHouseman', 'Nishtar Hospital');
insert into PREV_EXPERIENCE values (109, 2, '2012-07-01', '2016-04-30', 'SeniorHouseman', 'Combined Military Hospital');
insert into PREV_EXPERIENCE values (110, 1, '2016-03-01', '2018-11-30', 'JuniorHouseman', 'Benazir Bhutto Hospital');
insert into PREV_EXPERIENCE values (110, 2, '2018-12-01', '2020-11-30', 'AssistantRegistrar', 'Federal Government Hospital');
go

-- 8. performance (20 records)
insert into PERFORMANCE values (101, 1, '2019-06-30', 'A');
insert into PERFORMANCE values (101, 2, '2019-12-31', 'A');
insert into PERFORMANCE values (102, 1, '2021-06-30', 'B');
insert into PERFORMANCE values (102, 2, '2021-12-31', 'A');
insert into PERFORMANCE values (103, 1, '2022-06-30', 'B');
insert into PERFORMANCE values (103, 2, '2022-12-31', 'C');
insert into PERFORMANCE values (104, 1, '2022-06-30', 'A');
insert into PERFORMANCE values (104, 2, '2022-12-31', 'B');
insert into PERFORMANCE values (105, 1, '2018-06-30', 'A');
insert into PERFORMANCE values (105, 2, '2018-12-31', 'A');
insert into PERFORMANCE values (106, 1, '2023-06-30', 'C');
insert into PERFORMANCE values (106, 2, '2023-12-31', 'B');
insert into PERFORMANCE values (107, 1, '2020-06-30', 'B');
insert into PERFORMANCE values (107, 2, '2020-12-31', 'A');
insert into PERFORMANCE values (108, 1, '2024-06-30', 'C');
insert into PERFORMANCE values (109, 1, '2017-06-30', 'A');
insert into PERFORMANCE values (109, 2, '2017-12-31', 'A');
insert into PERFORMANCE values (110, 1, '2021-06-30', 'B');
insert into PERFORMANCE values (110, 2, '2021-12-31', 'B');
insert into PERFORMANCE values (103, 3, '2023-06-30', 'B');
go

-- 9. nurse (19 records)
insert into NURSE values (201, 'Hina', 'Bashir', 'DaySister', 1, 1);
insert into NURSE values (202, 'Rukhsar', 'Naz', 'NightSister', 1, 2);
insert into NURSE values (203, 'Maria', 'Yousuf', 'StaffNurse', 1, 1);
insert into NURSE values (204, 'Sana', 'Iqbal', 'StaffNurse', 1, 2);
insert into NURSE values (205, 'Ayesha', 'Tariq', 'NonRegistered', 1, 1);
insert into NURSE values (206, 'Rabia', 'Anwar', 'DaySister', 2, 3);
insert into NURSE values (207, 'Saima', 'Pervaiz', 'NightSister', 2, 4);
insert into NURSE values (208, 'Uzma', 'Ghani', 'StaffNurse', 2, 3);
insert into NURSE values (209, 'Nadia', 'Rehman', 'StaffNurse', 2, 4);
insert into NURSE values (210, 'Fozia', 'Aslam', 'NonRegistered', 2, 3);
insert into NURSE values (211, 'Asma', 'Shaheen', 'DaySister', 3, 5);
insert into NURSE values (212, 'Lubna', 'Nawaz', 'NightSister', 3, 6);
insert into NURSE values (213, 'Shamim', 'Bibi', 'StaffNurse', 3, 5);
insert into NURSE values (214, 'Kiran', 'Saleem', 'StaffNurse', 4, 7);
insert into NURSE values (215, 'Sumera', 'Zafar', 'NonRegistered', 4, 8);
insert into NURSE values (216, 'Tahira', 'Malik', 'DaySister', 4, 7);
insert into NURSE values (217, 'Zubeda', 'Rani', 'NightSister', 4, 8);
insert into NURSE values (218, 'Amina', 'Bibi', 'DaySister', 5, 9);
insert into NURSE values (219, 'Farida', 'Begum', 'NightSister', 5, 10);
go

-- 10. bed (35 records)
insert into BED values (1, 'Standard', 'Occupied', 1);
insert into BED values (2, 'Standard', 'Occupied', 1);
insert into BED values (3, 'Standard', 'Occupied', 1);
insert into BED values (4, 'ICU', 'Occupied', 1);
insert into BED values (5, 'Standard', 'Available', 1);
insert into BED values (6, 'Standard', 'Occupied', 2);
insert into BED values (7, 'Standard', 'Occupied', 2);
insert into BED values (8, 'Standard', 'Occupied', 2);
insert into BED values (9, 'Geriatric', 'Occupied', 2);
insert into BED values (10, 'Standard', 'Available', 2);
insert into BED values (11, 'Standard', 'Occupied', 3);
insert into BED values (12, 'ICU', 'Occupied', 3);
insert into BED values (13, 'Standard', 'Occupied', 3);
insert into BED values (14, 'Standard', 'Available', 3);
insert into BED values (15, 'Standard', 'Occupied', 4);
insert into BED values (16, 'Standard', 'Occupied', 4);
insert into BED values (17, 'Standard', 'Occupied', 4);
insert into BED values (18, 'Standard', 'Available', 4);
insert into BED values (19, 'Pediatric', 'Occupied', 5);
insert into BED values (20, 'Pediatric', 'Occupied', 5);
insert into BED values (21, 'Pediatric', 'Available', 5);
insert into BED values (22, 'Standard', 'Occupied', 6);
insert into BED values (23, 'Standard', 'Occupied', 6);
insert into BED values (24, 'Isolation', 'Occupied', 6);
insert into BED values (25, 'Standard', 'Available', 6);
insert into BED values (26, 'Standard', 'Occupied', 7);
insert into BED values (27, 'Standard', 'Available', 7);
insert into BED values (28, 'Maternity', 'Occupied', 8);
insert into BED values (29, 'Maternity', 'Occupied', 8);
insert into BED values (30, 'Maternity', 'Available', 8);
insert into BED values (31, 'Standard', 'Occupied', 9);
insert into BED values (32, 'Standard', 'Occupied', 9);
insert into BED values (33, 'Standard', 'Available', 9);
insert into BED values (34, 'Standard', 'Occupied', 10);
insert into BED values (35, 'Standard', 'Available', 10);
go

-- 11. patient (30 records)
insert into PATIENT values (1, 'Ali', 'Raza', '1985-04-12', '2026-01-05', 1, 1, 1, 102);
insert into PATIENT values (2, 'Bushra', 'Noor', '1972-08-23', '2026-01-10', 1, 1, 2, 102);
insert into PATIENT values (3, 'Kamran', 'Shah', '1990-11-30', '2026-01-15', 1, 2, 3, 103);
insert into PATIENT values (4, 'Shazia', 'Bibi', '1968-02-14', '2026-01-18', 1, 2, 4, 103);
insert into PATIENT values (5, 'Tariq', 'Mehmood', '1955-06-05', '2026-01-20', 2, 3, 6, 102);
insert into PATIENT values (6, 'Naseem', 'Akhtar', '1948-09-17', '2026-01-22', 2, 3, 7, 102);
insert into PATIENT values (7, 'Farhan', 'Qureshi', '1980-03-28', '2026-01-25', 2, 4, 8, 103);
insert into PATIENT values (8, 'Lubna', 'Khatoon', '1963-12-01', '2026-01-28', 2, 4, 9, 103);
insert into PATIENT values (9, 'Imran', 'Butt', '1975-07-19', '2026-02-01', 3, 5, 11, 104);
insert into PATIENT values (10, 'Zainab', 'Mirza', '1982-05-10', '2026-02-03', 3, 5, 12, 104);
insert into PATIENT values (11, 'Jawad', 'Qazi', '1993-01-22', '2026-02-05', 3, 6, 13, 106);
insert into PATIENT values (12, 'Rukhsar', 'Bano', '1970-10-08', '2026-02-07', 3, 6, 15, 106);
insert into PATIENT values (13, 'Danish', 'Siddiqui', '1988-06-14', '2026-02-10', 4, 7, 16, 108);
insert into PATIENT values (14, 'Munaza', 'Parveen', '1977-04-25', '2026-02-12', 4, 7, 17, 108);
insert into PATIENT values (15, 'Waqar', 'Ahmed', '1965-09-03', '2026-02-14', 4, 8, 18, 110);
insert into PATIENT values (16, 'Sobia', 'Riaz', '1995-12-20', '2026-02-16', 5, 9, 19, 104);
insert into PATIENT values (17, 'Haris', 'Javed', '2010-03-11', '2026-02-18', 5, 9, 20, 104);
insert into PATIENT values (18, 'Mahnoor', 'Asif', '2015-07-07', '2026-02-20', 5, 10, 22, 106);
insert into PATIENT values (19, 'Usman', 'Ghani', '1983-11-16', '2026-02-22', 6, 11, 23, 102);
insert into PATIENT values (20, 'Fariha', 'Habib', '1978-08-29', '2026-02-24', 6, 11, 24, 102);
insert into PATIENT values (21, 'Saad', 'Nawaz', '1991-02-05', '2026-02-26', 6, 12, 26, 103);
insert into PATIENT values (22, 'Gulshan', 'Begum', '1960-06-18', '2026-03-01', 7, 13, 27, 103);
insert into PATIENT values (23, 'Rida', 'Fatima', '1988-10-23', '2026-03-03', 8, 15, 28, 104);
insert into PATIENT values (24, 'Maryam', 'Saleem', '1993-04-17', '2026-03-05', 8, 15, 29, 106);
insert into PATIENT values (25, 'Khalid', 'Mahmood', '1957-01-30', '2026-03-07', 9, 17, 31, 108);
insert into PATIENT values (26, 'Nazia', 'Ilyas', '1974-07-12', '2026-03-09', 9, 17, 32, 108);
insert into PATIENT values (27, 'Shahid', 'Iqbal', '1969-11-04', '2026-03-11', 10, 19, 34, 110);
insert into PATIENT values (28, 'Samina', 'Waheed', '1985-03-26', '2026-03-13', 1, 1, 21, 102);
insert into PATIENT values (29, 'Adnan', 'Bashir', '1998-09-08', '2026-03-15', 2, 3, 10, 103);
insert into PATIENT values (30, 'Hina', 'Zubair', '1971-05-21', '2026-03-17', 3, 5, 14, 104);
go

-- 12. complaint (12 records)
insert into COMPLAINT values (1, 'Fracture');
insert into COMPLAINT values (2, 'Hypertension');
insert into COMPLAINT values (3, 'Diabetes');
insert into COMPLAINT values (4, 'Chest Pain');
insert into COMPLAINT values (5, 'Migraine');
insert into COMPLAINT values (6, 'Pneumonia');
insert into COMPLAINT values (7, 'Appendicitis');
insert into COMPLAINT values (8, 'Arthritis');
insert into COMPLAINT values (9, 'Asthma');
insert into COMPLAINT values (10, 'Gastritis');
insert into COMPLAINT values (11, 'Anemia');
insert into COMPLAINT values (12, 'Kidney Infection');
go

-- 13. treatment (12 records)
insert into TREATMENT values (1, 'Casting and Immobilization');
insert into TREATMENT values (2, 'Antihypertensive Medication');
insert into TREATMENT values (3, 'Insulin Therapy');
insert into TREATMENT values (4, 'ECG and Cardiac Monitoring');
insert into TREATMENT values (5, 'Pain Relief Medication');
insert into TREATMENT values (6, 'Antibiotics Course');
insert into TREATMENT values (7, 'Surgical Removal');
insert into TREATMENT values (8, 'Physiotherapy');
insert into TREATMENT values (9, 'Bronchodilator Therapy');
insert into TREATMENT values (10, 'Antacid Therapy');
insert into TREATMENT values (11, 'Iron Supplementation');
insert into TREATMENT values (12, 'IV Antibiotics');
go

-- 14. patient_treatment (42 records)
insert into PATIENT_TREATMENT values (1, 1, 1, 1, 102, '2026-01-05', '2026-01-25');
insert into PATIENT_TREATMENT values (1, 2, 8, 8, 102, '2026-01-10', null);
insert into PATIENT_TREATMENT values (2, 1, 2, 2, 102, '2026-01-10', '2026-02-10');
insert into PATIENT_TREATMENT values (2, 2, 11, 11, 103, '2026-01-15', null);
insert into PATIENT_TREATMENT values (3, 1, 1, 8, 103, '2026-01-15', '2026-02-15');
insert into PATIENT_TREATMENT values (3, 2, 3, 3, 102, '2026-01-20', null);
insert into PATIENT_TREATMENT values (4, 1, 2, 2, 103, '2026-01-18', '2026-02-18');
insert into PATIENT_TREATMENT values (4, 2, 5, 5, 102, '2026-01-22', null);
insert into PATIENT_TREATMENT values (5, 1, 2, 2, 102, '2026-01-20', '2026-02-20');
insert into PATIENT_TREATMENT values (5, 2, 4, 4, 104, '2026-01-25', null);
insert into PATIENT_TREATMENT values (6, 1, 2, 2, 102, '2026-01-22', null);
insert into PATIENT_TREATMENT values (7, 1, 5, 5, 103, '2026-01-25', '2026-02-10');
insert into PATIENT_TREATMENT values (7, 2, 8, 8, 103, '2026-02-01', null);
insert into PATIENT_TREATMENT values (8, 1, 8, 8, 103, '2026-01-28', null);
insert into PATIENT_TREATMENT values (9, 1, 4, 4, 104, '2026-02-01', '2026-02-20');
insert into PATIENT_TREATMENT values (9, 2, 2, 2, 106, '2026-02-05', null);
insert into PATIENT_TREATMENT values (10, 1, 4, 4, 104, '2026-02-03', null);
insert into PATIENT_TREATMENT values (10, 2, 3, 3, 106, '2026-02-08', null);
insert into PATIENT_TREATMENT values (11, 1, 9, 9, 106, '2026-02-05', '2026-02-25');
insert into PATIENT_TREATMENT values (11, 2, 6, 6, 106, '2026-02-10', null);
insert into PATIENT_TREATMENT values (12, 1, 2, 2, 106, '2026-02-07', null);
insert into PATIENT_TREATMENT values (13, 1, 5, 5, 108, '2026-02-10', '2026-03-01');
insert into PATIENT_TREATMENT values (13, 2, 12, 12, 108, '2026-02-15', null);
insert into PATIENT_TREATMENT values (14, 1, 5, 5, 108, '2026-02-12', null);
insert into PATIENT_TREATMENT values (15, 1, 8, 8, 110, '2026-02-14', null);
insert into PATIENT_TREATMENT values (15, 2, 2, 2, 110, '2026-02-18', null);
insert into PATIENT_TREATMENT values (16, 1, 3, 3, 104, '2026-02-16', null);
insert into PATIENT_TREATMENT values (17, 1, 9, 9, 104, '2026-02-18', '2026-03-10');
insert into PATIENT_TREATMENT values (18, 1, 9, 9, 106, '2026-02-20', null);
insert into PATIENT_TREATMENT values (19, 1, 7, 7, 102, '2026-02-22', '2026-03-01');
insert into PATIENT_TREATMENT values (19, 2, 10, 10, 102, '2026-02-25', null);
insert into PATIENT_TREATMENT values (20, 1, 6, 6, 102, '2026-02-24', '2026-03-15');
insert into PATIENT_TREATMENT values (21, 1, 1, 1, 103, '2026-02-26', null);
insert into PATIENT_TREATMENT values (22, 1, 8, 8, 103, '2026-03-01', null);
insert into PATIENT_TREATMENT values (23, 1, 2, 2, 104, '2026-03-03', null);
insert into PATIENT_TREATMENT values (24, 1, 11, 11, 106, '2026-03-05', null);
insert into PATIENT_TREATMENT values (25, 1, 9, 9, 108, '2026-03-07', null);
insert into PATIENT_TREATMENT values (26, 1, 3, 3, 108, '2026-03-09', null);
insert into PATIENT_TREATMENT values (27, 1, 10, 10, 110, '2026-03-11', null);
insert into PATIENT_TREATMENT values (28, 1, 1, 8, 102, '2026-03-13', null);
insert into PATIENT_TREATMENT values (29, 1, 6, 6, 103, '2026-03-15', null);
insert into PATIENT_TREATMENT values (30, 1, 4, 4, 104, '2026-03-17', null);
go