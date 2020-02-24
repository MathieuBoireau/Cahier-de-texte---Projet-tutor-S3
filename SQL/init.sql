DROP TABLE IF EXISTS groupes CASCADE;

CREATE TABLE groupes
(
	groupe     varchar(10) PRIMARY KEY NOT NULL,
	groupePere varchar(10) REFERENCES groupes (groupe) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TYPE IF EXISTS role CASCADE;
CREATE TYPE role AS ENUM ('A', 'E', 'T', 'AE');

DROP TABLE IF EXISTS utilisateurs CASCADE;

CREATE TABLE utilisateurs
(
	id_user          varchar(15) PRIMARY KEY NOT NULL,
	nom              varchar(20)             NOT NULL,
	prenom           varchar(20)             NOT NULL,
	mdp              varchar                 NOT NULL,
	-- mdp au moins 2 maj, 2 min, 2 cara spé., 8 cara min.
	role_utilisateur role                    NOT NULL,
	cree_le          date                    NOT NULL,
	maj_le           date                    NOT NULL,
	mdpGenere        varchar
);

DROP FUNCTION IF EXISTS f_trig_login();

/**
  Trigger permettant de gérer la date de création d'un utilisateur
  Indique la date en fonction de l'année scolaire
 */
CREATE OR REPLACE FUNCTION f_trig_login() RETURNS TRIGGER as
$$
BEGIN
	IF (TG_OP LIKE 'INSERT') THEN
		IF (SELECT EXTRACT(MONTH FROM CURRENT_DATE)<=6) THEN
			select date(CONCAT(EXTRACT(YEAR FROM CURRENT_DATE)-1,'-09-01')) into NEW.cree_le;
		ELSE
			select date(CONCAT(EXTRACT(YEAR FROM CURRENT_DATE),'-09-01')) into NEW.cree_le;
		END IF;
	end if;
	select now() into NEW.maj_le;
	RETURN NEW;
END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_login on utilisateurs;

create trigger trig_login
	BEFORE INSERT OR UPDATE
	on utilisateurs
	FOR each row
execute procedure f_trig_login();

DROP TABLE IF EXISTS modules CASCADE;

CREATE TABLE modules
(
	valeur_module varchar(8)  NOT NULL PRIMARY KEY,
	lib_module    varchar(20) NOT NULL,
	couleur       varchar(6),
	droit         role
);


DROP TABLE IF EXISTS affectationsModules CASCADE;

CREATE TABLE affectationsModules
(
	id_user       varchar REFERENCES utilisateurs (id_user) ON DELETE CASCADE ON UPDATE CASCADE,
	valeur_module varchar REFERENCES modules (valeur_module) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (id_user, valeur_module)
);


DROP FUNCTION IF EXISTS f_trig_chkAffectationModules();

/**
  Trigger vérifiant que l'utilisateur et le module possèdent les même rôles
 */
CREATE OR REPLACE FUNCTION f_trig_chkAffectationModules() RETURNS TRIGGER as
$$
BEGIN
	IF (
				'' || (select role_utilisateur from utilisateurs where id_user = NEW.id_user)
			LIKE
				('%' || (select droit from modules where valeur_module = NEW.valeur_module) || '%')
		) THEN
		RETURN NEW;
	END IF;
	RETURN NULL;
END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_chkAffectationModules on affectationsModules;

CREATE TRIGGER trig_chkAffectationModules
	BEFORE INSERT OR UPDATE
	on affectationsModules
	FOR EACH row
EXECUTE PROCEDURE f_trig_chkAffectationModules();


DROP TABLE IF EXISTS affectationGroupesTuteurs;

CREATE TABLE affectationGroupesTuteurs(
	id_user varchar REFERENCES utilisateurs(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
	groupe varchar REFERENCES groupes(groupe) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY(id_user, groupe)
);


DROP TABLE IF EXISTS seance CASCADE;

CREATE TABLE seance
(
	valeur_module varchar REFERENCES modules (valeur_module) ON DELETE CASCADE ON UPDATE CASCADE,
	date_seance   varchar,
	id_user       varchar REFERENCES utilisateurs (id_user) ON DELETE CASCADE ON UPDATE CASCADE,
	type_seance   varchar(2),
	groupe        varchar,
	PRIMARY KEY (valeur_module, date_seance, id_user, type_seance, groupe)
);

DROP TABLE IF EXISTS evenement CASCADE;

CREATE TABLE evenement
(
	id_event      serial      NOT NULL,
	valeur_module varchar     NOT NULL,
	date_seance   varchar     NOT NULL,
	id_user       varchar     NOT NULL,
	type_seance   varchar(2)  NOT NULL,
	groupe        varchar     NOT NULL,
	FOREIGN KEY (valeur_module, date_seance, id_user, type_seance, groupe) REFERENCES seance (valeur_module, date_seance, id_user, type_seance, groupe) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (id_event, valeur_module, date_seance, id_user, type_seance, groupe),
	type_event    varchar     NOT NULL,
	lib_event     varchar(90) NOT NULL,
	date_rendu    varchar,
	duree         float
);

DROP TABLE IF EXISTS pieces_jointes CASCADE;

CREATE TABLE pieces_jointes
(
    nomFichier    varchar NOT NULL,
	pj            varchar NOT NULL,
	id_event      int     NOT NULL,
	valeur_module varchar NOT NULL,
	date_seance   varchar NOT NULL,
	id_user       varchar NOT NULL,
	type_seance   varchar(2) NOT NULL,
	groupe        varchar NOT NULL,
	date          varchar NOT NULL,
	FOREIGN KEY (id_event, valeur_module, date_seance, id_user, type_seance, groupe) REFERENCES evenement (id_event, valeur_module, date_seance, id_user, type_seance, groupe) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (nomFichier) -- nomFichier est un id unique généré par md5 en php
);

DROP TABLE IF EXISTS contraintes;

CREATE TABLE contraintes
(
	tab    varchar,
	CONSTRAINT chk_table CHECK (tab in ('seance', 'evenement')),
	type   varchar,
	CONSTRAINT chk_type CHECK (type in ('nombre', 'type', 'affichageNombre')),
	valeur varchar NOT NULL,
	PRIMARY KEY (tab, type, valeur)
);
/**
  C'est 3 insert doivent impérativement être avant le trigger trig_chkContrainteNb
  Il permet d'obligatoirement avoir une contrainte sur le nombre de pièces-jointes par événements et événements par séances
 */
INSERT INTO contraintes values('seance', 'nombre', '5');
INSERT INTO contraintes values('seance', 'affichageNombre', '50');
INSERT INTO contraintes values('evenement', 'nombre', '3');

DROP FUNCTION IF EXISTS f_trig_chkContrainteNb();

/**
  Trigger vérifiant qu'il n'y a pas de nouvelle insertion de contrainte sur nombre, et que si un nombre est update la valeur correspond bien à un nombre
 */
CREATE OR REPLACE FUNCTION f_trig_chkContrainteNb() RETURNS TRIGGER as $$
	BEGIN
		IF (NEW.type LIKE 'nombre') OR (NEW.type LIKE 'affichageNombre') THEN
			IF(TG_OP LIKE 'INSERT') THEN
				RETURN NULL;
			ELSE
				PERFORM CAST(NEW.valeur AS INTEGER);
			END IF;
		end if;
		RETURN NEW;
	EXCEPTION
		WHEN OTHERS THEN
			RETURN NULL;
	END
$$language plpgsql;

DROP TRIGGER IF EXISTS trig_chkContrainteNb on contraintes;

CREATE TRIGGER trig_chkContrainteNb
	BEFORE INSERT OR UPDATE
	on contraintes
	FOR EACH row
EXECUTE PROCEDURE f_trig_chkContrainteNb();

DROP FUNCTION IF EXISTS f_trig_deleteContrainte();

/**
  Trigger empêchant la suppression d'une contrainte sur nombre
 */
CREATE OR REPLACE FUNCTION f_trig_deleteContrainte() RETURNS TRIGGER as $$
    BEGIN
        IF(OLD.type LIKE 'nombre') THEN
            RETURN NULL;
        end if;
        RETURN OLD;
    END
$$language plpgsql;

DROP TRIGGER IF EXISTS trig_deleteContrainte on contraintes;

CREATE TRIGGER trig_deleteContrainte
    BEFORE DELETE on contraintes
    FOR EACH row
EXECUTE PROCEDURE f_trig_deleteContrainte();

INSERT INTO contraintes values('seance', 'type', 'TD');
INSERT INTO contraintes values('seance', 'type', 'TP');
INSERT INTO contraintes values('evenement', 'type', 'Travail fait');
INSERT INTO contraintes values('evenement', 'type', 'Travail à faire');

DROP FUNCTION IF EXISTS f_trig_chkTypeSeance();

/**
  Trigger vérifiant que le type d'une séance insérée est bien présent dans la table des contraintes
 */
CREATE OR REPLACE FUNCTION f_trig_chkTypeSeance() RETURNS TRIGGER as $$
	BEGIN
		IF(NEW.type_seance NOT IN(select valeur from contraintes where tab LIKE 'seance' and type LIKE 'type')) THEN
			RETURN NULL;
		END IF;
		RETURN NEW;
	END
$$language plpgsql;

DROP TRIGGER IF EXISTS trig_chkTypeSeance on seance;

CREATE TRIGGER trig_chkTypeSeance
	BEFORE INSERT OR UPDATE on seance
	FOR EACH row
EXECUTE PROCEDURE f_trig_chkTypeSeance();

DROP FUNCTION IF EXISTS f_trig_chkTypeEvent();

/**
  Trigger vérifiant que le type d'un événement inséré est bien présent dans la table des contraintes
 */
CREATE OR REPLACE FUNCTION f_trig_chkTypeEvent() RETURNS TRIGGER as $$
	BEGIN
		IF(NEW.type_event NOT IN(select valeur from contraintes where tab LIKE 'evenement' and type LIKE 'type')) THEN
			RETURN NULL;
		end if;
		RETURN NEW;
	END
$$language plpgsql;

DROP TRIGGER IF EXISTS trig_chkTypeEvent on evenement;

CREATE TRIGGER trig_chkTypeEvent
	BEFORE INSERT OR UPDATE on evenement
	FOR EACH row
EXECUTE PROCEDURE f_trig_chkTypeEvent();

DROP FUNCTION IF EXISTS f_trig_createEvent();

/**
  Trigger vérifiant qu'un n'insère pas plus d'événement à une séance que la contrainte ne le permet
 */
CREATE OR REPLACE FUNCTION f_trig_createEvent() RETURNS TRIGGER as $$
DECLARE
	nb int;
BEGIN
	select COUNT(*)
	into nb
	from evenement
	where valeur_module LIKE NEW.valeur_module
	  and date_seance LIKE NEW.date_seance
	  and id_user LIKE NEW.id_user
	  and type_seance LIKE NEW.type_seance
	  and groupe LIKE NEW.groupe;
	IF (nb >= CAST((select valeur from contraintes where type LIKE 'nombre' and tab LIKE 'seance') AS INTEGER)) THEN
        RAISE EXCEPTION 'Il ne peut pas y avoir plus de 5 événements par séances';
	end if;
    RETURN NEW;
    END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_createEvent on evenement;

create trigger trig_createEvent
	BEFORE INSERT
	on evenement
	FOR each row
execute procedure f_trig_createEvent();

DROP FUNCTION IF EXISTS f_trig_createPj();

/**
  Trigger vérifiant qu'un n'insère pas plus de pièces-jointes à un événement que la contrainte ne le permet
 */
CREATE OR REPLACE FUNCTION f_trig_createPj() RETURNS TRIGGER as
$$
DECLARE
	nb int;
BEGIN
	select COUNT(*)
	into nb
	from pieces_jointes
	where id_event = NEW.id_event
	  and valeur_module LIKE NEW.valeur_module
	  and date_seance LIKE NEW.date_seance
	  and id_user LIKE NEW.id_user
	  and type_seance LIKE NEW.type_seance
	  and groupe LIKE NEW.groupe;
	IF (nb >= CAST((select valeur from contraintes where tab LIKE 'evenement' and type LIKE 'nombre') AS INTEGER)) THEN
		RAISE EXCEPTION 'Il ne peut pas y avoir plus de 3 pièce-jointes par événement';
	end if;
	RETURN NEW;
END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_createPj on pieces_jointes;

create trigger trig_createPj
	BEFORE INSERT
	on pieces_jointes
	FOR EACH row
execute procedure f_trig_createPj();

DROP TABLE IF EXISTS audit_seance;

/**
  Table regroupant l'historique des modifications sur les séance
 */
CREATE TABLE audit_seance
(
	id_audit_seance SERIAL PRIMARY KEY,
	valeur_module   varchar,
	date_seance     varchar,
	id_user         varchar,
	type_seance     varchar,
	groupe          varchar,
	date            date,
	modif           varchar
);

DROP FUNCTION IF EXISTS f_trig_historiqueSeanceUpdate();

/**
  Trigger insérant les modifications effectuées sur une séance dans la table audit_seance
 */
CREATE OR REPLACE FUNCTION f_trig_historiqueSeanceUpdate() RETURNS TRIGGER as
$$
DECLARE
	modif varchar;
BEGIN
	modif = '';
	IF (NEW.date_seance NOT LIKE OLD.date_seance) THEN
		modif = modif || OLD.date_seance || ' -> ' || NEW.date_seance || ', ';
	end if;
	IF (NEW.id_user NOT LIKE OLD.id_user) THEN
		modif = modif || OLD.id_user || ' -> ' || NEW.id_user || ', ';
	end if;
	INSERT INTO audit_seance
	values (default, NEW.valeur_module, NEW.date_seance, NEW.id_user, NEW.type_seance, NEW
		.groupe, now(), modif);
	RETURN NEW;
END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_historiqueSeanceUpdate on seance;

CREATE TRIGGER trig_historiqueSeanceUpdate
	AFTER UPDATE
	on seance
	FOR EACH row
EXECUTE PROCEDURE f_trig_historiqueSeanceUpdate();

DROP TABLE IF EXISTS audit_evenement;

/**
  Table regroupant l'historique des modifications sur les séance
 */
CREATE TABLE audit_evenement
(
	id_audit_evenement SERIAL PRIMARY KEY NOT NULL,
	id_event           varchar            NOT NULL,
	valeur_module      varchar            NOT NULL,
	date_seance        varchar            NOT NULL,
	id_user            varchar            NOT NULL,
	type_seance        varchar            NOT NULL,
	groupe             varchar            NOT NULL,
	modif              varchar            NOT NULL,
	date               date               NOT NULL
);

DROP FUNCTION IF EXISTS f_trig_historiqueEvenementUpdate();

/**
  Trigger insérant les modifications effectuées sur une séance dans la table audit_seance
 */
CREATE OR REPLACE FUNCTION f_trig_historiqueEvenementUpdate() RETURNS TRIGGER as
$$
DECLARE
	modif varchar;
BEGIN
	modif = '';
	IF (NEW.type_event NOT LIKE OLD.type_event) THEN
		modif = modif || OLD.type_event || ' -> ' || NEW.type_event || ', ';
	end if;
	IF (NEW.lib_event NOT LIKE OLD.lib_event) THEN
		modif = modif || OLD.lib_event || ' -> ' || NEW.lib_event || ', ';
	end if;
	IF (NEW.date_rendu NOT LIKE OLD.date_rendu) THEN
		modif = modif || OLD.date_rendu || ' -> ' || NEW.date_rendu || ', ';
	end if;
	INSERT INTO audit_evenement
	values (default, NEW.id_event, NEW.valeur_module, NEW.date_seance, NEW.id_user, NEW.type_seance, NEW.groupe, modif, now());
	RETURN NEW;
END
$$ language plpgsql;

DROP TRIGGER IF EXISTS trig_historiqueEvenementUpdate on evenement;

CREATE TRIGGER trig_historiqueEvenementUpdate
	AFTER UPDATE
	on evenement
	FOR EACH row
EXECUTE PROCEDURE f_trig_historiqueEvenementUpdate();


DROP TABLE IF EXISTS utilisateurseance;

/**
  Table lien tous les utilisateurs avec toutes les séances
  Permet de gérer les sémaphores des séances pour chaque utilisateur
 */
CREATE TABLE utilisateurseance (
	valeur_module varchar     NOT NULL,
	date_seance   varchar     NOT NULL,
	id_user       varchar     NOT NULL,
	type_seance   varchar(2),
	groupe        varchar,
	FOREIGN KEY (valeur_module, date_seance, id_user, type_seance, groupe) REFERENCES seance (valeur_module, date_seance, id_user, type_seance, groupe) ON DELETE CASCADE ON UPDATE CASCADE,
	utilisateur   varchar REFERENCES utilisateurs(id_user) NOT NULL,
	semaphore     boolean default false,
	PRIMARY KEY (valeur_module, date_seance, id_user, type_seance, groupe, utilisateur)
);

DROP TABLE IF EXISTS semaphore;

CREATE TABLE semaphore (
	etat              varchar(10) NOT NULL,
	couleurNonVu      varchar(10) NOT NULL,
	couleurVu         varchar(10) NOT NULL
);

-- Insertion des valeurs des sémaphores

insert into semaphore values ('vu','#000000','#ff0000');
insert into semaphore values ('texte','#000000','#ff0000');