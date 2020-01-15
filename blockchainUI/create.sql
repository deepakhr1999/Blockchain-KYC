create table data.people (
    Id int not null AUTO_INCREMENT,
	Name varchar(255) not null,
	Dob date not null,
	Phone varchar(20) not null,
	Aadhar varchar(20) not null,
	Bank varchar(20) not null,
	is_pending int not null,
	Filename varchar(255) not null,
	Hash varchar(500) not null,
	PRIMARY KEY (Id) );

-- GRANT ALL PRIVILEGES ON data.* TO 'phpmyadmin'@'localhost';
-- insert into data.people (Name, Dob, Phone, Aadhar, Bank, is_pending, Filename, Hash) values ('Rajesh Kumar', '1989/07/18', '(872) 865 2129', '83052940203', 'Bank1', 'rw_aadhar.png', '5f4dcc3b5aa765d61d8327deb882cf99');

