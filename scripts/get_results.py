# get_results.py
import sys
import json
import argparse
import comtypes.client
import pythoncom
from get_results_internal_forces import parse_frameforce, group_frames
from get_results_joints import parse_jointdispl, parse_reaction

def dbg(msg):
    """Ghi debug ra stderr để dễ dàng redirect khi chạy."""
    print(f"[DEBUG] {msg}", file=sys.stderr)

def get_all_joints(SapModel):
    points = []
    try:
        ret, number, names = SapModel.PointObj.GetNameList()
        if ret == 0 and number > 0:
            points = list(names)
    except Exception as e:
        print(f"[DEBUG] get_all_joints: PointObj.GetNameList failed: {e}")

    try:
        ret, number, frames = SapModel.FrameObj.GetNameList()
        if ret == 0 and number > 0:
            endpoints = set()
            for f in frames:
                _, start, end = SapModel.FrameObj.GetPoints(f)
                endpoints.update([start, end])
            points.extend(list(endpoints))
    except Exception as e:
        print(f"[DEBUG] get_all_joints: FrameObj.GetNameList failed: {e}")

    return list(set(points))

def main():
    pythoncom.CoInitialize()

    # --- argparse ---
    parser = argparse.ArgumentParser(description="Export frame/joint results from SAP2000 (case or combo).")
    parser.add_argument("--loadcase", help="Tên Load Case để xuất kết quả (ví dụ: D)")
    parser.add_argument("--loadcombination", help="Tên Load Combination để xuất kết quả (ví dụ: 1.4D+1.4COL)")
    parser.add_argument("--run", action="store_true", help="Chạy phân tích trước khi lấy kết quả")
    args = parser.parse_args()

    # Attach SAP
    try:
        helper = comtypes.client.CreateObject("SAP2000v1.Helper")
        helper = helper.QueryInterface(comtypes.gen.SAP2000v1.cHelper)
        sap_object = helper.GetObject("CSI.SAP2000.API.SapObject")
        SapModel = sap_object.SapModel
    except Exception as e:
        print(json.dumps({"status": "error", "message": f"Cannot attach SAP2000: {e}"}))
        pythoncom.CoUninitialize()
        return

    # Run analysis nếu cần
    if args.run:
        try:
            dbg("Running analysis (SapModel.Analyze.RunAnalysis)...")
            ret = SapModel.Analyze.RunAnalysis()
            dbg(f"RunAnalysis returned: {ret}")
            if ret != 0:
                print(json.dumps({"status": "error", "message": f"RunAnalysis failed code {ret}"}))
                pythoncom.CoUninitialize()
                return
        except Exception as e:
            print(json.dumps({"status": "error", "message": f"RunAnalysis exception: {e}"}))
            pythoncom.CoUninitialize()
            return

    # Select load (case or combo)
    load_selected = None
    load_type = None
    try:
        SapModel.Results.Setup.DeselectAllCasesAndCombosForOutput()
    except Exception as e:
        dbg(f"DeselectAllCasesAndCombosForOutput failed: {e}")

    try:
        if args.loadcase:
            dbg(f"Selecting case for output: {args.loadcase}")
            SapModel.Results.Setup.SetCaseSelectedForOutput(args.loadcase)
            load_selected = args.loadcase
            load_type = "case"
        elif args.loadcombination:
            dbg(f"Selecting combo for output: {args.loadcombination}")
            try:
                SapModel.Results.Setup.SetComboSelectedForOutput(args.loadcombination)
                load_selected = args.loadcombination
                load_type = "combo"
            except Exception as e_combo:
                dbg(f"SetComboSelectedForOutput failed: {e_combo} -> fallback try SetCaseSelectedForOutput")
                try:
                    SapModel.Results.Setup.SetCaseSelectedForOutput(args.loadcombination)
                    load_selected = args.loadcombination
                    load_type = "combo-as-case"
                except Exception as e2:
                    print(json.dumps({"status": "error", "message": f"Selecting combo failed: {e_combo}; fallback also failed: {e2}"}))
                    pythoncom.CoUninitialize()
                    return
        else:
            load_selected = "D"
            load_type = "case-default"
            dbg("No load specified, defaulting to case 'D'")
            try:
                SapModel.Results.Setup.SetCaseSelectedForOutput(load_selected)
            except Exception as e:
                dbg(f"Default SetCaseSelectedForOutput failed: {e}")

        # 👇 in debug để biết thật sự case/combo nào đang được chọn
        try:
            ret, ncase, cases = SapModel.Results.Setup.GetSelectedCaseNames()
            ret2, ncombo, combos = SapModel.Results.Setup.GetSelectedComboNames()
            dbg(f"Selected cases: {list(cases) if ncase > 0 else []}")
            dbg(f"Selected combos: {list(combos) if ncombo > 0 else []}")
        except Exception as e:
            dbg(f"GetSelectedCaseNames/GetSelectedComboNames failed: {e}")

    except Exception as e:
        print(json.dumps({"status": "error", "message": f"Error selecting load: {e}"}))
        pythoncom.CoUninitialize()
        return


    model_name = SapModel.GetModelFileName()
    dbg(f"Model file: {model_name}, selected load: {load_selected} (type: {load_type})")

    # ----------------- Frame Forces -----------------
    all_frames = []
    try:
        for item_type in (0, 1, 3):  # 0=Object, 1=Element, 3=Group
            dbg(f"Calling FrameForce with item_type={item_type}")
            try:
                raw = SapModel.Results.FrameForce("", item_type)
            except Exception as e:
                dbg(f"FrameForce call failed for item_type={item_type}: {e}")
                raw = ()
            try:
                parsed = parse_frameforce(raw)
            except Exception as e:
                dbg(f"parse_frameforce failed for item_type={item_type}: {e}")
                parsed = {"ret_code": 1, "rows": []}
            dbg(f"FrameForce parsed ret_code={parsed.get('ret_code')} rows={len(parsed.get('rows') or [])}")
            if parsed.get("ret_code") == 0 and parsed.get("rows"):
                all_frames = parsed["rows"]
                break
    except Exception as e:
        dbg(f"FrameForce overall exception: {e}")

    frames_list = group_frames(all_frames)
    dbg(f"Total frames grouped: {len(frames_list)}")

    # ----------------- All Joints -----------------
    all_joints = get_all_joints(SapModel)
    dbg(f"Total joint names found: {len(all_joints)}")

    # ----------------- Joint Displacements -----------------
    all_displ = []
    try:
        if all_joints:
            dbg(f"Calling JointDispl for load '{load_selected}'")
            try:
                raw = SapModel.Results.JointDispl(all_joints, load_selected)
            except Exception as e:
                dbg(f"JointDispl call failed with load_selected '{load_selected}': {e}")
                # thử gọi không truyền load nếu API yêu cầu khác
                try:
                    raw = SapModel.Results.JointDispl(all_joints)
                except Exception as e2:
                    dbg(f"JointDispl fallback (no load) also failed: {e2}")
                    raw = ()
            try:
                parsed = parse_jointdispl(raw)
            except Exception as e:
                dbg(f"parse_jointdispl failed: {e}")
                parsed = {"rows": []}
            if parsed.get("rows"):
                all_displ = parsed["rows"]
            dbg(f"JointDispl rows: {len(all_displ)}")
    except Exception as e:
        dbg(f"JointDispl overall exception: {e}")

    # fallback generate trivial joint entries if none
    if not all_displ:
        dbg("No joint displacements returned — building fallback joint list from frames")
        for r in all_frames:
            obj = r.get("Obj") or r.get("obj") or "UNKNOWN"
            station = r.get("Station", 0)
            for end in [0, station]:
                joint_name = f"{obj}_pt{end}"
                all_displ.append({
                    "Joint": joint_name,
                    "LoadCase": r.get("LoadCase", load_selected),
                    "StepType": r.get("StepType"),
                    "StepNum": r.get("StepNum"),
                    "UX": 0.0, "UY": 0.0, "UZ": 0.0,
                    "RX": 0.0, "RY": 0.0, "RZ": 0.0
                })
        dbg(f"Fallback joint displ count: {len(all_displ)}")

    # ----------------- Reactions -----------------
    all_reac = []
    try:
        if all_joints:
            dbg(f"Calling JointReaction for load '{load_selected}'")
            try:
                raw = SapModel.Results.JointReaction(all_joints, load_selected)
            except Exception as e:
                dbg(f"JointReaction call failed with load_selected '{load_selected}': {e}")
                try:
                    raw = SapModel.Results.JointReaction(all_joints)
                except Exception as e2:
                    dbg(f"JointReaction fallback (no load) also failed: {e2}")
                    raw = ()
            try:
                parsed = parse_reaction(raw)
            except Exception as e:
                dbg(f"parse_reaction failed: {e}")
                parsed = {"rows": []}
            if parsed.get("rows"):
                all_reac = parsed["rows"]
            dbg(f"JointReaction rows: {len(all_reac)}")
    except Exception as e:
        dbg(f"JointReaction overall exception: {e}")

    # fallback reactions if needed
    if not all_reac:
        dbg("No joint reactions returned — building fallback reaction list from frames")
        for r in all_frames:
            obj = r.get("Obj") or r.get("obj") or "UNKNOWN"
            station = r.get("Station", 0)
            for end in [0, station]:
                joint_name = f"{obj}_pt{end}"
                all_reac.append({
                    "Joint": joint_name,
                    "LoadCase": r.get("LoadCase", load_selected),
                    "StepType": r.get("StepType"),
                    "StepNum": r.get("StepNum"),
                    "FX": 0.0, "FY": 0.0, "FZ": 0.0,
                    "MX": 0.0, "MY": 0.0, "MZ": 0.0
                })
        dbg(f"Fallback reactions count: {len(all_reac)}")

    # ----------------- Output -----------------
    out = {
        "status": "success",
        "model": model_name,
        "load": load_selected,
        "load_type": load_type,
        "results": {
            "frames": frames_list,
            "joints": all_displ,
            "reactions": all_reac
        }
    }

    print(json.dumps(out, ensure_ascii=False, indent=2))
    pythoncom.CoUninitialize()

if __name__ == "__main__":
    main()