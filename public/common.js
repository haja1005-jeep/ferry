// 공통: API 호출, 카카오맵 SDK 로드
const API = '../api/index.php';

async function api(resource, { method = 'GET', params = {}, body, form, token } = {}) {
  const qs = new URLSearchParams({ r: resource, ...params });
  const headers = {};
  if (token) headers['X-Admin-Token'] = token;
  let payload;
  if (form) payload = form;
  else if (body !== undefined) { headers['Content-Type'] = 'application/json'; payload = JSON.stringify(body); }
  const res = await fetch(`${API}?${qs}`, { method, headers, body: payload });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.error || `HTTP ${res.status}`);
  return data;
}

function loadKakao(appkey) {
  return new Promise((resolve, reject) => {
    if (!appkey) return reject(new Error('api/config.php 에 kakao_js_key 를 설정하세요'));
    const s = document.createElement('script');
    s.src = `https://dapi.kakao.com/v2/maps/sdk.js?appkey=${appkey}&libraries=services&autoload=false`;
    s.onload = () => kakao.maps.load(resolve);
    s.onerror = () => reject(new Error('카카오맵 SDK 로드 실패 (JavaScript 키/도메인 등록 확인)'));
    document.head.appendChild(s);
  });
}

const DEFAULT_CENTER = [34.8, 126.5]; // 전남 서남권
const won = n => Number(n).toLocaleString('ko-KR') + '원';
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const CAT_LABEL = { person: '사람', vehicle: '차량', other: '기타' };
