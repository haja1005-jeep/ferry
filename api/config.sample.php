<?php
// 이 파일을 config.php 로 복사해서 값을 채우세요. (config.php 는 git 에 올라가지 않습니다)
return array(
    'db_dsn'        => 'mysql:host=localhost;dbname=ferry;charset=utf8mb4',
    'db_user'       => 'ferry',
    'db_pass'       => '',
    'kakao_js_key'  => '',            // 카카오 개발자 콘솔 > JavaScript 키 (사이트 도메인 등록 필요)
    'admin_token'   => 'change-me',   // 입력/수정 화면(admin.html)에서 쓰는 비밀번호
    'anthropic_key' => '',            // 운임표 사진 자동 인식용 (비워 두면 수동 입력만 가능)
    'anthropic_model' => 'claude-sonnet-5-5',
);
