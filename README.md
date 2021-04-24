# Ideja projekta

Projekat kao ideju ima primenu JWT tokena sa role-based sistemom autentifikacije preko kog ostvaruje autorizaciju nad odredjenim funkcionalnostima

### Tipovi korisnika

1. Administrator - ima na raspolaganju sve funkcionalnost
2. Moderator - moze pregledati sadrzaj i izmenjivati ga
3. User - moze samo pregledati sadržaj

## Inicijalizacija baze podataka

**Kreiranje baze**

```sql
CREATE DATABASE `zarko_singidunum`;
```

**Kreiranje tabela**
```sql
create table role
(
    id int auto_increment
        primary key,
    name varchar(64) not null
);

create table todo
(
    id int auto_increment
        primary key,
    title varchar(64) not null
);

create table user
(
    id int auto_increment
        primary key,
    username varchar(64) not null,
    password varchar(64) not null,
    id_role int null,
    constraint user_ibfk_1
        foreign key (id_role) references role (id)
);

create index id_role
    on user (id_role);

```

**Dodavanje privilegija preko SQL skripte**

```sql
LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ADMIN'),(2,'MODERATOR'),(3,'CLIENT');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

```

**Dodavanje korisnika preko SQL skripte**

```sql

INSERT INTO `user` VALUES (1,'admin','admin',1),(2,'user','user',3),(4,'moderator','moderator',2);
```

Nakon uspešno izvrešenih SQL skripti potrebno je otvoriti folder u kom se nalazi projekat i pokrenuti PHP server.
