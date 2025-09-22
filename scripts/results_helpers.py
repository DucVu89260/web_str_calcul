# results_helpers.py
def to_list(x):
    if x is None:
        return []
    if isinstance(x, (list, tuple)):
        return list(x)
    return [x]

def to_int(x):
    try:
        if isinstance(x, (list, tuple)) and len(x) > 0:
            return int(x[0])
        return int(x)
    except Exception:
        return 0

def detect_ret_and_base(res):
    # res expected to be a sequence (tuple/list) returned from COM call
    if not res:
        return 0, 1, 0, 'empty'
    last = res[-1]
    first = res[0]
    # trailing integer (ret code at end)
    if isinstance(last, int):
        return int(last), 1, res[0], 'trailing'
    # leading integer (ret code at front)
    if isinstance(first, int) and not isinstance(first, bool):
        num_raw = res[1] if len(res) > 1 else 0
        return int(first), 2, num_raw, 'leading'
    # trailing tuple like (n,) at end
    if isinstance(last, (list, tuple)) and len(last) == 1 and isinstance(last[0], int):
        return int(last[0]), 1, res[0], 'trailing(tuple)'
    # leading tuple like (n,) at start
    if isinstance(first, (list, tuple)) and len(first) == 1 and isinstance(first[0], int):
        num_raw = res[1] if len(res) > 1 else 0
        return int(first[0]), 2, num_raw, 'leading(tuple)'
    # fallback
    return 0, 1, res[0] if len(res) > 0 else 0, 'assumed_trailing_default0'

def parse_result(raw, keys):
    """
    Generic parser for various Results.* COM outputs.
    raw: sequence returned from COM method
    keys: list of expected column keys
    Returns dict {ret_code, ret_location, reported_count, rows}
    """
    res = list(raw)
    ret_code, base_idx, num_raw, ret_loc = detect_ret_and_base(res)
    NumberResults = to_int(num_raw)

    data = {}
    for i, k in enumerate(keys):
        idx = base_idx + i
        if idx < len(res):
            data[k] = to_list(res[idx])
        else:
            data[k] = []

    # determine n
    lengths = [len(v) for v in data.values() if isinstance(v, list) and len(v) > 0]
    n = min(NumberResults, min(lengths)) if lengths else 0

    rows = []
    for i in range(n):
        row = {}
        for k in keys:
            arr = data.get(k, [])
            row[k] = arr[i] if i < len(arr) else None
        rows.append(row)
    return {"ret_code": ret_code, "ret_location": ret_loc, "reported_count": NumberResults, "rows": rows}