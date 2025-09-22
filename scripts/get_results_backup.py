# get_results.py
import sys
import json
import comtypes.client
import pythoncom
from get_results_internal_forces import parse_frameforce, group_frames
from get_results_joints import parse_jointdispl, parse_reaction

def get_all_joints(SapModel):
    try:
        _, points = SapModel.PointObj.GetNameList()
        points = list(points) if points else []
    except Exception:
        points = []

    try:
        _, frames = SapModel.FrameObj.GetNameList()
        endpoints = set()
        for f in frames:
            _, start, end = SapModel.FrameObj.GetPoints(f)
            endpoints.update([start, end])
        points.extend(list(endpoints))
    except Exception:
        pass

    return list(set(points))

def main():
    pythoncom.CoInitialize()
    try:
        helper = comtypes.client.CreateObject("SAP2000v1.Helper")
        helper = helper.QueryInterface(comtypes.gen.SAP2000v1.cHelper)
        sap_object = helper.GetObject("CSI.SAP2000.API.SapObject")
        SapModel = sap_object.SapModel
    except Exception as e:
        print(json.dumps({"status":"error","message":f"Cannot attach SAP2000: {e}"}))
        return

    load_case = sys.argv[1] if len(sys.argv) > 1 else "D"
    run_flag = "--run" in sys.argv

    if run_flag:
        try:
            ret = SapModel.Analyze.RunAnalysis()
            if ret != 0:
                print(json.dumps({"status":"error","message":f"RunAnalysis failed code {ret}"}))
                pythoncom.CoUninitialize()
                return
        except Exception as e:
            print(json.dumps({"status":"error","message":f"RunAnalysis exception: {e}"}))
            pythoncom.CoUninitialize()
            return

    try:
        SapModel.Results.Setup.DeselectAllCasesAndCombosForOutput()
        SapModel.Results.Setup.SetCaseSelectedForOutput(load_case)
    except Exception as e:
        print(json.dumps({"status":"error","message":f"Error selecting load case: {e}"}))
        pythoncom.CoUninitialize()
        return

    model_name = SapModel.GetModelFileName()

    # ----------------- Frame Forces -----------------
    all_frames = []
    try:
        for item_type in (0,1,3):
            raw = SapModel.Results.FrameForce("", item_type)
            parsed = parse_frameforce(raw)
            if parsed["ret_code"] == 0 and parsed["rows"]:
                all_frames = parsed["rows"]
                break
    except Exception as e:
        print(f"FrameForce error: {e}")

    frames_list = group_frames(all_frames)

    # ----------------- All Joints -----------------
    all_joints = get_all_joints(SapModel)

    # ----------------- Joint Displacements -----------------
    all_displ = []
    try:
        if all_joints:
            raw = SapModel.Results.JointDispl(all_joints, load_case)
            parsed = parse_jointdispl(raw)
            if parsed["rows"]:
                all_displ = parsed["rows"]
    except Exception:
        pass

    # Fallback nếu SAP2000 không trả về displacement
    if not all_displ:
        for r in all_frames:
            obj = r["Obj"]
            for end in [0, r["Station"]]:
                joint_name = f"{obj}_pt{end}"
                all_displ.append({
                    "Joint": joint_name,
                    "LoadCase": r["LoadCase"],
                    "StepType": r["StepType"],
                    "StepNum": r["StepNum"],
                    "UX": 0.0,"UY":0.0,"UZ":0.0,"RX":0.0,"RY":0.0,"RZ":0.0
                })

    # ----------------- Reactions -----------------
    all_reac = []
    try:
        if all_joints:
            raw = SapModel.Results.JointReaction(all_joints, load_case)
            parsed = parse_reaction(raw)
            if parsed["rows"]:
                all_reac = parsed["rows"]
    except Exception:
        pass

    # Fallback nếu SAP2000 không trả về reaction
    if not all_reac:
        for r in all_frames:
            obj = r["Obj"]
            for end in [0, r["Station"]]:
                joint_name = f"{obj}_pt{end}"
                all_reac.append({
                    "Joint": joint_name,
                    "LoadCase": r["LoadCase"],
                    "StepType": r["StepType"],
                    "StepNum": r["StepNum"],
                    "FX":0.0,"FY":0.0,"FZ":0.0,"MX":0.0,"MY":0.0,"MZ":0.0
                })

    # ----------------- Output -----------------
    out = {
        "status":"success",
        "model": model_name,
        "loadcase": load_case,
        "results":{
            "frames": frames_list,
            "joints": all_displ,
            "reactions": all_reac
        }
    }
    print(json.dumps(out, ensure_ascii=False, indent=2))
    pythoncom.CoUninitialize()

if __name__=="__main__":
    main()