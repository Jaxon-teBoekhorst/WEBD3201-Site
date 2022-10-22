-- Jaxon teBoekhorst
-- 14 October 2022
-- WEBD3201

DROP TABLE IF EXISTS clients CASCADE ;
DROP SEQUENCE IF EXISTS client_id_seq CASCADE ;

CREATE SEQUENCE client_id_seq START 1000;

CREATE TABLE clients
(
    Id        INT PRIMARY KEY DEFAULT nextval('client_id_seq'),
    email     VARCHAR(255) UNIQUE,
    salesID   INT REFERENCES users (Id),
    firstName VARCHAR(128) NOT NULL,
    lastName  VARCHAR(128) NOT NULL,
    phoneNum  VARCHAR(10)  NOT NULL,
    phoneExt  VARCHAR(4)
);

INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client1@outlook.com',
        1001,
        'Jaxon',
        'teBoekhorst',
        '9051234567');
INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client2@outlook.com',
        1001,
        'Jax',
        'Tebs',
        '9051234567');
INSERT INTO clients(email, salesID, firstName, lastName, phoneNum)
VALUES ('jax.tebs+client3@outlook.com',
        1004,
        'Jaxon',
        'Tebs',
        '9051234567');

SELECT *
FROM clients