# ferry — 전남 여객선 터미널 안내

카카오맵 기반으로 전라남도 여객선 터미널(소형 선착장 포함)의 위치·사진·연락처·운임을 안내합니다.

## 기능
1. **터미널 정보** — 위치(카카오맵), 사진, 연락처
2. **운임표** — 사람 / 차량 / 기타. 수동 입력 + 사진 자동 인식(Claude 비전, 초안 → 사람이 검수 후 저장)
3. **길찾기** — 카카오맵 웹 / 앱(`kakaomap://route`) 연결

## 구조
- `public/index.html` 지도·상세 화면, `public/admin.html` 입력 화면
- `api/` PHP JSON API (`index.php?r=config|terminals|photos|fares|fares_ocr`)
- `db/schema.sql` MySQL 스키마, `uploads/` 업로드 사진

## 설치
1. MySQL DB 생성 후 `db/schema.sql` 실행
2. `api/config.sample.php` → `api/config.php` 복사 후 DB, `kakao_js_key`, `admin_token`, (선택) `anthropic_key` 입력
3. 카카오 개발자 콘솔에서 JavaScript 키를 발급하고 사이트 도메인을 등록
4. `uploads/` 를 웹서버가 쓸 수 있게 권한 설정 (PHP 실행 차단용 `.htaccess` 포함)
5. `public/admin.html` 에서 관리자 비밀번호 입력 후 터미널 등록

수정 API는 `X-Admin-Token` 헤더 인증이 필요하고, 조회는 공개입니다.
