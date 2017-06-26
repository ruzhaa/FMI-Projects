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
	score double,
	PRIMARY KEY (id),
	UNIQUE KEY unique_score (student_id, subject_id, category_id)
);

create table test_import (
	id bigint auto_increment PRIMARY KEY,
	name varchar(256),
	fn varchar(256),
	control double,
	project double,
	exam double
);