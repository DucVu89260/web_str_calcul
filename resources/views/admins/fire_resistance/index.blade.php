@extends('admins.layouts.master')

@push('styles')
<style>
   .section-header { margin-top: 20px; }
   #output { margin-top: 20px; border: 1px solid #ddd; padding: 15px; background-color: #f8f9fa; }
</style>
@endpush

@section('content')
   <section>
      <div class="container">
         <h1 class="text-center mb-4">Công Cụ Tính Toán Tiêu Chuẩn Phòng Cháy Chữa Cháy QCVN 06:2022/BXD</h1>
         <p class="text-muted">Dựa trên Quy chuẩn kỹ thuật quốc gia về An toàn cháy cho nhà và công trình. Nhập thông tin để tính toán các thông số liên quan đến kích thước hình học và yêu cầu PCCC.</p>

         <!-- Phần Input -->
         <div class="card mb-4">
            <div class="card-header">Nhập Thông Tin</div>
            <div class="card-body">
                  <form id="pcccForm">
                     <div class="form-group">
                        <label for="buildingType">Loại Nhà/Công Trình (Nhóm Nguy Hiểm Cháy Theo Công Năng)</label>
                        <select class="form-control" id="buildingType" required>
                              <option value="">Chọn loại</option>
                              <option value="F1.1">F1.1 - Nhà ở tập thể (ký túc xá, khách sạn)</option>
                              <option value="F1.2">F1.2 - Nhà chung cư</option>
                              <option value="F1.3">F1.3 - Nhà ở riêng lẻ</option>
                              <option value="F2">F2 - Công trình công cộng (rạp hát, bảo tàng)</option>
                              <option value="F3">F3 - Công trình thương mại (cửa hàng, chợ)</option>
                              <option value="F4">F4 - Công trình giáo dục, y tế</option>
                              <option value="F5">F5 - Nhà sản xuất, kho</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label for="fireResistanceLevel">Bậc Chịu Lửa</label>
                        <select class="form-control" id="fireResistanceLevel" required>
                              <option value="">Chọn bậc</option>
                              <option value="I">I</option>
                              <option value="II">II</option>
                              <option value="III">III</option>
                              <option value="IV">IV</option>
                              <option value="V">V</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label for="height">Chiều Cao PCCC (m)</label>
                        <input type="number" class="form-control" id="height" min="0" step="0.1" placeholder="Nhập chiều cao PCCC" required>
                     </div>
                     <div class="form-group">
                        <label for="floorArea">Diện Tích Sàn Một Tầng (m²)</label>
                        <input type="number" class="form-control" id="floorArea" min="0" step="0.1" placeholder="Nhập diện tích sàn" required>
                     </div>
                     <div class="form-group">
                        <label for="numFloors">Số Tầng</label>
                        <input type="number" class="form-control" id="numFloors" min="1" placeholder="Nhập số tầng" required>
                     </div>
                     <div class="form-group">
                        <label for="numBasements">Số Tầng Hầm</label>
                        <input type="number" class="form-control" id="numBasements" min="0" max="3" placeholder="Nhập số tầng hầm (0-3)">
                     </div>
                     <div class="form-group">
                        <label for="numPeoplePerFloor">Số Người Lớn Nhất Một Tầng</label>
                        <input type="number" class="form-control" id="numPeoplePerFloor" min="0" placeholder="Nhập số người">
                     </div>
                     <button type="submit" class="btn btn-primary btn-block">Tính Toán</button>
                  </form>
            </div>
         </div>

         <!-- Phần Output -->
         <div id="output" class="card" style="display: none;">
            <div class="card-header">Kết Quả Tính Toán</div>
            <div class="card-body">
                  <ul id="resultList" class="list-group"></ul>
            </div>
         </div>
      </div>

      <!-- Phần tính toán Am/V -->
      <div class="card mb-4">
         <div class="card-header">Tính Toán Hệ Số Tiết Diện Am/V</div>
         <div class="card-body">
            <form id="amvForm">
               <div class="form-row">
                  <div class="form-group col-md-3">
                     <label for="sectionType">Loại Tiết Diện</label>
                     <select class="form-control" id="sectionType" required>
                        <option value="">Chọn loại tiết diện</option>
                        <option value="H">H / I</option>
                        <option value="Tube">Ống</option>
                     </select>
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="h1">Bụng 1 h1 (mm)</label>
                     <input type="number" class="form-control" id="h1">
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="h2">Bụng 2 h2 (mm)</label>
                     <input type="number" class="form-control" id="h2">
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="b">Bề rộng cánh b (mm)</label>
                     <input type="number" class="form-control" id="b">
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="tw">Chiều dày bụng tw (mm)</label>
                     <input type="number" class="form-control" id="tw">
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="tf">Chiều dày cánh tf (mm)</label>
                     <input type="number" class="form-control" id="tf">
                  </div>
                  <div class="form-group col-md-2 section-H">
                     <label for="faces">Số mặt lộ lửa</label>
                     <input type="number" class="form-control" id="faces" min="1" max="4" value="4">
                  </div>

                  <div class="form-group col-md-2 section-Tube" style="display:none;">
                     <label for="dOuter">Đường kính ngoài D (mm)</label>
                     <input type="number" class="form-control" id="dOuter">
                  </div>
                  <div class="form-group col-md-2 section-Tube" style="display:none;">
                     <label for="tWall">Độ dày t (mm)</label>
                     <input type="number" class="form-control" id="tWall">
                  </div>
               </div>
               <button type="submit" class="btn btn-success">Tính Am/V</button>
            </form>
         </div>
      </div>

      <!-- Bảng kết quả Am/V -->
      <div id="amvOutput" class="card" style="display:none;">
         <div class="card-header">Kết Quả Am/V</div>
         <div class="card-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th>STT</th>
                     <th>Tiết diện</th>
                     <th>Am (m)</th>
                     <th>V (m²)</th>
                     <th>Am/V (m⁻¹)</th>
                     <th>Kết luận</th>
                  </tr>
               </thead>
               <tbody id="amvResultTable"></tbody>
            </table>
         </div>
      </div>

   </section>
