# get_selection.py
import comtypes.client
import pythoncom
import json

def safe_int(val, default=0):
    try:
        return int(val)
    except:
        return default

def safe_unpack(res, expected_5=True):
    """
    Chuẩn hóa kết quả trả về của SAP2000 API (4 hoặc 5 values).
    Trả về tuple: (ret, NumberItems, ObjTypes, ObjNames, Selected)
    """
    if len(res) == 5:
        return res
    elif len(res) == 4:
        NumberItems, ObjTypes, ObjNames, Selected = res
        return (0, NumberItems, ObjTypes, ObjNames, Selected)
    else:
        raise Exception(f"Unexpected return values: {len(res)}")

def main():
    pythoncom.CoInitialize()
    try:
        SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
        SapModel = SapObject.SapModel

        ret, NumberItems, ObjTypes, ObjNames, Selected = safe_unpack(
            SapModel.SelectObj.GetSelected()
        )

        selection = []
        for i in range(NumberItems):
            obj_type = safe_int(ObjTypes[i])
            name = str(ObjNames[i])

            if obj_type == 1:
                selection.append({"type": "Joint", "name": name})
            elif obj_type == 2:
                selection.append({"type": "Frame", "name": name})
            elif obj_type == 3:
                selection.append({"type": "Area", "name": name})
            elif obj_type == 4:
                selection.append({"type": "Solid", "name": name})
            else:
                selection.append({"type": f"ObjType {obj_type}", "name": name})

        print(json.dumps({
            "status": "success",
            "selected_count": NumberItems,
            "selection": selection
        }, ensure_ascii=False, indent=2))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}, ensure_ascii=False))
    finally:
        pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()