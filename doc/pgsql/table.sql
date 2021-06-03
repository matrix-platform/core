
CREATE TABLE common_member (
    id       INTEGER NOT NULL PRIMARY KEY,
    username TEXT    NOT NULL UNIQUE,
    password TEXT        NULL,
    name     TEXT        NULL,
    mobile   TEXT        NULL,
    mail     TEXT        NULL,
    disabled BOOLEAN NOT NULL
);

CREATE TABLE base_group (
    id    INTEGER NOT NULL PRIMARY KEY,
    title TEXT        NULL UNIQUE
);

CREATE TABLE base_manipulation_log (
    id         INTEGER   NOT NULL PRIMARY KEY DEFAULT NEXTVAL('base_manipulation'),
    type       INTEGER   NOT NULL,
    log_time   TIMESTAMP NOT NULL DEFAULT LOCALTIMESTAMP(0),
    controller TEXT      NOT NULL,
    user_id    INTEGER       NULL,
    member_id  INTEGER       NULL,
    ip         TEXT          NULL,
    data_type  TEXT      NOT NULL,
    data_id    INTEGER   NOT NULL,
    previous   TEXT          NULL,
    current    TEXT          NULL
);

CREATE TABLE base_member () INHERITS (common_member);

CREATE TABLE base_member_log (
    id          INTEGER   NOT NULL PRIMARY KEY,
    member_id   INTEGER   NOT NULL,
    type        INTEGER   NOT NULL, -- options: member-log-type
    ip          TEXT      NOT NULL,
    create_time TIMESTAMP NOT NULL
);

CREATE TABLE base_user (
    id          INTEGER NOT NULL PRIMARY KEY,
    username    TEXT    NOT NULL UNIQUE,
    password    TEXT        NULL,
    group_id    INTEGER     NULL,
    begin_date  DATE        NULL,
    expire_date DATE        NULL,
    disabled    BOOLEAN NOT NULL
);

CREATE TABLE base_user_log (
    id          INTEGER   NOT NULL PRIMARY KEY,
    user_id     INTEGER   NOT NULL,
    type        INTEGER   NOT NULL, -- options: user-log-type
    ip          TEXT      NOT NULL,
    create_time TIMESTAMP NOT NULL
);

