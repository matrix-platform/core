
CREATE TABLE common_member (
    id       INTEGER NOT NULL PRIMARY KEY,
    username TEXT    NOT NULL UNIQUE,
    password TEXT        NULL,
    name     TEXT        NULL,
    mobile   TEXT        NULL,
    mail     TEXT        NULL,
    avatar   TEXT        NULL,
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

CREATE TABLE base_vendor () INHERITS (common_member);

CREATE TABLE base_vendor_log (
    id          INTEGER   NOT NULL PRIMARY KEY,
    vendor_id   INTEGER   NOT NULL,
    type        INTEGER   NOT NULL, -- options: vendor-log-type
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

CREATE TABLE base_file (
    id            INTEGER   NOT NULL PRIMARY KEY,
    parent_id     INTEGER       NULL,
    type          INTEGER   NOT NULL,
    name          TEXT      NOT NULL,
    path          TEXT          NULL UNIQUE,
    size          BIGINT        NULL,
    hash          TEXT          NULL,
    description   TEXT          NULL,
    mime_type     TEXT          NULL,
    width         INTEGER       NULL,
    height        INTEGER       NULL,
    seconds       INTEGER       NULL,
    privilege     INTEGER   NOT NULL,
    owner_id      INTEGER   NOT NULL,
    group_id      INTEGER       NULL,
    modified_time TIMESTAMP NOT NULL,
    deleted       BOOLEAN   NOT NULL
);

CREATE TABLE base_sms_log (
    id          INTEGER   NOT NULL PRIMARY KEY,
    receiver    TEXT      NOT NULL,
    content     TEXT      NOT NULL,
    response    TEXT          NULL,
    ip          TEXT      NOT NULL,
    create_time TIMESTAMP NOT NULL,
    status      INTEGER   NOT NULL
);

CREATE TABLE base_menu (
    id           INTEGER   NOT NULL PRIMARY KEY,
    parent_id    INTEGER       NULL,
    title        TEXT          NULL,
    icon         TEXT          NULL,
    url          TEXT          NULL,
    enable_time  TIMESTAMP     NULL,
    disable_time TIMESTAMP     NULL,
    ranking      INTEGER   NOT NULL
);

CREATE TABLE base_page (
    id           INTEGER   NOT NULL PRIMARY KEY,
    path         TEXT      NOT NULL UNIQUE,
    title        TEXT          NULL,
    description  TEXT          NULL,
    fluid        BOOLEAN   NOT NULL,
    color        TEXT          NULL,
    bg_color     TEXT          NULL,
    bulletin     BOOLEAN   NOT NULL,
    header       BOOLEAN   NOT NULL,
    footer       BOOLEAN   NOT NULL,
    enable_time  TIMESTAMP     NULL,
    disable_time TIMESTAMP     NULL
);

CREATE TABLE base_block (
    id             INTEGER          NOT NULL PRIMARY KEY,
    page_id        INTEGER          NOT NULL,
    module         TEXT             NOT NULL,
    title          TEXT                 NULL,
    content        TEXT                 NULL,
    image          TEXT                 NULL,
    url            TEXT                 NULL,
    extra          TEXT                 NULL,
    padding_top    DOUBLE PRECISION     NULL,
    padding_bottom DOUBLE PRECISION     NULL,
    fluid          BOOLEAN              NULL,
    color          TEXT                 NULL,
    bg_color       TEXT                 NULL,
    enable_time    TIMESTAMP            NULL,
    disable_time   TIMESTAMP            NULL,
    ranking        INTEGER          NOT NULL
);

CREATE TABLE base_block_item (
    id           INTEGER   NOT NULL PRIMARY KEY,
    block_id     INTEGER   NOT NULL,
    title        TEXT          NULL,
    content      TEXT          NULL,
    image        TEXT          NULL,
    url          TEXT          NULL,
    extra        TEXT          NULL,
    enable_time  TIMESTAMP     NULL,
    disable_time TIMESTAMP     NULL,
    ranking      INTEGER   NOT NULL
);

