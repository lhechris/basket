CREATE TABLE matchs(id integer primary key, equipe number, jour text, titre text, score text);
CREATE TABLE users(id integer primary key, nom text,equipe integer);
CREATE TABLE disponibilites(match integer,user integer,val integer);
CREATE TABLE selections(match integer,user integer,val integer);
CREATE TABLE entrainements(id integer primary key,jour text,titre text);
CREATE TABLE presences(entrainement integer, user integer,val integer);
