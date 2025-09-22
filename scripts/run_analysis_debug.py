import json
from pathlib import Path
from tkinter import Tk, filedialog
from sap_wrapper import SAPWrapper


def select_sdb_file():
    """Mở hộp thoại chọn file .sdb"""
    root = Tk()
    root.withdraw()
    file_path = filedialog.askopenfilename(
        title="Chọn file SAP2000 SDB",
        filetypes=[("SAP2000 Database", "*.sdb")]
    )
    return file_path


def main():
    model_path = select_sdb_file()
    if not model_path:
        print(json.dumps({"error": "Chưa chọn file"}))
        return

    if not Path(model_path).exists():
        print(json.dumps({"error": f"Không tìm thấy file: {model_path}"}))
        return

    try:
        sap = SAPWrapper(model_path)
    except Exception as e:
        print(json.dumps({"error": f"Lỗi khi attach vào SAP2000: {e}"}))
        return

    try:
        sap.run_analysis()
        summary = {
            "total_nodes": sap.SapModel.PointObj.Count(),
            "total_frames": sap.SapModel.FrameObj.Count()
        }
        print(json.dumps({"success": True, "summary": summary}))
    except Exception as e:
        print(json.dumps({"error": f"Lỗi khi phân tích: {e}"}))
    finally:
        sap.close()


if __name__ == "__main__":
    main()
