/* ================= Utility ================= */
function round(x, d = 2) {
  return Math.round(x * Math.pow(10, d)) / Math.pow(10, d);
}

function getInputs() {
  const inputs = {
    nSpans: +document.getElementById('nSpans').value,
    B: +document.getElementById('B').value,
    H: +document.getElementById('H').value,
    slope: +document.getElementById('slope').value,
    parapet: +document.getElementById('parapet').value,
    L: +document.getElementById('L').value,
    Wo: +document.getElementById('Wo').value,
    C: +document.getElementById('C').value,
    Gf: +document.getElementById('Gf').value,
    terrain: document.getElementById('terrain').value,
    Kzt: +document.getElementById('Kzt').value,
    lifetime: +document.getElementById('lifetime').value
  };
  // Kiểm tra giá trị hợp lệ
  for (const [key, value] of Object.entries(inputs)) {
    if (key !== 'terrain' && (isNaN(value) || value < 0)) {
      throw new Error(`Giá trị ${key} không hợp lệ: ${value}`);
    }
  }
  if (!['I', 'II', 'III', 'IV'].includes(inputs.terrain)) {
    throw new Error(`Địa hình không hợp lệ: ${inputs.terrain}`);
  }
  return inputs;
}

/* ================= Bảng dữ liệu (đã mở rộng zones) ================= */
/* LƯU Ý: giá trị Ce cho A,B,C,D,M1,M2,E* là giá trị tham khảo/tạm thời.
   Nếu bạn muốn mình điền đúng từng ô theo TCVN 2737:2023 mình sẽ cập nhật. */

const kz_table = {
  'I': { 3: 0.75, 5: 0.80, 10: 1.00, 15: 1.10, 20: 1.20, 30: 1.30, 40: 1.40, 50: 1.50 },
  'II': { 3: 0.65, 5: 0.70, 10: 0.90, 15: 1.00, 20: 1.10, 30: 1.20, 40: 1.30, 50: 1.40 },
  'III': { 3: 0.55, 5: 0.60, 10: 0.80, 15: 0.90, 20: 1.00, 30: 1.10, 40: 1.20, 50: 1.30 },
  'IV': { 3: 0.45, 5: 0.50, 10: 0.70, 15: 0.80, 20: 0.90, 30: 1.00, 40: 1.10, 50: 1.20 }
};

/* Extended Ce tables (theta 0 and 90): added A,B,C,D,M1,M2 and E-zones.
   These are example coefficients — replace with exact TCVN table if needed.
*/
const ce_theta0 = {
  0:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -0.70, M2: -0.70,
        "1E1": -0.90, "1E2": -0.90, "2E1": -0.90, "2E2": -0.90, "3E1": -0.90, "3E2": -0.90,
        "4E1": -0.90, "4E2": -0.90, "5E1": -0.90, "5E2": -0.90, "6E1": -0.90, "6E2": -0.90,
        F: 0.80, G: -0.40, H: -0.40, I: -0.60 },
  5:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -0.70, M2: -0.70,
        "1E1": -0.90, "1E2": -0.90, "2E1": -0.90, "2E2": -0.90, "3E1": -0.90, "3E2": -0.90,
        "4E1": -0.90, "4E2": -0.90, "5E1": -0.90, "5E2": -0.90, "6E1": -0.90, "6E2": -0.90,
        F: 0.80, G: -0.40, H: -0.40, I: -0.60 },
 10:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -0.70, M2: -0.70,
        "1E1": -0.90, "1E2": -0.90, "2E1": -0.90, "2E2": -0.90, "3E1": -0.90, "3E2": -0.90,
        "4E1": -0.90, "4E2": -0.90, "5E1": -0.90, "5E2": -0.90, "6E1": -0.90, "6E2": -0.90,
        F: 0.80, G: -0.40, H: -0.40, I: -0.60 },
 15:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -0.70, M2: -0.70,
        "1E1": -0.90, "1E2": -0.90, "2E1": -0.90, "2E2": -0.90, "3E1": -0.90, "3E2": -0.90,
        "4E1": -0.90, "4E2": -0.90, "5E1": -0.90, "5E2": -0.90, "6E1": -0.90, "6E2": -0.90,
        F: 0.80, G: -0.40, H: -0.40, I: -0.60 },
 20:  { A: 0.90, B: -0.50, C: -0.50, D: -0.50, M1: -0.75, M2: -0.75,
        "1E1": -0.95, "1E2": -0.95, "2E1": -0.95, "2E2": -0.95, "3E1": -0.95, "3E2": -0.95,
        "4E1": -0.95, "4E2": -0.95, "5E1": -0.95, "5E2": -0.95, "6E1": -0.95, "6E2": -0.95,
        F: 0.90, G: -0.50, H: -0.50, I: -0.70 },
 30:  { A: 1.00, B: -0.60, C: -0.60, D: -0.60, M1: -0.80, M2: -0.80,
        "1E1": -1.00, "1E2": -1.00, "2E1": -1.00, "2E2": -1.00, "3E1": -1.00, "3E2": -1.00,
        "4E1": -1.00, "4E2": -1.00, "5E1": -1.00, "5E2": -1.00, "6E1": -1.00, "6E2": -1.00,
        F: 1.00, G: -0.60, H: -0.60, I: -0.80 },
 45:  { A: 1.20, B: -0.70, C: -0.70, D: -0.70, M1: -0.85, M2: -0.85,
        "1E1": -1.10, "1E2": -1.10, "2E1": -1.10, "2E2": -1.10, "3E1": -1.10, "3E2": -1.10,
        "4E1": -1.10, "4E2": -1.10, "5E1": -1.10, "5E2": -1.10, "6E1": -1.10, "6E2": -1.10,
        F: 1.20, G: -0.70, H: -0.70, I: -0.90 }
};

