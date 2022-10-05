-- Jaxon teBoekhorst
-- 13 September 2022
-- WEBD3201 

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP TABLE IF EXISTS users;
DROP SEQUENCE IF EXISTS users_id_seq;

CREATE SEQUENCE users_id_seq START 1000;

CREATE TABLE users (
    Id INT PRIMARY KEY DEFAULT nextval('users_id_seq'),
    EmailAddress VARCHAR(255) UNIQUE,
    Password VARCHAR(255) NOT NULL,
    FirstName VARCHAR(128),
    LastName VARCHAR(128),
    LastAccess TIMESTAMP,
    EnrolDate TIMESTAMP,
    Enabled BOOLEAN,
    Type VARCHAR(2)
);

INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type) VALUES(
    'jaxon.teboekhorst@dcmail.ca',
    crypt('100821229', gen_salt('bf')),
    'Jaxon',
    'teBoekhorst',
    '2022-09-14 19:24:36', '2022-09-14 19:24:36',
    true, 's' 
);
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type) VALUES(
    'jdoe@dcmail.ca',
    crypt('TestPass123', gen_salt('bf')),
    'Jane',
    'Doe',
    '2022-09-14 19:24:36', '2022-09-14 19:24:36',
    true, 's' 
);
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type) VALUES(
    'bsmith@dcmail.ca',
    crypt('TestPass123', gen_salt('bf')),
    'Bill',
    'Smith',
    '2022-09-14 19:24:36', '2022-09-14 19:24:36',
    true, 's' 
);

SELECT * FROM users;