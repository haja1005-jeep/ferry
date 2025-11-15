CREATE TABLE realtime_operations (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  snapshot_time   DATETIME,
  departure_time  VARCHAR(10),
  ship_name       VARCHAR(100),
  route_name      VARCHAR(200),
  direction       VARCHAR(50),
  origin          VARCHAR(100),
  route_type      VARCHAR(50),
  operation_type  VARCHAR(50),
  reason          VARCHAR(200),
  status          VARCHAR(50),
  license_route   VARCHAR(200)
);

