# get_results_joints.py
from results_helpers import parse_result

def parse_jointdispl(raw):
    """Parse Joint Displacements"""
    keys = ["Joint", "LoadCase", "StepType", "StepNum",
            "UX", "UY", "UZ", "RX", "RY", "RZ"]
    return parse_result(raw, keys)

def parse_reaction(raw):
    """Parse Joint Reactions"""
    keys = ["Joint", "LoadCase", "StepType", "StepNum",
            "FX", "FY", "FZ", "MX", "MY", "MZ"]
    return parse_result(raw, keys)