@endsection

@push('scripts')
<script>
let amvIndex = 1;

document.getElementById('sectionType').addEventListener('change', function() {
   const type = this.value;
   document.querySelectorAll('.section-H').forEach(el => el.style.display = type === 'H' ? 'block' : 'none');
   document.querySelectorAll('.section-Tube').forEach(el => el.style.display = type === 'Tube' ? 'block' : 'none');
});

// Tính Am/V
document.getElementById('amvForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const type = document.getElementById('sectionType').value;
  let am, v, label, amv;

  if(type === 'H'){
      const h1_mm = parseFloat(document.getElementById('h1').value);
      const h2_mm = parseFloat(document.getElementById('h2').value);
      const b_mm = parseFloat(document.getElementById('b').value);
      const tw_mm = parseFloat(document.getElementById('tw').value);
      const tf_mm = parseFloat(document.getElementById('tf').value);
      const faces = parseInt(document.getElementById('faces').value);

      const h_avg_mm = (h1_mm + h2_mm) / 2;
      const h = h_avg_mm / 1000;
      const b = b_mm / 1000;
      const tw = tw_mm / 1000;
      const tf = tf_mm / 1000;

      am = 2 * h + faces * b - 2 * tw;
      v = tw * (h - 2 * tf) + 2 * b * tf;
      amv = (am / v).toFixed(3);
      label = `H(${h1_mm}-${h2_mm})*${b_mm}*${tw_mm}*${tf_mm} (${faces} faces)`;
  } else if(type === 'Tube'){
      const d_mm = parseFloat(document.getElementById('dOuter').value);
      const t_mm = parseFloat(document.getElementById('tWall').value);

      const am_mm = Math.PI * d_mm;
      const outer = d_mm + 2;
      const inner = d_mm - 2 * t_mm + 2;
      const diff = Math.pow(outer, 2) - Math.pow(inner, 2);
      const v_temp = Math.PI * diff / 1000;
      am = am_mm.toFixed(3);
      v = v_temp.toFixed(3);
      const amv_num = am_mm / v_temp;
      amv = amv_num.toFixed(3);
      label = `Tube D${d_mm}xt${t_mm}`;
  } else {
      alert('Vui lòng chọn loại tiết diện');
      return;
  }

  const result = `
    <tr>
      <td>${amvIndex++}</td>
      <td>${label}</td>
      <td>${am}</td>
      <td>${v}</td>
      <td>${amv}</td>
      <td>${parseFloat(amv) < 250 ? 'ĐẠT' : 'KHÔNG ĐẠT'}</td>
    </tr>
  `;
  document.getElementById('amvResultTable').innerHTML += result;
  document.getElementById('amvOutput').style.display = 'block';
});
</script>
@endpush