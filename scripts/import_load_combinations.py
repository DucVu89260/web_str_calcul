# import_load_combinations.py
import sys
import csv
import re
import comtypes.client
import time

def get_sap_object():
    """Kết nối tới SAP2000 qua comtypes"""
    try:
        # Thử kết nối tới SAP2000 đang chạy
        print("Đang thử kết nối tới SAP2000 đang chạy...")
        try:
            SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
            print("✅ Kết nối tới instance SAP2000 đang chạy.")
        except:
            print("⚠️ Không tìm thấy instance SAP2000 đang chạy. Tạo instance mới...")
            helper = comtypes.client.CreateObject('SAP2000v1.Helper')
            helper = helper.QueryInterface(comtypes.gen.SAP2000v1.cHelper)
            print("✅ Helper object tạo thành công.")
            SapObject = helper.CreateObjectProgID("CSI.SAP2000.API.SapObject")
            print("✅ SapObject tạo thành công.")
            SapObject.ApplicationStart(Units=6)  # 6 = kN_m_C
            print("✅ SAP2000 đã khởi động.")

        # Lấy SapModel
        SapModel = SapObject.SapModel
        print("✅ Lấy SapModel thành công.")

        # Khởi tạo model mới nếu cần
        ret = SapModel.InitializeNewModel()
        if ret != 0:
            raise RuntimeError(f"❌ Không thể khởi tạo model mới (ret={ret})")

        # Kiểm tra model locked
        is_locked = SapModel.GetModelIsLocked()
        if is_locked:
            print("⚠️ Model đang bị khóa. Hãy mở file mới thủ công.")
            sys.exit(1)
        print("✅ Model không bị khóa.")

        print("✅ Kết nối tới SAP2000 và khởi tạo model thành công.")
        return SapObject, SapModel
    except Exception as e:
        raise RuntimeError(f"❌ Lỗi khi kết nối tới SAP2000: {str(e)}\nTip: Đảm bảo SAP2000 v22 đã mở với quyền admin và blank model sẵn sàng.")

def parse_part(part):
    """Tách hệ số và load case từ chuỗi như '1.2DL1' hoặc '1.0WX1+,WX2-'"""
    match = re.match(r'([0-9\.]+)(.+)', part.strip())
    if not match:
        raise ValueError(f"⚠️ Không đọc được phần '{part}'")
    coef = float(match.group(1))
    cases_str = match.group(2)
    cases = [c.strip() for c in cases_str.split(',') if c.strip()]
    return coef, cases

def ensure_load_cases(SapModel, load_cases):
    """Đảm bảo các load case tồn tại (tạo nếu chưa có)"""
    for case in load_cases:
        # Kiểm tra nếu case tồn tại
        ret, _, names = SapModel.LoadCases.GetNameList()
        if ret == 0 and case in names:
            print(f"✅ Load case '{case}' đã tồn tại.")
            continue
        
        # Tạo mới StaticLinear case
        ret = SapModel.LoadCases.StaticLinear.SetCase(case)
        if ret != 0:
            print(f"⚠️ Lỗi tạo load case '{case}' (ret={ret}). Kiểm tra tên hợp lệ.")
        else:
            print(f"✅ Đã tạo load case '{case}'.")

def import_combinations(csv_file, save_path=None):
    SapObject, SapModel = get_sap_object()

    # Thu thập tất cả load case từ file CSV
    all_load_cases = set()
    combo_formulas = {}
    with open(csv_file, newline='', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            combo_name = f"LC{row['No']}".strip()
            formula = row['Combination'].strip()
            combo_formulas[combo_name] = formula
            parts = formula.split('+')
            for part in parts:
                if not part.strip():
                    continue
                try:
                    _, cases = parse_part(part)
                    all_load_cases.update(cases)
                except ValueError as e:
                    print(e)
                    continue

    # Đảm bảo tất cả load case tồn tại
    ensure_load_cases(SapModel, all_load_cases)
    print(f"✅ Đã kiểm tra/tạo các load case: {', '.join(sorted(all_load_cases))}")

    # Kiểm tra tổ hợp trùng lặp
    seen_formulas = {}
    for combo_name, formula in combo_formulas.items():
        if formula in seen_formulas:
            print(f"⚠️ Tổ hợp {combo_name} trùng với {seen_formulas[formula]}: {formula}")
        else:
            seen_formulas[formula] = combo_name

    # Tạo tổ hợp tải
    for combo_name, formula in combo_formulas.items():
        parts = formula.split('+')
        load_cases = []
        factors = []

        for part in parts:
            if not part.strip():
                continue
            try:
                coef, cases = parse_part(part)
                for case in cases:
                    load_cases.append(case)
                    factors.append(coef)
            except ValueError as e:
                print(e)
                continue

        # Tạo combo tuyến tính (0 = Linear Add)
        ret = SapModel.LoadCases.LoadCombo.Add(combo_name, 0)
        if ret != 0:
            print(f"⚠️ Lỗi khi tạo combo {combo_name} (ret={ret})")
            continue

        # Gán từng load case vào combo
        for case, factor in zip(load_cases, factors):
            ret2 = SapModel.LoadCases.LoadCombo.SetCaseList(combo_name, 0, case, factor)
            if ret2 != 0:
                print(f"⚠️ Lỗi khi thêm case '{case}' với hệ số {factor} vào combo {combo_name} (ret={ret2})")

        print(f"✅ Added {combo_name}: {formula}")

    # Lưu model nếu có đường dẫn
    if save_path:
        ret = SapModel.File.Save(save_path)
        if ret == 0:
            print(f"💾 Saved model with load combos at: {save_path}")
        else:
            print(f"⚠️ Lỗi lưu model (ret={ret})")

    # Đóng kết nối an toàn
    SapObject.ApplicationExit(False)  # False = không lưu khi thoát
    print("✅ Đã đóng kết nối SAP2000.")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python import_load_combinations.py path_to_csv [save_path]")
    else:
        csv_file = sys.argv[1]
        save_path = sys.argv[2] if len(sys.argv) > 2 else None
        import_combinations(csv_file, save_path)