const ce_theta90 = {
  0:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -1.00, M2: -1.00,
        "1E1": -1.00, "1E2": -1.00, "2E1": -1.00, "2E2": -1.00, "3E1": -1.00, "3E2": -1.00,
        "4E1": -1.00, "4E2": -1.00, "5E1": -1.00, "5E2": -1.00, "6E1": -1.00, "6E2": -1.00,
        F: 0.80, G: -0.70, H: -0.40, I: -0.60 },
  5:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -1.00, M2: -1.00,
        "1E1": -1.00, "1E2": -1.00, "2E1": -1.00, "2E2": -1.00, "3E1": -1.00, "3E2": -1.00,
        "4E1": -1.00, "4E2": -1.00, "5E1": -1.00, "5E2": -1.00, "6E1": -1.00, "6E2": -1.00,
        F: 0.80, G: -0.70, H: -0.40, I: -0.60 },
 10:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -1.00, M2: -1.00,
        "1E1": -1.00, "1E2": -1.00, "2E1": -1.00, "2E2": -1.00, "3E1": -1.00, "3E2": -1.00,
        "4E1": -1.00, "4E2": -1.00, "5E1": -1.00, "5E2": -1.00, "6E1": -1.00, "6E2": -1.00,
        F: 0.80, G: -0.70, H: -0.40, I: -0.60 },
 15:  { A: 0.80, B: -0.50, C: -0.50, D: -0.50, M1: -1.00, M2: -1.00,
        "1E1": -1.00, "1E2": -1.00, "2E1": -1.00, "2E2": -1.00, "3E1": -1.00, "3E2": -1.00,
        "4E1": -1.00, "4E2": -1.00, "5E1": -1.00, "5E2": -1.00, "6E1": -1.00, "6E2": -1.00,
        F: 0.80, G: -0.70, H: -0.40, I: -0.60 },
 20:  { A: 0.90, B: -0.50, C: -0.50, D: -0.50, M1: -1.05, M2: -1.05,
        "1E1": -1.05, "1E2": -1.05, "2E1": -1.05, "2E2": -1.05, "3E1": -1.05, "3E2": -1.05,
        "4E1": -1.05, "4E2": -1.05, "5E1": -1.05, "5E2": -1.05, "6E1": -1.05, "6E2": -1.05,
        F: 0.90, G: -0.80, H: -0.50, I: -0.70 },
 30:  { A: 1.00, B: -0.60, C: -0.60, D: -0.60, M1: -1.10, M2: -1.10,
        "1E1": -1.10, "1E2": -1.10, "2E1": -1.10, "2E2": -1.10, "3E1": -1.10, "3E2": -1.10,
        "4E1": -1.10, "4E2": -1.10, "5E1": -1.10, "5E2": -1.10, "6E1": -1.10, "6E2": -1.10,
        F: 1.00, G: -0.90, H: -0.60, I: -0.80 },
 45:  { A: 1.20, B: -0.70, C: -0.70, D: -0.70, M1: -1.20, M2: -1.20,
        "1E1": -1.20, "1E2": -1.20, "2E1": -1.20, "2E2": -1.20, "3E1": -1.20, "3E2": -1.20,
        "4E1": -1.20, "4E2": -1.20, "5E1": -1.20, "5E2": -1.20, "6E1": -1.20, "6E2": -1.20,
        F: 1.20, G: -1.00, H: -0.70, I: -0.90 }
};

