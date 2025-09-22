import comtypes.client
import pythoncom
import json

def main():
    pythoncom.CoInitialize()
    try:
        SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
        SapModel = SapObject.SapModel

        # Lấy Load Cases
        case_names = SapModel.LoadCases.GetNameList()[1]  # [0]=count, [1]=array

        # Lấy Load Combinations
        combo_names = SapModel.RespCombo.GetNameList()[1]

        output = {
            "status": "success",
            "load_cases": case_names,
            "load_combinations": combo_names
        }
        print(json.dumps(output, ensure_ascii=False))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}, ensure_ascii=False))
    finally:
        pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()
