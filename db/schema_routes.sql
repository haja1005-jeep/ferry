CREATE TABLE routes (
  route_code          VARCHAR(20) PRIMARY KEY,
  route_name_ko       VARCHAR(100),
  route_name_en       VARCHAR(100),
  origin_terminal_code VARCHAR(20),
  destination_name    VARCHAR(100),
  via_islands         VARCHAR(200),
  region              VARCHAR(20),
  operator_name       VARCHAR(100),
  route_type          VARCHAR(20),
  distance_km         INT,
  sailing_time_min    INT,
  season_type         VARCHAR(20),
  memo                TEXT,
  use_yn              CHAR(1) DEFAULT 'Y'
);

