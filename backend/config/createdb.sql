CREATE TABLE matchs(id integer primary key, numero text, equipe integer, jour text, titre text, score text,collation text, otm text,maillots text,adresse text, horaire text, rendezvous text);
CREATE TABLE users(id integer primary key, nom text,prenom text,equipe integer, licence text, charte integer,otm integer);
CREATE TABLE disponibilites(jour integer,user integer,val integer);
CREATE TABLE selections(match integer,user integer,val integer);
CREATE TABLE entrainements(id integer primary key,jour text,titre text);
CREATE TABLE presences(entrainement integer, user integer,val integer);
CREATE TABLE matchinfos(user integer,match integer,opposition text, numero integer, commentaire text);
CREATE TABLE staff(id integer primary key, nom text, prenom text, licence text, role text);
CREATE TABLE staffmatchs(match,staff);