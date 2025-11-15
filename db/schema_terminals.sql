DROP TABLE IF EXISTS terminals;

CREATE TABLE terminals (
    terminal_code   VARCHAR(20)  NOT NULL PRIMARY KEY COMMENT '터미널 고유 코드(JN-MP-001 등)',

    name_ko         VARCHAR(100) NOT NULL COMMENT '터미널명(한글)',
    name_en         VARCHAR(150) NULL     COMMENT '터미널명(영문)',
    
    city            VARCHAR(50)  NOT NULL COMMENT '시/군',
    eup_myeon       VARCHAR(80)  NULL     COMMENT '읍/면/동',
    ri              VARCHAR(80)  NULL     COMMENT '리/동',

    terminal_type   VARCHAR(50)  NOT NULL DEFAULT '소형선착장'
                                COMMENT '터미널 유형',

    road_addr       VARCHAR(200) NULL     COMMENT '도로명 주소',
    jibun_addr      VARCHAR(200) NULL     COMMENT '지번 주소',

    lat             DECIMAL(10,7) NULL COMMENT '위도',
    lng             DECIMAL(10,7) NULL COMMENT '경도',

    phone           VARCHAR(40)  NULL COMMENT '전화번호',

    has_parking     CHAR(1)      DEFAULT 'N' COMMENT '주차 여부(Y/N)',
    parking_note    VARCHAR(200) NULL COMMENT '주차 설명',

    use_yn          CHAR(1)      DEFAULT 'Y' COMMENT '사용 여부(Y/N)',

    source_url      VARCHAR(300) NULL COMMENT '자료 출처 URL',
    memo            VARCHAR(300) NULL COMMENT '메모',

    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_city(city),
    INDEX idx_name(name_ko),
    INDEX idx_location(lat, lng)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
