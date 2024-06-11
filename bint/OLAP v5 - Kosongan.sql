drop view if exists DuesMonthly;
drop view if exists RtIncomeMonthly;
drop table if exists factDues;
drop table if exists dimDues;
drop table if exists dimRt;
drop table if exists dimType;
drop table if exists dimCivilian;
drop table if exists dimDate;

create table dimDate(
  id_dimDate bigint PRIMARY KEY AUTO_INCREMENT,
  `date` date,
  year int,
  month int,
  day int
);

create table dimType(
  id_dimType bigint PRIMARY KEY AUTO_INCREMENT,
  `type` varchar(20)
);

/*
insert into dimType(type)
values ('Event'),('Security'),('TrashManagement');
*/

create table dimRt(
  id_dimRt bigint PRIMARY KEY AUTO_INCREMENT,
  rt bigint
);

/*
insert into dimRt(rt)
values (1),(2),(3),(4),(5),(6),(7),(8),(9);
*/

create table dimCivilian(
  id_dimCivilian bigint PRIMARY KEY AUTO_INCREMENT,
  name varchar(100),
  nik varchar(16),
  nkk varchar(16),
  residentStatus enum('PermanentResident', 'Kos', 'ContractResident')
  /* Just In case
  rt_id bigint not null,
  CONSTRAINT user_rt FOREIGN KEY(rt_id) references dimRt(id_dimRt)*/
);

create table dimDues(
  id_dimDues bigint PRIMARY KEY AUTO_INCREMENT,
  `desc` varchar(500), 
  type_id bigint not null,
  status boolean default 1,
  rt_id bigint not null,
  created_at bigint not null,
  CONSTRAINT dues_rt FOREIGN KEY(rt_id) references dimRt(id_dimRt),
  CONSTRAINT duesType FOREIGN KEY(type_id) references dimType(id_dimType),
  CONSTRAINT dues_start FOREIGN KEY(created_at) references dimDate(id_dimDate)
);

create table FactDues(
  id_FactDues bigint PRIMARY KEY AUTO_INCREMENT,
  fk_dimDate bigint not null, /*duespaymentlog: paid_for*/
  fk_dimCivilian bigint not null, /*dues_member: member*/
  fk_dimDues bigint not null, /*dues: typeDues*/
  amt decimal(10,2),
  CONSTRAINT paid_for FOREIGN KEY(fk_dimDate) references dimDate(id_dimDate),
  CONSTRAINT dues_member FOREIGN KEY(fk_dimCivilian) references dimCivilian(id_dimCivilian),
  CONSTRAINT dues FOREIGN KEY(fk_dimDues) references dimDues(id_dimDues)
);

create view DuesMonthly as
(
  select
  monthname(ddate.date) as 'bulan',
  ddate.year as 'tahun',
  ddues.desc as 'deskripsi',
  dtype.type as 'tipe',
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by ddate.month, ddate.year, ddues.id_dimDues
  order by ddues.id_dimDues, ddate.year, ddate.month
);

create view rtMonthly as
(select
  monthname(ddate.date) as 'bulan',
  ddate.year as 'tahun',
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by ddate.month, ddate.year, ddues.rt_id
  order by ddues.rt_id, ddate.year, ddate.month
);

create view userMonthlyExpense as 
(select
  monthname(ddate.date) as 'bulan',
  ddate.year as 'tahun',
  dciv.nik as 'NIK',
  dciv.name as 'Nama', 
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by ddate.month, ddate.year, dciv.id_dimCivilian
  order by dciv.id_dimCivilian, ddate.year, ddate.month
 );

create view generalPaymentList as (
select
  monthname(ddate.date) as 'bulan',
  ddate.year as 'tahun',
  dciv.nik as 'NIK',
  dciv.name as 'Nama', 
  ddues.desc as 'Desc',
  dtype.type as 'Type',
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by ddate.month, ddate.year
  order by dciv.id_dimCivilian, ddate.year, ddate.month);


create view duesList as (
select
  ddues.desc as 'Desc',
  dtype.type as 'Type',
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by ddues.id_dimDues
  order by ddues.id_dimDues
    );

create view totalPayment as (
select
  dciv.nik as 'NIK',
  dciv.name as 'Name',
  dciv.residentStatus as 'Status',
  drt.rt as 'RT',
  sum(fd.amt) as 'jumlah'
  from factdues fd
  left join dimcivilian dciv on fd.fk_dimCivilian = dciv.id_dimCivilian
  left join dimdues ddues on fd.fk_dimDues = ddues.id_dimDues
  left join dimtype dtype on ddues.type_id = dtype.id_dimType
  left join dimrt drt on ddues.rt_id = drt.id_dimRt
  left join dimdate ddate on fd.fk_dimDate = ddate.id_dimDate
  group by dciv.id_dimCivilian
  order by dciv.id_dimCivilian
    );