CREATE TABLE timetables (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  route_code           VARCHAR(20),
  trip_no              VARCHAR(10),
  season               VARCHAR(20),
  weekday_type         VARCHAR(20), -- 매일/평일/토요일/일요일/공휴일/목·금 등
  depart_time          VARCHAR(10),
  arrive_time          VARCHAR(10),
  depart_terminal_code VARCHAR(20),
  arrive_place         VARCHAR(100),
  notes                VARCHAR(200),
  use_yn               CHAR(1) DEFAULT 'Y'
);

