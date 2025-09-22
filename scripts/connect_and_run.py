# connect_and_run.py
import sys
import json
import comtypes.client
import pythoncom
import os

DEBUG = "--debug" in sys.argv

def normalize_getnamelist(raw, label):
    try:
        res = list(raw)
        if DEBUG:
            print(f"[DEBUG] {label} raw:", res, file=sys.stderr)  # gửi ra stderr, không phá JSON stdout

        ret = None
        names = []
        warnings = []

        if len(res) >= 3 and isinstance(res[2], (list, tuple)):
            ret, count, names = res[0], res[1], res[2]
        elif len(res) == 3 and isinstance(res[1], (list, tuple)):
            ret, names, _ = res
        elif len(res) == 2 and isinstance(res[1], (list, tuple)):
            ret, names = res
        else:
            warnings.append(f"{label} unexpected format: {res}")

        if ret is not None and ret != 0:
            warnings.append(f"{label} returned ret={ret}")

        names = list(names) if names else []
        return names, warnings
    except Exception as e:
        return [], [f"{label} exception: {e}"]

def main():
    pythoncom.CoInitialize()
    try:
        model_path = sys.argv[1] if len(sys.argv) > 1 else None
        if not model_path:
            raise Exception("Model path is required")

        SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
        SapModel = SapObject.SapModel

        # lấy model hiện tại
        current_model = SapModel.GetModelFileName()
        if not current_model or os.path.normcase(current_model) != os.path.normcase(model_path):
            ret = SapModel.File.OpenFile(model_path)
            if ret != 0:
                raise Exception(f"Failed to open model: {model_path}")
            current_model = model_path

        # chạy phân tích
        ret = SapModel.Analyze.RunAnalysis()
        if ret != 0:
            raise Exception(f"Analysis failed with code {ret}")

        # load cases
        cases, err_cases = normalize_getnamelist(SapModel.LoadCases.GetNameList(), "LoadCases")
        # load combos
        combos, err_combos = normalize_getnamelist(SapModel.RespCombo.GetNameList(), "LoadCombos")

        output = {
            "status": "success",
            "model": current_model,
            "load_cases": cases,
            "load_combinations": combos,
        }
        if err_cases or err_combos:
            output["warnings"] = [m for m in (err_cases, err_combos) if m]

        print(json.dumps(output, ensure_ascii=False, indent=2))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}, ensure_ascii=False))
    finally:
        pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()