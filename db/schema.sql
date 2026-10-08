-- 여객선 터미널 / 운임 DB (MySQL 5.7+, utf8mb4)
CREATE TABLE IF NOT EXISTS terminals (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL COMMENT '터미널(선착장) 이름',
  city        VARCHAR(50)  NULL COMMENT '시/군',
  address     VARCHAR(200) NULL,
  lat         DECIMAL(10,7) NOT NULL,
  lng         DECIMAL(10,7) NOT NULL,
  phone       VARCHAR(40)  NULL,
  memo        TEXT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_city (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS terminal_photos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  terminal_id INT NOT NULL,
  kind        ENUM('terminal','fare_sheet','timetable') NOT NULL DEFAULT 'terminal',
  file_name   VARCHAR(100) NOT NULL,
  caption     VARCHAR(200) NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_terminal (terminal_id),
  CONSTRAINT fk_photo_terminal FOREIGN KEY (terminal_id) REFERENCES terminals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fares (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  terminal_id INT NOT NULL,
  route_name  VARCHAR(100) NULL COMMENT '행선지/항로',
  category    ENUM('person','vehicle','other') NOT NULL,
  item_name   VARCHAR(100) NOT NULL COMMENT '예: 어른, 경차, 오토바이, 자전거',
  price       INT NOT NULL COMMENT '원',
  note        VARCHAR(200) NULL,
  source      ENUM('manual','ai') NOT NULL DEFAULT 'manual',
  photo_id    INT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_terminal (terminal_id),
  CONSTRAINT fk_fare_terminal FOREIGN KEY (terminal_id) REFERENCES terminals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
