DROP TABLE IF EXISTS vehicle_fare;

CREATE TABLE vehicle_fare (
    fare_id          INT AUTO_INCREMENT PRIMARY KEY COMMENT '운임 고유 ID',

    -- 어떤 항로에 대한 차량 요금인지
    route_code       VARCHAR(20) NOT NULL COMMENT 'routes.route_code 참조',
    
    -- 차량 구분
    vehicle_type     VARCHAR(50) NOT NULL COMMENT '차량 종류 (승용차, SUV, 1톤트럭 등)',
    vehicle_subtype  VARCHAR(50) NULL COMMENT '세부 구분 (경차, 5인승, 화물1톤 등)',
    
    -- 요율 조건
    tonnage          DECIMAL(5,2) NULL COMMENT '톤수 (톤 단위, 선택값)',
    length_m         DECIMAL(5,2) NULL COMMENT '길이 기준 (미터 단위)',
    
    -- 요금 구조
    fare_oneway      INT NOT NULL COMMENT '편도 차량 운임(원)',
    fare_round       INT NULL COMMENT '왕복 운임(원, 선택값)',
    
    -- 운임 구분
    season_type      VARCHAR(20) DEFAULT '연중' COMMENT '시즌 구분 (연중/동절기/하절기 등)',
    weekday_type     VARCHAR(20) DEFAULT '매일' COMMENT '요일 구분 (매일/평일/주말 등)',
    
    -- 부가 정보
    notes            VARCHAR(200) NULL COMMENT '비고',
    use_yn           CHAR(1) DEFAULT 'Y' COMMENT '사용여부',

    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',

    INDEX idx_route(route_code),
    INDEX idx_vehicle(vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO vehicle_fare 
(route_code, vehicle_type, vehicle_subtype, tonnage, length_m, fare_oneway, fare_round, season_type, weekday_type, notes, use_yn)
VALUES
('RT-GARONG-SINI','경차',NULL,NULL,NULL,8000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','승용차',NULL,NULL,NULL,12000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','승합차',NULL,NULL,NULL,12000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','용달차','1톤',1.0,NULL,12000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','1.5톤',1.5,NULL,12000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','2.5톤',2.5,NULL,20000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','3.5톤',3.5,NULL,30000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','5톤(단축)',5.0,NULL,40000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','5톤(장축)',5.0,NULL,50000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','10톤',10,NULL,60000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','12톤',12,NULL,70000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','15톤',15,NULL,80000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','18톤',18,NULL,100000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','화물차','25톤',25,NULL,120000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','버스','중형',NULL,NULL,35000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','버스','준대형',NULL,NULL,70000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','버스','대형',NULL,NULL,150000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','오토바이','125cc 미만',NULL,NULL,1000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','오토바이','125cc 이상',NULL,NULL,3000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','오토바이',NULL,NULL,NULL,5000,NULL,'연중','매일','기타 오토바이','Y'),
('RT-GARONG-SINI','농기계','경운기',NULL,NULL,10000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','농기계','이양기',NULL,NULL,10000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','농기계','콤바인',NULL,NULL,20000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','농기계','지게차',NULL,NULL,20000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','농기계','트랙터',NULL,NULL,20000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','불도저','소',NULL,NULL,90000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','불도저','중',NULL,NULL,120000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','불도저','대',NULL,NULL,220000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','미니',NULL,NULL,10000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','02,03',NULL,NULL,80000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','04,05',NULL,NULL,100000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','06,07',NULL,NULL,160000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','08,09',NULL,NULL,180000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','포크레인','10',NULL,NULL,220000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','카고','5톤',5.0,NULL,50000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','카고','23톤',23,NULL,150000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','덤프','15톤 레미콘',15,NULL,80000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','덤프','25톤',25,NULL,120000,NULL,'연중','매일',NULL,'Y'),
('RT-GARONG-SINI','콤비','25인승 이하',NULL,NULL,35000,NULL,'연중','매일',NULL,'Y');
