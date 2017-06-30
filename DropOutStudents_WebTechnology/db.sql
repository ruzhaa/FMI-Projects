create table students(
	id bigint auto_increment PRIMARY KEY, 
	name varchar(256), 
	fk_number integer, 
	creation_date timestamp default now()
);

alter table students add unique unique_student (fk_number);

create table subjects(
	id bigint auto_increment PRIMARY KEY, 
	title varchar(256)
);

create table categories(
	id bigint auto_increment PRIMARY KEY, 
	title varchar(256)
);

create table scores (
	id bigint auto_increment PRIMARY KEY,
	student_id bigint REFERENCES students(id) ON DELETE CASCADE, 
	subject_id bigint REFERENCES subjects(id) ON DELETE CASCADE,  
	category_id bigint REFERENCES categories(id) ON DELETE CASCADE, 
	score double
);

create table scores (
	id bigint auto_increment,
	student_id bigint REFERENCES students(id) ON DELETE CASCADE, 
	subject_id bigint REFERENCES subjects(id) ON DELETE CASCADE,  
	category_id bigint REFERENCES categories(id) ON DELETE CASCADE, 
	score double DEFAULT 0.00,
	PRIMARY KEY (id)
);

ALTER TABLE scores ADD CONSTRAINT constr_ID UNIQUE (student_id, subject_id, category_id);

delete from students;
delete from subjects;
delete from categories;
delete from scores;

drop table students;
drop table subjects;
drop table categories;
drop table scores;
