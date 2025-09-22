# get_results_internal_forces.py
from results_helpers import parse_result

def parse_frameforce(raw):
    keys = ["Obj", "Station", "Elm", "PointElm",
            "LoadCase", "StepType", "StepNum",
            "P", "V2", "V3", "T", "M2", "M3"]
    return parse_result(raw, keys)

def group_frames(rows):
    frames_map = {}
    for r in rows:
        obj = r.get("Obj") or "UNKNOWN"
        force = {k: r.get(k) for k in ["LoadCase","StepType","StepNum","P","V2","V3","T","M2","M3","Station","Elm","PointElm"]}
        frames_map.setdefault(obj, []).append(force)
    return [{"name": name, "forces": forces} for name, forces in frames_map.items()]