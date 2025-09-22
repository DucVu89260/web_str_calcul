import comtypes.client
import pythoncom
import sys
import os

class SAPWrapper:
    def __init__(self, model_path, visible=True):
        """
        Attach vào SAP2000 đang mở sẵn model.
        Yêu cầu: DV mở đúng .sdb trước khi chạy wrapper.
        """
        pythoncom.CoInitialize()
        self.model_path = model_path
        self.visible = visible
        self.SapObject = None
        self.SapModel = None

        try:
            print("Attempting to connect to existing SAP2000 instance...")
            # 🔑 Nếu v22 mà lỗi thì thử đổi thành "CSI.SAP2000v1.SapObject"
            self.SapObject = comtypes.client.GetActiveObject("CSI.SAP2000.API.SapObject")
            self.SapObject.Visible = visible
            self.SapModel = self.SapObject.SapModel
            print("Successfully connected to existing SAP2000 instance.")
        except Exception as e:
            raise Exception(f"Failed to attach to existing SAP2000 instance: {e}")

        # Kiểm tra model đang mở có khớp không
        current_model = self.SapModel.GetModelFileName()
        norm_current = os.path.normcase(os.path.normpath(current_model))
        norm_input = os.path.normcase(os.path.normpath(model_path))

        if norm_current != norm_input:
            raise Exception(
                f"Current model mismatch.\n"
                f"Opened in SAP2000: {current_model}\n"
                f"Requested: {model_path}"
            )

        print(f"Using model: {current_model}")

    def run_analysis(self):
        print("Running analysis...")
        ret = self.SapModel.Analyze.RunAnalysis()
        if ret == 0:
            print("Analysis completed successfully.")
        else:
            raise Exception(f"Analysis failed with code {ret}")

    def close(self):
        if self.SapModel:
            self.SapModel.File.Save()
        # ❌ KHÔNG gọi ApplicationExit khi attach vào phiên đã mở sẵn
        pythoncom.CoUninitialize()
        print("Detached from SAP2000 (model still open).")
