CREATE TABLE users (
	id serial PRIMARY KEY,
	name varchar(40) NOT NULL,
	surname varchar(40) NOT NULL,
	telephone varchar(12) NOT NULL	
);


CREATE TABLE door (
	id serial PRIMARY KEY,
	description varchar(64)
);

CREATE TABLE card (
	id serial PRIMARY KEY,
	user_id integer REFERENCES users(id),
	is_active bool default False
);


CREATE TABLE permissions (
	card_id integer REFERENCES card(id),
	door_id integer REFERENCES door(id)
);

CREATE TABLE log (
	card_id integer REFERENCES card(id),
	door_id integer REFERENCES door(id),
	event_time timestamp NOT NULL,
	decision varchar(32)
);

CREATE or replace  VIEW logs AS 
SELECT l.event_time, u.name, u.surname, d.description, u.telephone, l.decision, u.id as user_id, c.id as card_id, d.id as door_id  from 
log l LEFT JOIN card c on c.id = l.card_id 
left join users u on u.id = c.user_id 
left join door d on d.id = l.door_id;


CREATE OR REPLACE FUNCTION delete_user_dependencies() RETURNS trigger AS $delete_user_dependencies$
BEGIN
  UPDATE card set is_active = False, user_id = NULL  where user_id = OLD.id ;
  RETURN OLD;
END;
$delete_user_dependencies$ LANGUAGE plpgsql;


CREATE TRIGGER updatecards BEFORE DELETE ON users FOR EACH ROW EXECUTE PROCEDURE delete_user_dependencies;

CREATE OR REPLACE FUNCTION check_permissions(cardId integer, doorId integer) RETURNS text AS $check_permissions$
BEGIN
  IF EXISTS (SELECT 1 FROM permissions p where p.card_id = cardId and  p.door_id = doorId) THEN
    INSERT INTO log VALUES (cardId, doorId, NOW(), 'OK');
    RETURN 'DOSTEP PRZYZNANY';
  END IF;
  INSERT INTO log VALUES (cardId, doorId, NOW(), 'DENIED');
  RETURN 'DOSTEP ODMOWIONY';
END;
$check_permissions$ LANGUAGE plpgsql;

INSERT INTO users(name, surname, telephone) VALUES ('Barbara', 'Garcia', '702570684');
INSERT INTO users(name, surname, telephone) VALUES ('Amber', 'Lewis', '740210127');
INSERT INTO users(name, surname, telephone) VALUES ('Robert', 'Overbeck', '816289495');
INSERT INTO users(name, surname, telephone) VALUES ('Scot', 'Pothier', '303887962');

INSERT INTO card(user_id) VALUES (1), (2), (3), (4);
INSERT INTO door(description) VALUES ('korytarz'),( 'recepcja'),( 'pokoj100'), ('pokoj101'), ('pokoj103'), ('pomieszczenie techniczne');       

INSERT INTO permissions VALUES (2, 1);
INSERT INTO permissions VALUES (2, 2);
INSERT INTO permissions VALUES (2, 3);
INSERT INTO permissions VALUES (2, 4);
INSERT INTO permissions VALUES (2, 5);
INSERT INTO permissions VALUES (2, 6);

INSERT INTO permissions VALUES (3, 1);
INSERT INTO permissions VALUES (3, 2);
INSERT INTO permissions VALUES (3, 3);

INSERT INTO permissions VALUES (4, 1);
INSERT INTO permissions VALUES (4, 2);
INSERT INTO permissions VALUES (4, 4);

INSERT INTO permissions VALUES (5, 1);
INSERT INTO permissions VALUES (5, 2);
INSERT INTO permissions VALUES (5, 5);