function getKz(z, terrain) {
  const table = kz_table[terrain] || kz_table['II'];
  const heights = Object.keys(table).map(Number).sort((a, b) => a - b);
  if (z <= heights[0]) return table[heights[0]];
  if (z >= heights[heights.length - 1]) return table[heights[heights.length - 1]];
  for (let i = 1; i < heights.length; i++) {
    if (z < heights[i]) {
      const lower = heights[i - 1];
      const upper = heights[i];
      const factor = (z - lower) / (upper - lower);
      return table[lower] + factor * (table[upper] - table[lower]);
    }
  }
  return 1.0; // fallback
}

function nearestAlpha(alpha, table) {
  const keys = Object.keys(table).map(Number).sort((a, b) => a - b);
  if (alpha <= keys[0]) return keys[0];
  if (alpha >= keys[keys.length - 1]) return keys[keys.length - 1];
  for (let i = 1; i < keys.length; i++) {
    if (alpha < keys[i]) {
      const lower = keys[i - 1];
      const upper = keys[i];
      const factor = (alpha - lower) / (upper - lower);
      return { lower, upper, factor };
    }
  }
  return keys[0]; // fallback
}

function getCe(alpha, theta, zone) {
  const tbl = (Math.abs(theta - 90) < 1e-6) ? ce_theta90 : ce_theta0;
  const a = nearestAlpha(Math.abs(alpha), tbl);
  if (typeof a === 'number') {
    return (tbl[a] && tbl[a][zone] !== undefined) ? tbl[a][zone] : 0;
  } else {
    const ceLower = (tbl[a.lower] && tbl[a.lower][zone] !== undefined) ? tbl[a.lower][zone] : 0;
    const ceUpper = (tbl[a.upper] && tbl[a.upper][zone] !== undefined) ? tbl[a.upper][zone] : 0;
    return ceLower + a.factor * (ceUpper - ceLower);
  }
}

/* ================= Tính toán ================= */
function computeWind(inputs) {
  const { nSpans, B, H, slope, parapet, L, Wo, C, Gf, terrain, Kzt, lifetime } = inputs;
  const W3_10 = Wo * C; // baseline for 10-year return
  const returnFactor = Math.pow(1 + 0.134 * Math.log(lifetime / 10), 2); // adjustment for return period
  const ze = H + (B / 2) * Math.tan(slope * Math.PI / 180);
  const kze = getKz(ze, terrain);
  const q = W3_10 * returnFactor * Kzt * kze * Gf; // velocity pressure
  const totalB = nSpans * B;
  const a = Math.max(Math.min(0.1 * totalB, 0.1 * L, 0.4 * ze), 3);

  // full list of zones we want to compute
  const zones = [
    "A","B","C","D","M1","M2",
    "1E1","1E2","2E1","2E2","3E1","3E2",
    "4E1","4E2","5E1","5E2","6E1","6E2",
    "F","G","H","I"
  ];

  const res = { theta0: {}, theta90: {}, ce0: {}, ce90: {}, q: round(q, 3), a: round(a, 2) };

  zones.forEach(z => {
    // Ce lookup
    const ce0 = getCe(slope, 0, z);
    const ce90 = getCe(slope, 90, z);

    // small parapet effect adjustment (kept from original)
    let adjCe0 = ce0;
    let adjCe90 = ce90;
    if (parapet > 0.5 && (z === 'F' || z === 'G')) { adjCe0 *= 1.1; adjCe90 *= 1.1; }

    res.ce0[z] = round(adjCe0, 2);
    res.ce90[z] = round(adjCe90, 2);

    res.theta0[z] = round(q * adjCe0, 3);
    res.theta90[z] = round(q * adjCe90, 3);
  });

  res.W3_10 = round(W3_10 * returnFactor, 3);
  res.kze = round(kze, 3);
  res.Kzt = Kzt;
  res.returnFactor = round(returnFactor, 3);
  res.Winternal_pos = round(q * 0.18, 3);
  res.Winternal_neg = round(q * (-0.18), 3);

  return res;
}

