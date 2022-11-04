-- Jaxon teBoekhorst
-- 19 October 2022
-- WEBD3201

DROP TABLE IF EXISTS calls CASCADE;
DROP SEQUENCE IF EXISTS calls_id_seq CASCADE;

CREATE SEQUENCE calls_id_seq START 1000;

CREATE TABLE calls
(
    call_id   INT PRIMARY KEY DEFAULT nextval('calls_id_seq'),
    client_id INT,
    time      TIMESTAMP
);

INSERT INTO calls (client_id, time)
VALUES (1000,
        '2022-10-19 19:24:36');

INSERT INTO calls (client_id, time)
VALUES (1000,
        '2022-10-20 09:37:24');

INSERT INTO calls (client_id, time)
VALUES (1002,
        '2022-10-22 09:13:58');

SELECT *
FROM calls;