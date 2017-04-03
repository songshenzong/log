-- auto-generated definition
CREATE TABLE songshenzong_logs
(
    id         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
        PRIMARY KEY,
    data       LONGTEXT         NOT NULL,
    utime      VARCHAR(255)     NOT NULL,
    uri        VARCHAR(255)     NOT NULL,
    ip         VARCHAR(255)     NOT NULL,
    method     VARCHAR(255)     NOT NULL,
    created_at TIMESTAMP        NULL,
    updated_at TIMESTAMP        NULL
);

CREATE INDEX songshenzong_logs_created_at_index
    ON songshenzong_logs (created_at);

CREATE INDEX songshenzong_logs_ip_index
    ON songshenzong_logs (ip);

CREATE INDEX songshenzong_logs_method_index
    ON songshenzong_logs (method);

CREATE INDEX songshenzong_logs_uri_index
    ON songshenzong_logs (uri);

CREATE INDEX songshenzong_logs_utime_index
    ON songshenzong_logs (utime);

