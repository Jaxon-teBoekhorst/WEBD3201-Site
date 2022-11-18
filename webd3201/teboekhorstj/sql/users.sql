-- Jaxon teBoekhorst
-- 13 September 2022
-- WEBD3201 

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS calls;
DROP SEQUENCE IF EXISTS users_id_seq;
DROP SEQUENCE IF EXISTS client_id_seq;

CREATE SEQUENCE users_id_seq START 1000;
CREATE SEQUENCE client_id_seq START 1000;

CREATE TABLE users
(
    Id           INT PRIMARY KEY DEFAULT nextval('users_id_seq'),
    EmailAddress VARCHAR(255) UNIQUE,
    Password     VARCHAR(255) NOT NULL,
    FirstName    VARCHAR(128),
    LastName     VARCHAR(128),
    LastAccess   TIMESTAMP,
    EnrolDate    TIMESTAMP,
    Enabled      BOOLEAN,
    Type         VARCHAR(2)
);

INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('jax.tebs+webd3201admin@outlook.com',
        crypt('100821229', gen_salt('bf')),
        'Jaxon',
        'teBoekhorst',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 'a');
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('jax.tebs+webd3201salesperson@outlook.com',
        crypt('100821229', gen_salt('bf')),
        'Jaxon',
        'teBoekhorst',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 's');
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('jax.tebs+webd3201client@outlook.com',
        crypt('100821229', gen_salt('bf')),
        'Jaxon',
        'teBoekhorst',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 'c');
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('jaxon.teboekhorst@dcmail.ca',
        crypt('100821229', gen_salt('bf')),
        'Jaxon',
        'teBoekhorst',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 's');
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('jdoe@dcmail.ca',
        crypt('TestPass123', gen_salt('bf')),
        'Jane',
        'Doe',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 's');
INSERT INTO Users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type)
VALUES ('bsmith@dcmail.ca',
        crypt('TestPass123', gen_salt('bf')),
        'Bill',
        'Smith',
        '2022-09-14 19:24:36', '2022-09-14 19:24:36',
        true, 's');

SELECT *
FROM users;


CREATE TABLE clients
(
    Id        INT PRIMARY KEY DEFAULT nextval('client_id_seq'),
    email     VARCHAR(255) UNIQUE,
    salesID   INT REFERENCES users (Id),
    firstName VARCHAR(128) NOT NULL,
    lastName  VARCHAR(128) NOT NULL,
    phoneNum  VARCHAR(14)  NOT NULL,
    phoneExt  VARCHAR(4)
);

INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client1@outlook.com',
        1001,
        'Jaxon',
        'teBoekhorst',
        '(905) 123-4567');
INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client2@outlook.com',
        1001,
        'Jax',
        'Tebs',
        '(905) 123-4567');
INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client3@outlook.com',
        1003,
        'Jaxon',
        'Tebs',
        '(905) 123-4567');

SELECT *
FROM clients