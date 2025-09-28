@extends('admins.layouts.master')

@section('content')
<div class="container-fluid mt-4">
   <h3>Thiết kế Liên kết Moment — AISC 360-10</h3>

   <div class="row">
      <!-- CỘT TRÁI: INPUT CHUNG -->
      <div class="col-md-5">
         <form method="POST" action="{{ route('connections.calculate', ['type' => 'moment', 'standard' => 'aisc360-10']) }}">
               @csrf

               {{-- ===== 1. TẢI TRỌNG & PHƯƠNG PHÁP ===== --}}
               <div class="card mb-3">
                  <div class="card-header">1. Tải trọng & Phương pháp</div>
                  <div class="card-body">
                     <div class="form-group">
                           <label>Phương pháp</label>
                           <select name="method" class="form-control">
                              <option value="lrfd">LRFD</option>
                              <option value="asd">ASD</option>
                           </select>
                     </div>
                     <div class="form-group">
                           <label>M<sub>max</sub> (kNm)</label>
                           <input name="Mmax" type="number" class="form-control" value="{{ old('Mmax', 200) }}">
                     </div>
                     <div class="form-group">
                           <label>P<sub>max</sub> (kN)</label>
                           <input name="Pmax" type="number" class="form-control" value="{{ old('Pmax', 100) }}">
                     </div>
                     <div class="form-group">
                           <label>Lever arm (mm)</label>
                           <input name="lever_arm_mm" type="number" class="form-control" value="{{ old('lever_arm_mm', 200) }}">
                     </div>
                  </div>
               </div>

               {{-- ===== 2. VẬT LIỆU & TIẾT DIỆN ===== --}}
               <div class="card mb-3">
                  <div class="card-header">2. Vật liệu & Tiết diện</div>
                  <div class="card-body">
                     <div class="form-group">
                           <label>Tiết diện dầm</label>
                           <input name="beam_section" class="form-control" value="{{ old('beam_section') }}">
                     </div>
                     <div class="form-group">
                           <label>Tiết diện cột</label>
                           <input name="column_section" class="form-control" value="{{ old('column_section') }}">
                     </div>
                     <div class="form-group">
                           <label>Fy (MPa)</label>
                           <input name="fy" type="number" class="form-control" value="{{ old('fy', 355) }}">
                     </div>
                     <div class="form-group">
                           <label>Fu (MPa)</label>
                           <input name="fu" type="number" class="form-control" value="{{ old('fu', 490) }}">
                     </div>
                     <div class="form-group">
                           <label>Độ dày tấm (mm)</label>
                           <input name="plate_t_mm" type="number" class="form-control" value="{{ old('plate_t_mm', 10) }}">
                     </div>
                  </div>
               </div>

               {{-- ===== 3. BU-LÔNG ===== --}}
               <div class="card mb-3">
                  <div class="card-header">3. Bu-lông</div>
                  <div class="card-body">
                     <div class="form-group">
                           <label>Đường kính d (mm)</label>
                           <input name="bolt_d_mm" type="number" class="form-control" value="{{ old('bolt_d_mm', 20) }}">
                     </div>
                     <div class="form-group">
                           <label>Loại bu-lông</label>
                           <select name="bolt_grade" id="bolt_grade" class="form-control">
                              <option value="A325">A325</option>
                              <option value="A490">A490</option>
                              <option value="A307">A307</option>
                              <option value="custom">Custom</option>
                           </select>
                     </div>
                     <div id="custom_fn" style="display:none;">
                           <div class="form-group">
                              <label>Fnt (MPa)</label>
                              <input name="Fnt" type="number" class="form-control" value="{{ old('Fnt') }}">
                           </div>
                           <div class="form-group">
                              <label>Fnv (MPa)</label>
                              <input name="Fnv" type="number" class="form-control" value="{{ old('Fnv') }}">
                           </div>
                     </div>
                  </div>
               </div>

               <div class="text-right mb-4">
                  <a href="{{ route('connections.index') }}" class="btn btn-secondary">Quay lại</a>
                  <button class="btn btn-success" type="submit">Tính toán (AISC 360-10)</button>
               </div>
         </form>
      </div>

      <!-- CỘT PHẢI: INPUT BỐ TRÍ + PREVIEW -->
      <div class="col-md-7">
         <div class="card mb-3">
            <div class="card-header">Bố trí bu-lông (theo hình minh họa)</div>
            <div class="card-body">
               <div class="form-row">
                  <div class="col">
                     <label>L (Plate width, mm)</label>
                     <input id="plateW" type="number" class="form-control" value="{{ old('plateW',600) }}">
                  </div>
                  <div class="col">
                     <label>Tổng số cột (X)</label>
                     <select id="totalCols" class="form-control">
                        @foreach([4,6,8,10] as $c)
                           <option value="{{ $c }}" {{ old('totalCols',8)==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col">
                     <label>Số cột bên trái</label>
                     <input id="nColsLeft" type="number" class="form-control" value="{{ old('nColsLeft',2) }}">
                  </div>
               </div>

               <div class="form-row mt-2">
                  <div class="col">
                     <label>Edge-X1 (mm)</label>
                     <input id="edgeX1" type="number" class="form-control" value="{{ old('edgeX1',40) }}">
                  </div>
                  <div class="col">
                     <label>Pitch-X1 (mm)</label>
                     <input id="pitchX1" type="number" class="form-control" value="{{ old('pitchX1',80) }}">
                  </div>
                  <div class="col">
                     <label>Dx (mm)</label>
                     <input id="Dx" type="number" class="form-control" value="{{ old('Dx',120) }}">
                  </div>
               </div>

               <div class="form-row mt-2">
                  <div class="col">
                     <label>Pitch-X2 (mm)</label>
                     <input id="pitchX2" type="number" class="form-control" value="{{ old('pitchX2',80) }}">
                  </div>
                  <div class="col">
                     <label>Số hàng bu-lông (Y)</label>
                     <input id="nRows" type="number" class="form-control" value="2" readonly>
                  </div>
                  <div class="col">
                     <label>Pitch-Y (B, mm)</label>
                     <input id="pitchY" type="number" class="form-control" value="{{ old('pitchY',80) }}">
                  </div>
               </div>

               <div class="form-row mt-2">
                  <div class="col">
                     <label>Dy (mm) (từ tâm đến mép trên)</label>
                     <input id="Dy" type="number" class="form-control" value="{{ old('Dy',150) }}">
                  </div>
                  <div class="col">
                     <label>Edge-X2 (mm) — tự tính</label>
                     <input id="edgeX2" type="text" class="form-control" readonly>
                  </div>
                  <div class="col">
                     <label>Edge-Y (mm) — tự tính từ Dy</label>
                     <input id="edgeYComputed" type="text" class="form-control" readonly>
                  </div>
               </div>

            </div>
         </div>

         <!-- PREVIEW -->
         <div class="card">
               <div class="card-header">Preview bố trí (Origin = tâm bản)</div>
               <div class="card-body text-center">
                  <svg id="boltLayout" width="100%" height="420" style="border:1px solid #ccc"></svg>
               </div>
         </div>
      </div>
   </div>
</div>

@push('scripts')
<script>
function drawBolts() {
   const plateW = parseFloat($('#plateW').val()) || 600;
   const totalCols = parseInt($('#totalCols').val()) || 8;
   let nColsLeft = parseInt($('#nColsLeft').val()) || 2;
   if (nColsLeft < 1) nColsLeft = 1;
   if (nColsLeft > totalCols - 1) nColsLeft = totalCols - 1;
   $('#nColsLeft').val(nColsLeft);
   const nColsRight = totalCols - nColsLeft;

   const edgeX1 = parseFloat($('#edgeX1').val()) || 40;
   const pitchX1 = parseFloat($('#pitchX1').val()) || 80;
   const Dx = parseFloat($('#Dx').val()) || 120;
   const pitchX2 = parseFloat($('#pitchX2').val()) || 80;

   const nRows = 2; // Fixed to 2 rows
   const pitchY = parseFloat($('#pitchY').val()) || 80;
   const Dy = parseFloat($('#Dy').val()) || 150;

   // edgeX2
   const leftLast = (nColsLeft-1)*pitchX1;
   const rightLast = (nColsRight-1)*pitchX2;
   const edgeX2 = plateW - edgeX1 - leftLast - Dx - rightLast;
   $('#edgeX2').val(edgeX2.toFixed(1));

   // plate height
   const plateH = 2*Dy;

   const svg = document.getElementById("boltLayout");
   svg.innerHTML = "";
   const margin = 100;
   const vbW = plateW + margin*2;
   const vbH = plateH + margin*2;
   svg.setAttribute('viewBox', `0 0 ${vbW} ${vbH}`);
   svg.setAttribute('preserveAspectRatio','xMidYMid meet');

   const offsetX = margin;
   const offsetY = margin;
   const originX = offsetX;
   const originY = offsetY + plateH;

   // plate
   const plate = document.createElementNS("http://www.w3.org/2000/svg","rect");
   plate.setAttribute("x", offsetX);
   plate.setAttribute("y", offsetY);
   plate.setAttribute("width", plateW);
   plate.setAttribute("height", plateH);
   plate.setAttribute("fill","#fbfbfb");
   plate.setAttribute("stroke","#333");
   svg.appendChild(plate);

   // left/right columns
   const leftPos = [];
   for(let j=0;j<nColsLeft;j++) leftPos.push(originX+edgeX1+j*pitchX1);
   const rightPos = [];
   const startRight = leftPos[leftPos.length-1]+Dx;
   for(let j=0;j<nColsRight;j++) rightPos.push(startRight+j*pitchX2);

   // rows
   const rowPos = [originY-Dy, offsetY+Dy];
   $('#edgeYComputed').val(Dy.toFixed(1));

   // bolt positions
   const boltPositions = [];
   [...leftPos,...rightPos].forEach(x=>{
      rowPos.forEach(y=>{
         boltPositions.push({x,y,xRel:x-offsetX,yRel:originY-y});
      });
   });

   const boltR = (parseFloat($('input[name="bolt_d_mm"]').val())||20)/2*0.8;
   boltPositions.forEach(pos=>{
      const bolt = document.createElementNS("http://www.w3.org/2000/svg","circle");
      bolt.setAttribute("cx",pos.x);
      bolt.setAttribute("cy",pos.y);
      bolt.setAttribute("r",boltR);
      bolt.setAttribute("fill","#a33");
      svg.appendChild(bolt);

      const label = document.createElementNS("http://www.w3.org/2000/svg","text");
      label.textContent=`(${pos.xRel},${pos.yRel})`;
      label.setAttribute("x",pos.x+boltR+4);
      label.setAttribute("y",pos.y-4);
      label.setAttribute("font-size","10");
      label.setAttribute("fill","#111");
      svg.appendChild(label);
   });

   // dimension defs
   const defs = document.createElementNS("http://www.w3.org/2000/svg","defs");
   defs.innerHTML = `<marker id="arrow" markerWidth="10" markerHeight="10" refX="5" refY="3" orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#00f"/></marker>`;
   svg.appendChild(defs);

   function drawDim(x1,y1,x2,y2,txt,align="middle"){
      const line=document.createElementNS("http://www.w3.org/2000/svg","line");
      line.setAttribute("x1",x1);line.setAttribute("y1",y1);
      line.setAttribute("x2",x2);line.setAttribute("y2",y2);
      line.setAttribute("stroke","#00f");
      line.setAttribute("marker-start","url(#arrow)");
      line.setAttribute("marker-end","url(#arrow)");
      svg.appendChild(line);

      const t=document.createElementNS("http://www.w3.org/2000/svg","text");
      t.textContent=txt;
      t.setAttribute("x",(x1+x2)/2);
      t.setAttribute("y",(y1+y2)/2-5);
      t.setAttribute("text-anchor",align);
      t.setAttribute("font-size","12");
      t.setAttribute("fill","#00f");
      svg.appendChild(t);
   }

   // dimensions
   drawDim(originX,originY+60,originX+plateW,originY+60,`B=${plateW}mm`);
   drawDim(originX-60,originY,originX-60,originY-plateH,`L=${plateH}mm`);
   drawDim(originX,originY+40,leftPos[0],originY+40,`edgeX1=${edgeX1}mm`);
   drawDim(leftPos[leftPos.length-1]+Dx+(nColsRight-1)*pitchX2,originY+40,originX+plateW,originY+40,`edgeX2=${edgeX2}mm`);
   if(nColsLeft>0 && nColsRight>0) drawDim(leftPos[leftPos.length-1],originY+20,rightPos[0],originY+20,`Dx=${Dx}mm`);
   if(nColsLeft>1) drawDim(leftPos[0],originY+20,leftPos[1],originY+20,`PitchX1=${pitchX1}`);
   if(nColsRight>1) drawDim(rightPos[0],originY+20,rightPos[1],originY+20,`PitchX2=${pitchX2}`);
   drawDim(originX-40,originY,originX-40,rowPos[0],`edgeY=${Dy}mm`);
   drawDim(originX-20,rowPos[0],originX-20,rowPos[1],`Dy=${Dy}mm`);
}

$(document).ready(function(){
    $('#bolt_grade').on('change', function(){ $('#custom_fn').toggle($(this).val()==='custom'); }).trigger('change');
    $('#plateW,#totalCols,#nColsLeft,#edgeX1,#pitchX1,#Dx,#pitchX2,#pitchY,#Dy,input[name="bolt_d_mm"]').on('input change', drawBolts);
    drawBolts();
});
</script>
@endpush
@endsection