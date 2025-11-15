import requests
from bs4 import BeautifulSoup
import csv
from datetime import datetime, timezone, timedelta

# KOMSA 여객선 실시간 운항현황 페이지
URL = "https://www.komsa.or.kr/prog/pssgCurState/kor/sub03_0202/list.do"

# 전라남도 관련 키워드 (항로명/출항지에 이 단어가 들어가면 전남 항로로 간주)
JEONNAM_KEYWORDS = [
    "목포", "완도", "여수", "고흥", "진도", "해남",
    "신안", "장흥", "보성", "광양"
]

# 한국 시간대(KST)
KST = timezone(timedelta(hours=9))


def is_jeonnam_row(route_name: str, origin: str) -> bool:
    """
    항로명(route_name)이나 출항지(origin)에
    전라남도 관련 지명이 하나라도 포함되어 있으면 True
    """
    text = f"{route_name or ''} {origin or ''}"
    return any(k in text for k in JEONNAM_KEYWORDS)


def fetch_page(url: str) -> str:
    """
    KOMSA 페이지 HTML 가져오기
    필요하면 User-Agent 헤더 추가
    """
    headers = {
        "User-Agent": "Mozilla/5.0 (compatible; JeonnamFerryBot/1.0)"
    }
    res = requests.get(url, headers=headers, timeout=10)
    res.raise_for_status()
    return res.text


def main():
    html = fetch_page(URL)
    soup = BeautifulSoup(html, "html.parser")

    # ✅ 기준시간: HTML에서 못 가져오니까 그냥 "스크립트 실행 시각(KST)" 사용
    snapshot_dt = datetime.now(KST)
    snapshot_time_str = snapshot_dt.strftime("%Y-%m-%d %H:%M")

    # 운항현황 테이블 찾기
    table = soup.find("table")
    if table is None:
        raise RuntimeError("운항현황 테이블을 찾지 못했습니다. HTML 구조를 다시 확인해 주세요.")

    tbody = table.find("tbody") or table
    rows = tbody.find_all("tr")

    data = []

    for row in rows:
        cells = [c.get_text(strip=True).replace("\xa0", " ") for c in row.find_all(["th", "td"])]

        # 헤더나 빈 행 스킵
        if not cells or "출항시각" in cells[0]:
            continue

        # 예상 컬럼 구조:
        # 0: 출항시각
        # 1: 여객선명
        # 2: 운항항로명
        # 3: 운항방향
        # 4: 출항지
        # 5: 항로구분
        # 6: 운항구분
        # 7: 사유
        # 8: 출항여부
        # 9: 면허항로
        if len(cells) < 10:
            # 구조 달라진 경우 확인용
            print("⚠️ 예상보다 컬럼 수가 적은 행 (무시):", cells)
            continue

        departure_time = cells[0]
        ship_name      = cells[1]
        route_name     = cells[2]
        direction      = cells[3]
        origin         = cells[4]
        route_type     = cells[5]
        op_type        = cells[6]
        reason         = cells[7]
        status         = cells[8]
        license_route  = cells[9]

        # 전남 항로만 필터
        if not is_jeonnam_row(route_name, origin):
            continue

        data.append([
            snapshot_time_str,
            departure_time,
            ship_name,
            route_name,
            direction,
            origin,
            route_type,
            op_type,
            reason,
            status,
            license_route
        ])

    # CSV 저장
    header = [
        "snapshot_time",   # 기준시간 (스크립트 실행 시각, KST)
        "departure_time",  # 출항시각
        "ship_name",       # 여객선명
        "route_name",      # 운항항로명
        "direction",       # 운항방향
        "origin",          # 출항지
        "route_type",      # 항로구분
        "operation_type",  # 운항구분
        "reason",          # 사유
        "status",          # 출항여부
        "license_route"    # 면허항로
    ]

    filename = "jeonnam_realtime.csv"
    with open(filename, "w", newline="", encoding="utf-8-sig") as f:
        writer = csv.writer(f)
        writer.writerow(header)
        writer.writerows(data)

    print(f"✅ 기준시간 {snapshot_time_str} 기준 전남 관련 운항현황 {len(data)}건을 {filename} 에 저장했습니다.")


if __name__ == "__main__":
    main()

