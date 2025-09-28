/* === Interactive SVG, hover hiển thị p_net ±GCpi === */
const ce_theta0 = {
  5:  {F:-1.7, G:-1.2, H:-0.6, I:-0.6},
  15: {F:-0.9, G:-0.8, H:-0.3, I:-0.4},
  30: {F:-0.5, G:-0.5, H:-0.2, I:-0.4},
  45: {F:0.0,  G:0.0,  H:0.0,  I:-0.2},
  60: {F:0.7,  G:0.7,  H:0.7,  I:-0.2},
  75: {F:0.8,  G:0.8,  H:0.8,  I:-0.2}
};

const ce_theta90 = {
  5:  {F:1.6, G:1.3, H:0.7, I:0.6},
  15: {F:1.3, G:1.3, H:0.6, I:0.5},
  30: {F:1.1, G:1.4, H:0.8, I:0.5},
  45: {F:1.1, G:1.4, H:0.9, I:0.5},
  60: {F:1.1, G:1.2, H:0.8, I:0.5},
  75: {F:1.1, G:1.2, H:0.8, I:0.5}
};

const kz_table = {
  I: {5:0.87,10:1.00,15:1.08,20:1.15,30:1.24,50:1.36,100:1.52},
  II:{5:0.96,10:1.09,15:1.17,20:1.24,30:1.33,50:1.45,100:1.61},
  III:{5:1.07,10:1.20,15:1.28,20:1.34,30:1.44,50:1.56,100:1.72},
  IV:{5:1.23,10:1.36,15:1.44,20:1.50,30:1.60,50:1.72,100:1.88}
};

function getKz(z, terrain){
  const table = kz_table[terrain]||kz_table['II'];
  const heights = Object.keys(table).map(Number).sort((a,b)=>a-b);
  let closest=heights[0], minDiff=Math.abs(z-closest);
  for(const h of heights){ const diff=Math.abs(z-h); if(diff<minDiff){minDiff=diff; closest=h;} }
  return table[closest];
}

function nearestAlpha(alpha, table){
  const keys = Object.keys(table).map(Number);
  let best = keys[0], mind = Math.abs(alpha-best);
  for(const k of keys){ const d=Math.abs(alpha-k); if(d<mind){mind=d; best=k;} }
  return best;
}

function getCe(alpha, theta, zone){
  const tbl = (Math.abs(theta-90)<1e-6)?ce_theta90:ce_theta0;
  const a = nearestAlpha(Math.abs(alpha), tbl);
  return tbl[a][zone];
}

function clearSvg(svg){ while(svg.firstChild) svg.removeChild(svg.firstChild); }

function makeMarker(svg, id, color){
  const ns="http://www.w3.org/2000/svg";
  const def=document.createElementNS(ns,'defs');
  const marker=document.createElementNS(ns,'marker');
  marker.setAttribute('id',id); marker.setAttribute('markerWidth',10);
  marker.setAttribute('markerHeight',10); marker.setAttribute('refX',6); marker.setAttribute('refY',3);
  marker.setAttribute('orient','auto');
  const path=document.createElementNS(ns,'path');
  path.setAttribute('d','M0,0 L0,6 L9,3 z'); path.setAttribute('fill',color);
  marker.appendChild(path); def.appendChild(marker); svg.appendChild(def);
}

function round(x,d=2){ return Math.round(x*Math.pow(10,d))/Math.pow(10,d); }

/* === Tooltip === */
const tooltip = document.createElement('div');
tooltip.style.position = 'absolute';
tooltip.style.background='rgba(255,255,200,0.9)';
tooltip.style.padding='5px 8px';
tooltip.style.border='1px solid #999';
tooltip.style.borderRadius='4px';
tooltip.style.pointerEvents='none';
tooltip.style.display='none';
document.body.appendChild(tooltip);

