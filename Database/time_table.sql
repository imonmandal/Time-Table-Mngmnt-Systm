-- DON'T USE INTEGERS EXCEPT FOR NOS OF LEC....

create database `time_table`;
drop database `time_table`;
use time_table;

CREATE TABLE `class` (
    ClassName varchar(255)
);

CREATE TABLE `teacher` (
    TeacherName varchar(255)
);

CREATE TABLE `room` (
    RoomNo varchar(255)
);

CREATE TABLE `subject` (
    SubjectName varchar(255)
);

INSERT INTO `class` VALUES
('TE1'),
('TE2'),
('TE3');

INSERT INTO `teacher` VALUES
('teacher1'),
('teacher2'),
('teacher3');

INSERT INTO `room` VALUES
('301'),
('302'),
('303');

INSERT INTO `subject` VALUES
('subject1'),
('subject2');

CREATE TABLE `teacher3` (
    Lecture_No varchar(5),
    Monday varchar(255),
    Tuesday varchar(255),
    Wednesday varchar(255),
    Thursday varchar(255),
    Friday varchar(255),
    Saturday varchar(255)
);

insert into `teacher3` (Lecture_No) values ('1'),('2'),('3'),('4');

create table `week`(days varchar(25));
insert into `week` values ('Monday'), ('Tuesday'), ('Wednesday'), ('Thursday'), ('Friday'), ('Saturday');

create table `lec`(sr_no int, lec int);
insert into `lec` values(1, 4);

truncate table `lec`;
drop table `subject`;

select * from `table`;
