```
create table status ( id int(10) not null auto_increment, confbrev varchar(50) not null, 
  paperurl varchar(200) not null, papercount int(6) not null, reg int(16) not null, 
  sub int(16) not null, notify int(16) not null,final int(16) not null, confstart int(16) not null, 
  confend int(16) not null, confloc varchar(100) not null, lastupdate int(16) not null, primary key(id) );
```