/* ================= Hiển thị ================= */
function renderSummary(inputs, out) {
  document.getElementById('results').style.display = 'block';
  const summary = document.getElementById('summary');
  summary.innerHTML = `
    <table class="table table-bordered">
      <thead class="table-light"><tr><th>Thông số</th><th>Giá trị</th></tr></thead>
      <tbody>
        <tr><td>Cấu hình</td><td>${inputs.nSpans} nhịp (B=${inputs.B} m, Tổng B=${inputs.nSpans * inputs.B} m), L=${inputs.L} m, H=${inputs.H} m, α=${inputs.slope}°, parapet=${inputs.parapet} m</td></tr>
        <tr><td>W3_10</td><td>${out.W3_10} kN/m²</td></tr>
        <tr><td>Kzt</td><td>${out.Kzt}</td></tr>
        <tr><td>k(ze)</td><td>${out.kze}</td></tr>
        <tr><td>q</td><td>${out.q} kN/m²</td></tr>
        <tr><td>a (zone width)</td><td>${out.a} m</td></tr>
        <tr><td>W_internal (+0.18)</td><td>${out.Winternal_pos} kN/m²</td></tr>
        <tr><td>W_internal (-0.18)</td><td>${out.Winternal_neg} kN/m²</td></tr>
      </tbody>
    </table>
  `;
}

function renderZoneTable(out, idPos, idNeg, thetaObj, ceObj) {
  // thetaObj: out.theta0 or out.theta90 ; ceObj: out.ce0 or out.ce90
  const zones = ['F', 'G', 'H', 'I'];
  let pos = '', neg = '';
  zones.forEach(z => {
    const p = thetaObj[z] ?? 0;
    const netPos = round(p - out.Winternal_pos, 3);
    const netNeg = round(p - out.Winternal_neg, 3);
    pos += `<tr><td>${z}</td><td>${ceObj[z]}</td><td class="${netPos >= 0 ? 'text-primary' : 'text-danger'}">${netPos}</td></tr>`;
    neg += `<tr><td>${z}</td><td>${ceObj[z]}</td><td class="${netNeg >= 0 ? 'text-primary' : 'text-danger'}">${netNeg}</td></tr>`;
  });
  document.getElementById(idPos).innerHTML = pos;
  document.getElementById(idNeg).innerHTML = neg;
}

/* render extra zones table */
function renderExtraZones(out) {
  const zones = ["A","B","C","D","M1","M2",
    "1E1","1E2","2E1","2E2","3E1","3E2",
    "4E1","4E2","5E1","5E2","6E1","6E2"];
  let html = "";
  zones.forEach(z => {
    const ce0 = (out.ce0[z] !== undefined) ? out.ce0[z] : '-';
    const p0  = (out.theta0[z] !== undefined) ? out.theta0[z] : '-';
    const ce90= (out.ce90[z] !== undefined) ? out.ce90[z] : '-';
    const p90 = (out.theta90[z] !== undefined) ? out.theta90[z] : '-';
    const cls0 = (typeof p0 === 'number' && p0 >= 0) ? 'text-primary' : 'text-danger';
    const cls90 = (typeof p90 === 'number' && p90 >= 0) ? 'text-primary' : 'text-danger';
    html += `<tr>
      <td>${z}</td>
      <td>${ce0}</td>
      <td class="${(p0==='-'? '' : '') } ${ (p0!=='-'? cls0 : '') }">${p0}</td>
      <td>${ce90}</td>
      <td class="${(p90==='-'? '' : '') } ${ (p90!=='-'? cls90 : '') }">${p90}</td>
    </tr>`;
  });
  document.getElementById("extraZones").innerHTML = html;
}

/* Parapet (net loads) */
function computeParapet(out) {
  // adjusted to typical values; replace with TCVN if available
  return {
    windward: round(out.q * 1.8, 3),
    leeward: round(out.q * -1.1, 3),
    sideward: round(out.q * -0.7, 3)
  };
}

