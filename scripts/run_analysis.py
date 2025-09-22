# run_analysis.py
import sys
import json
import comtypes.client
import pythoncom

def safe_int(val, default=0):
    try:
        return int(val)
    except Exception:
        return default

def safe_float(val, default=0.0):
    try:
        return float(val)
    except Exception:
        return default

def safe_unpack(res, expected):
    """Handle both (ret, ...) and (...) cases from SAP2000 API"""
    if len(res) == expected + 1:
        return res[0], res[1:]
    elif len(res) == expected:
        return 0, res
    else:
        raise Exception(f"Unexpected return length {len(res)} (expected {expected} or {expected+1})")

def main():
    pythoncom.CoInitialize()
    try:
        SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
        SapModel = SapObject.SapModel

        current_model = SapModel.GetModelFileName()

        # LoadCase/Combo từ argv[1] nếu có
        loadcase = sys.argv[1] if len(sys.argv) > 1 else "D"

        # Chạy phân tích
        ret = SapModel.Analyze.RunAnalysis()
        if ret != 0:
            raise Exception(f"Analysis failed with code {ret}")

        # Setup case cho output
        SapModel.Results.Setup.DeselectAllCasesAndCombosForOutput()
        SapModel.Results.Setup.SetCaseSelectedForOutput(loadcase)

        # --- Lấy Selection ---
        sel_res = SapModel.SelectObj.GetSelected()
        if len(sel_res) == 5:
            ret, NumberItems, ObjTypes, ObjNames, Selected = sel_res
        elif len(sel_res) == 4:
            NumberItems, ObjTypes, ObjNames, Selected = sel_res
            ret = 0
        else:
            raise Exception(f"Unexpected GetSelected length: {len(sel_res)}")

        frames = []
        joints = []
        reactions = []

        for i in range(NumberItems):
            obj_type = safe_int(ObjTypes[i])
            obj_name = str(ObjNames[i])

            # --- FRAME FORCES ---
            if obj_type == 2:  # Frame
                ret, values = safe_unpack(
                    SapModel.Results.FrameForce(obj_name, 0), 12
                )
                NumberResults, Obj, Elm, LoadCase, StepType, StepNum, \
                P, V2, V3, T, M2, M3 = values

                forces = []
                for j in range(NumberResults):
                    forces.append({
                        "LoadCase": str(LoadCase[j]),
                        "StepType": str(StepType[j]),
                        "StepNum": safe_float(StepNum[j]),
                        "P": safe_float(P[j]),
                        "V2": safe_float(V2[j]),
                        "V3": safe_float(V3[j]),
                        "T": safe_float(T[j]),
                        "M2": safe_float(M2[j]),
                        "M3": safe_float(M3[j]),
                    })
                frames.append({"name": obj_name, "forces": forces})

            # --- JOINT DISPLACEMENTS ---
            if obj_type == 1:  # Joint
                ret, values = safe_unpack(
                    SapModel.Results.JointDispl(obj_name), 12
                )
                NumberResults, Obj, Elm, LoadCase, StepType, StepNum, \
                UX, UY, UZ, RX, RY, RZ = values

                displs = []
                for j in range(NumberResults):
                    displs.append({
                        "LoadCase": str(LoadCase[j]),
                        "StepType": str(StepType[j]),
                        "StepNum": safe_float(StepNum[j]),
                        "UX": safe_float(UX[j]),
                        "UY": safe_float(UY[j]),
                        "UZ": safe_float(UZ[j]),
                        "RX": safe_float(RX[j]),
                        "RY": safe_float(RY[j]),
                        "RZ": safe_float(RZ[j]),
                    })
                joints.append({"name": obj_name, "displacements": displs})

                # --- JOINT REACTIONS ---
                ret, values = safe_unpack(
                    SapModel.Results.JointReact(obj_name), 12
                )
                NumberResults, Obj, Elm, LoadCase, StepType, StepNum, \
                FX, FY, FZ, MX, MY, MZ = values

                reacts = []
                for j in range(NumberResults):
                    reacts.append({
                        "LoadCase": str(LoadCase[j]),
                        "StepType": str(StepType[j]),
                        "StepNum": safe_float(StepNum[j]),
                        "FX": safe_float(FX[j]),
                        "FY": safe_float(FY[j]),
                        "FZ": safe_float(FZ[j]),
                        "MX": safe_float(MX[j]),
                        "MY": safe_float(MY[j]),
                        "MZ": safe_float(MZ[j]),
                    })
                if reacts:
                    reactions.append({"name": obj_name, "reactions": reacts})

        # --- OUTPUT JSON ---
        output = {
            "status": "success",
            "model": current_model,
            "loadcase": loadcase,
            "summary": {
                "total_frames": SapModel.FrameObj.Count(),
                "total_joints": SapModel.PointObj.Count(),
                "selected_items": NumberItems,
                "frames_selected": len(frames),
                "joints_selected": len(joints),
            },
            "results": {
                "frames": frames,
                "joints": joints,
                "reactions": reactions
            }
        }
        print(json.dumps(output, ensure_ascii=False, indent=2))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}, ensure_ascii=False))
    finally:
        pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()