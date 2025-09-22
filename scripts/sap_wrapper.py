import comtypes.client
import pythoncom
import sys
import os
import json

def main():
    pythoncom.CoInitialize()
    try:
        if len(sys.argv) < 2:
            print(json.dumps({
                "status": "error",
                "message": "Model path argument missing"
            }))
            return

        model_path = sys.argv[1]

        try:
            SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
            SapModel = SapObject.SapModel
        except Exception as e:
            print(json.dumps({
                "status": "error",
                "message": f"Failed to attach to SAP2000: {e}"
            }))
            return

        current_model = SapModel.GetModelFileName()
        norm_current = os.path.normcase(os.path.normpath(current_model))
        norm_input = os.path.normcase(os.path.normpath(model_path))

        if norm_current != norm_input:
            print(json.dumps({
                "status": "error",
                "message": "Model mismatch",
                "opened": current_model,
                "requested": model_path
            }))
            return

        # ✅ chỉ xác nhận connect
        print(json.dumps({
            "status": "success",
            "model": current_model,
            "message": "Connected successfully"
        }))

    except Exception as e:
        print(json.dumps({
            "status": "error",
            "message": str(e)
        }))
    finally:
        pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()