function renderParapet(out, idAcross, idAlong) {
  const pAcross = computeParapet(out);
  const pAlong = computeParapet(out);
  document.getElementById(idAcross).innerHTML = `
    <tr><td>Parapet windward</td><td class="${pAcross.windward>=0?'text-primary':'text-danger'}">${pAcross.windward}</td></tr>
    <tr><td>Parapet leeward</td><td class="${pAcross.leeward>=0?'text-primary':'text-danger'}">${pAcross.leeward}</td></tr>
    <tr><td>Parapet sideward</td><td class="${pAcross.sideward>=0?'text-primary':'text-danger'}">${pAcross.sideward}</td></tr>
  `;
  document.getElementById(idAlong).innerHTML = `
    <tr><td>Parapet windward</td><td class="${pAlong.windward>=0?'text-primary':'text-danger'}">${pAlong.windward}</td></tr>
    <tr><td>Parapet leeward</td><td class="${pAlong.leeward>=0?'text-primary':'text-danger'}">${pAlong.leeward}</td></tr>
    <tr><td>Parapet sideward</td><td class="${pAlong.sideward>=0?'text-primary':'text-danger'}">${pAlong.sideward}</td></tr>
  `;
}

/* ================ SVG drawing (labels for extra zones) ================ */
function drawFrame(nSpans, B, H, slope, parapet, out) {
  const svg = document.getElementById('elevationSvg');
  svg.innerHTML = '';
  const scale = 8;
  const width = Math.max(300, nSpans * B * scale);
  const roofHeight = (B / 2) * Math.tan(slope * Math.PI / 180) * scale;
  const height = (H + parapet) * scale + roofHeight + 40;
  svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

  // frame box
  const frame = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
  frame.setAttribute('x', 20);
  frame.setAttribute('y', roofHeight + 10);
  frame.setAttribute('width', width - 40);
  frame.setAttribute('height', H * scale);
  frame.setAttribute('fill', '#fff0');
  frame.setAttribute('stroke', '#666');
  svg.appendChild(frame);

  // roof path
  const midX = 20 + (width - 40) / 2;
  const pathD = `M20,${roofHeight + 10 + H*scale} L${midX},${10} L${width - 20},${roofHeight + 10 + H*scale}`;
  const roof = document.createElementNS('http://www.w3.org/2000/svg', 'path');
  roof.setAttribute('d', pathD);
  roof.setAttribute('fill', 'none');
  roof.setAttribute('stroke', '#2b6cb0');
  svg.appendChild(roof);

  // parapet (dashed)
  if (parapet > 0) {
    const p = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    p.setAttribute('x', 20);
    p.setAttribute('y', roofHeight + 10 - parapet*scale);
    p.setAttribute('width', width - 40);
    p.setAttribute('height', parapet*scale);
    p.setAttribute('fill', 'none');
    p.setAttribute('stroke', '#333');
    p.setAttribute('stroke-dasharray', '4,4');
    svg.appendChild(p);
  }

  // labels for A/B/C/D at walls (example positions)
  const label = (txt, x, y) => {
    const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    t.setAttribute('x', x);
    t.setAttribute('y', y);
    t.setAttribute('font-size', '12');
    t.setAttribute('fill', '#1f9d55');
    t.setAttribute('text-anchor', 'middle');
    t.textContent = txt;
    svg.appendChild(t);
  };

  label('A', 40, roofHeight + 10 + H*scale/2);
  label('B', width - 40, roofHeight + 10 + H*scale/2);
  label('M1', midX - (width-40)/6, roofHeight + 10 + H*scale/3);
  label('M2', midX + (width-40)/6, roofHeight + 10 + H*scale/3);

  // also show pressures on top if out available (show M1/M2 values)
  if (out) {
    const m1 = out.theta0['M1'] !== undefined ? out.theta0['M1'] : '-';
    const m2 = out.theta0['M2'] !== undefined ? out.theta0['M2'] : '-';
    const t1 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    t1.setAttribute('x', midX - (width-40)/6);
    t1.setAttribute('y', roofHeight + 10 + H*scale/3 - 10);
    t1.setAttribute('font-size', '11');
    t1.setAttribute('fill', '#000');
    t1.setAttribute('text-anchor', 'middle');
    t1.textContent = `${m1} kN/m²`;
    svg.appendChild(t1);

    const t2 = t1.cloneNode(true);
    t2.setAttribute('x', midX + (width-40)/6);
    t2.textContent = `${m2} kN/m²`;
    svg.appendChild(t2);
  }
}

