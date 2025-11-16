DROP TABLE IF EXISTS passenger_fare;

CREATE TABLE passenger_fare (
    fare_id          INT AUTO_INCREMENT PRIMARY KEY COMMENT '승객 운임 고유 ID',

    -- 어떤 항로에 대한 요금인지
    route_code       VARCHAR(20) NOT NULL COMMENT 'routes.route_code 참조',

    -- 승객 구분
    passenger_type   VARCHAR(30) NOT NULL COMMENT '승객 구분 (성인, 소인, 경로, 장애인, 도서주민 등)',
    age_group        VARCHAR(20) NULL COMMENT '연령대 (성인, 소인, 유아 등)',
    resident_type    VARCHAR(20) NULL COMMENT '거주 구분 (도서주민, 일반, 관광객 등)',
    discount_type    VARCHAR(30) NULL COMMENT '할인 구분 (경로우대, 단체, 국가유공자 등)',

    -- 요금 구조
    fare_oneway      INT NOT NULL COMMENT '편도 기본 운임(원)',
    fare_round       INT NULL COMMENT '왕복 운임(원, 제공 시)',

    -- 상세 항목 (분리해서 보고 싶을 때 사용)
    fuel_surcharge   INT NULL COMMENT '유류할증료(원)',
    port_fee         INT NULL COMMENT '항만이용료(원)',

    total_oneway     INT NULL COMMENT '편도 총액(원, base+할증+항만료)',
    total_round      INT NULL COMMENT '왕복 총액(원)',

    -- 조건
    season_type      VARCHAR(20) DEFAULT '연중' COMMENT '시즌 구분 (연중/동절기/하절기 등)',
    weekday_type     VARCHAR(20) DEFAULT '매일' COMMENT '요일 구분 (매일/평일/주말 등)',

    notes            VARCHAR(200) NULL COMMENT '비고',
    use_yn           CHAR(1) DEFAULT 'Y' COMMENT '사용 여부',

    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_route(route_code),
    INDEX idx_passenger(passenger_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO passenger_fare 
(route_code,passenger_type,age_group,resident_type,discount_type,fare_oneway,fare_round,fuel_surcharge,port_fee,total_oneway,total_round,season_type,weekday_type,notes,use_yn)
VALUES
('RT-AMTAE-BIGEUM','성인','성인','일반',NULL,8000,15000,1000,500,9500,18500,'연중','매일',NULL,'Y'),
('RT-AMTAE-BIGEUM','소인','소인','일반',NULL,4000,7500,500,300,4800,8300,'연중','매일',NULL,'Y'),
('RT-AMTAE-BIGEUM','성인','성인','도서주민','도서민할인',5000,9000,0,0,5000,9000,'연중','매일',"도서주민 할인요금",'Y'),
('RT-GARONG-SINI','성인','성인','일반',NULL,9000,17000,1000,500,10500,19500,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','소인','소인','일반',NULL,4500,8500,500,300,5300,9300,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','경로','성인','일반','경로우대',6000,11000,1000,500,7500,13500,'연중','매일',"만65세 이상",'Y');