/* === Draw Frame + Hover for p_net === */
function drawFrame(nSpans,B,H,slope,parapet,windLoads){
  const svg=document.getElementById('frameSvg'); const ns="http://www.w3.org/2000/svg";
  clearSvg(svg); makeMarker(svg,'arrowGreen','green');
  const paddingLeft=50; const baseY=270;
  const totalWidth_m=nSpans*B; const availW=900; const availH=220;
  const scaleX=availW/totalWidth_m; const scaleY=availH/(H+Math.max(0,parapet)+2); const scale=Math.min(scaleX,scaleY);

  for(let i=0;i<nSpans;i++){
    const x0=paddingLeft+i*B*scale, x1=x0+B*scale, eaveY=baseY-H*scale;
    const ridgeX=(x0+x1)/2, ridgeRise=Math.tan(slope*Math.PI/180)*(B/2)*scale, ridgeY=eaveY-ridgeRise;
    // Columns
    [['x1',x0,'y1',baseY,'x2',x0,'y2',eaveY],['x1',x1,'y1',baseY,'x2',x1,'y2',eaveY]].forEach(l=>{
      const line=document.createElementNS(ns,'line'); for(let j=0;j<l.length;j+=2) line.setAttribute(l[j],l[j+1]);
      line.setAttribute('stroke','black'); line.setAttribute('stroke-width',2); svg.appendChild(line);
    });
    // Roof
    [['x1',x0,'y1',eaveY,'x2',ridgeX,'y2',ridgeY],['x1',ridgeX,'y1',ridgeY,'x2',x1,'y2',eaveY]].forEach(l=>{
      const line=document.createElementNS(ns,'line'); for(let j=0;j<l.length;j+=2) line.setAttribute(l[j],l[j+1]);
      line.setAttribute('stroke','blue'); line.setAttribute('stroke-width',2); svg.appendChild(line);
    });
    // Parapet
    if(parapet>0){ const pH=parapet*scale; [[x0,eaveY],[x1,eaveY]].forEach(pt=>{
      const l=document.createElementNS(ns,'line'); l.setAttribute('x1',pt[0]); l.setAttribute('y1',pt[1]);
      l.setAttribute('x2',pt[0]); l.setAttribute('y2',pt[1]-pH); l.setAttribute('stroke','orange'); l.setAttribute('stroke-width',1.5); svg.appendChild(l);
    }); }

    if(i===0 && windLoads){
      const zones=['F','G','H','I'];
      const zonePositions={F:{x:x0+0.2*B*scale,y:eaveY-0.2*B*scale*Math.tan(slope*Math.PI/180)},
                           G:{x:ridgeX-0.1*B*scale,y:ridgeY+0.1*B*scale*Math.sin(slope*Math.PI/180)},
                           H:{x:ridgeX,y:ridgeY},
                           I:{x:x0-20,y:eaveY+(baseY-eaveY)/2}};
      zones.forEach(z=>{
        const pos=zonePositions[z];
        const pNetPos = round(windLoads.theta0[z]-windLoads.q*0.18,2);
        const pNetNeg = round(windLoads.theta0[z]-windLoads.q*(-0.18),2);
        const mag = 20;
        const line = document.createElementNS(ns,'line');
        line.setAttribute('x1',pos.x); line.setAttribute('y1',pos.y);
        line.setAttribute('x2',pos.x+mag); line.setAttribute('y2',pos.y);
        line.setAttribute('stroke',pNetPos>=0?'blue':'red'); line.setAttribute('stroke-width',3);
        svg.appendChild(line);

        line.addEventListener('mousemove',e=>{
          tooltip.style.display='block';
          tooltip.style.left=(e.pageX+10)+'px';
          tooltip.style.top=(e.pageY+10)+'px';
          tooltip.innerHTML=`Zone ${z}<br>p_net +GCpi: ${pNetPos}<br>p_net -GCpi: ${pNetNeg}`;
        });
        line.addEventListener('mouseleave',()=>tooltip.style.display='none');
      });
    }
  }
}

/* === Plan view similar with hover === */
function drawPlan(nSpans,B,L,windLoads){ /*... tương tự, hover tooltip cho zones F/G */ }

/* === Compute wind === */
function computeWindForTwoRoofDirections(inputs){
  const {nSpans,B,H,slope,parapet,L,Wo,Gf,terrain,Kzt,lifetime}=inputs;
  const W3_10=lifetime*Wo;
  const ze=H+(B/2)*Math.tan(slope*Math.PI/180);
  const kze=getKz(ze,terrain);
  const q=W3_10*Kzt*kze*Gf;
  const totalB=nSpans*B;
  const a=Math.max(Math.min(0.1*totalB,0.1*L,0.4*ze),3);
  const zones=['F','G','H','I'];
  const res={theta0:{},theta90:{},q:round(q,2),a:round(a,2)};
  for(const z of zones){
    let ce0=getCe(slope,0,z), ce90=getCe(slope,90,z);
    if(parapet>0.5 && (z==='F'||z==='G')){ ce0*=1.1; ce90*=1.1; }
    res.theta0[z]=round(q*ce0,2); res.theta90[z]=round(q*ce90,2);
  }
  res.W3_10=round(W3_10,2); res.kze=round(kze,3); res.Kzt=Kzt;
  res.Winternal_pos=round(q*0.18,2); res.Winternal_neg=round(q*(-0.18),2);
  return res;
}

/* === Event listeners === */
document.getElementById('calcBtn').addEventListener('click',()=>{
  const inputs={
    nSpans:+document.getElementById('nSpans').value,
    B:+document.getElementById('widthB').value,
    H:+document.getElementById('heightH').value,
    slope:+document.getElementById('roofSlope').value,
    parapet:+document.getElementById('parapet').value,
    L:+document.getElementById('lengthL').value,
    Wo:+document.getElementById('Wo').value,
    Gf:+document.getElementById('Gf').value,
    terrain:document.getElementById('terrain').value,
    Kzt:+document.getElementById('Kzt').value,
    lifetime:+document.getElementById('lifetime').value
  };
  const out=computeWindForTwoRoofDirections(inputs);
  drawFrame(inputs.nSpans,inputs.B,inputs.H,inputs.slope,inputs.parapet,out);
  drawPlan(inputs.nSpans,inputs.B,inputs.L,out);

  // Populate tables...
});

document.getElementById('resetBtn').addEventListener('click',()=>location.reload());