function drawPlan(nSpans, B, L, out) {
  const svg = document.getElementById('planSvg');
  svg.innerHTML = '';
  const scale = 6;
  const width = Math.max(300, nSpans * B * scale);
  const height = Math.max(240, L * scale);
  svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

  const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
  rect.setAttribute('x', 20);
  rect.setAttribute('y', 20);
  rect.setAttribute('width', width - 40);
  rect.setAttribute('height', height - 40);
  rect.setAttribute('fill', '#fff0');
  rect.setAttribute('stroke', '#666');
  svg.appendChild(rect);

  // place labels A,B,C,D around
  const xc = width/2, yc = height/2;
  const put = (txt, x, y) => {
    const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    t.setAttribute('x', x);
    t.setAttribute('y', y);
    t.setAttribute('font-size', '12');
    t.setAttribute('fill', '#1f9d55');
    t.setAttribute('text-anchor', 'middle');
    t.textContent = txt;
    svg.appendChild(t);
  };

  put('A', 40, yc);
  put('B', width - 40, yc);
  put('C', xc, 40);
  put('D', xc, height - 20);

  // show some E-zone labels along edges
  const edgeXs = [100, 200, 300].filter(x=>x < width-40);
  edgeXs.forEach((ex, i) => {
    put(`${i+1}E1`, ex, 40+14);
    put(`${i+1}E2`, ex, height-28);
  });

  // show p values small if out available (take A,B,...)
  if (out) {
    const showVal = (zone, x, y) => {
      const v = out.theta0[zone] !== undefined ? out.theta0[zone] : '-';
      const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      t.setAttribute('x', x);
      t.setAttribute('y', y);
      t.setAttribute('font-size', '11');
      t.setAttribute('fill', '#000');
      t.setAttribute('text-anchor', 'middle');
      t.textContent = (v !== '-') ? `${v}` : '-';
      svg.appendChild(t);
    };
    showVal('A', 40, yc+14);
    showVal('B', width - 40, yc+14);
    showVal('C', xc, 54);
    showVal('D', xc, height-4);
    showVal('M1', xc - 40, yc - 20);
    showVal('M2', xc + 40, yc - 20);
  }
}

/* ================= Event Handlers ================= */
function handleCalc() {
  const errorDiv = document.getElementById('error-message');
  errorDiv.style.display = 'none';
  errorDiv.innerHTML = '';
  try {
    const inputs = getInputs();
    const out = computeWind(inputs);

    drawFrame(inputs.nSpans, inputs.B, inputs.H, inputs.slope, inputs.parapet, out);
    drawPlan(inputs.nSpans, inputs.B, inputs.L, out);

    renderSummary(inputs, out);

    // main zone tables (F-G-H-I) with net (GCpi effect shown)
    renderZoneTable(out, 'windAcross_Pos', 'windAcross_Neg', out.theta0, out.ce0);
    renderZoneTable(out, 'windAlong_Pos', 'windAlong_Neg', out.theta90, out.ce90);

    // parapet results
    renderParapet(out, 'parapetAcross', 'parapetAlong');

    // extra zones (A,B,C,D,M1,M2,E's)
    renderExtraZones(out);

  } catch (error) {
    errorDiv.innerHTML = 'Lỗi: ' + error.message;
    errorDiv.style.display = 'block';
  }
}

document.getElementById('calcBtn').addEventListener('click', handleCalc);
document.getElementById('resetBtn').addEventListener('click', () => {
  document.getElementById('windForm').reset();
  document.getElementById('results').style.display = 'none';
  document.getElementById('summary').innerHTML = '';
  document.getElementById('windAcross_Pos').innerHTML = '';
  document.getElementById('windAcross_Neg').innerHTML = '';
  document.getElementById('windAlong_Pos').innerHTML = '';
  document.getElementById('windAlong_Neg').innerHTML = '';
  document.getElementById('extraZones').innerHTML = '';
  document.getElementById('parapetAcross').innerHTML = '';
  document.getElementById('parapetAlong').innerHTML = '';
  document.getElementById('error-message').style.display = 'none';
  const inputs = getInputs();
  drawFrame(inputs.nSpans, inputs.B, inputs.H, inputs.slope, inputs.parapet, null);
  drawPlan(inputs.nSpans, inputs.B, inputs.L, null);
});

document.addEventListener('DOMContentLoaded', () => {
  const inputs = getInputs();
  drawFrame(inputs.nSpans, inputs.B, inputs.H, inputs.slope, inputs.parapet, null);
  drawPlan(inputs.nSpans, inputs.B, inputs.L, null);
});