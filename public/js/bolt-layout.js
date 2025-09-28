function drawBolts() {
    const nOut   = parseInt($('#nBoltOutPlane').val()) || 2; // số hàng Y
    const nIn    = parseInt($('#nBoltInPlane').val()) || 4;  // số cột X
    const pitchX = parseFloat($('#pitchX').val()) || 80;
    const pitchY = parseFloat($('#pitchY').val()) || 80;
    const edgeX  = parseFloat($('#edgeX').val()) || 0;
    const edgeY  = parseFloat($('#edgeY').val()) || 0;
    const plateW = parseFloat($('#plateW').val()) || 400;
    const plateH = parseFloat($('#plateH').val()) || 300;
    const boltR  = (parseFloat($('input[name="bolt_d_mm"]').val()) || 20) / 2 * 0.5;

    const svg = document.getElementById("boltLayout");
    svg.innerHTML = "";

    // auto viewBox
    svg.setAttribute("viewBox", `0 0 ${plateW + 120} ${plateH + 100}`);
    svg.setAttribute("width", plateW + 120);
    svg.setAttribute("height", plateH + 100);

    const offsetX = 50;
    const offsetY = 30;

    // Plate
    const plate = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    plate.setAttribute("x", offsetX);
    plate.setAttribute("y", offsetY);
    plate.setAttribute("width", plateW);
    plate.setAttribute("height", plateH);
    plate.setAttribute("fill", "#f9f9f9");
    plate.setAttribute("stroke", "#333");
    svg.appendChild(plate);

    // Tính vị trí bu-lông
    const boltPositions = [];

    // Tính tọa độ X (đối xứng quanh tâm bản)
    const totalBoltWidth = (nIn - 1) * pitchX;
    const startX = offsetX + (plateW - totalBoltWidth) / 2; // căn giữa X

    // Tính tọa độ Y (theo Edge Y và Pitch Y)
    const totalBoltHeight = (nOut - 1) * pitchY;
    const startY = offsetY + edgeY;

    for (let j = 0; j < nIn; j++) {        // cột theo X
        for (let i = 0; i < nOut; i++) {   // hàng theo Y
            let cx = startX + j * pitchX;
            let cy = startY + i * pitchY;
            boltPositions.push({ x: cx, y: cy });
        }
    }

    // Vẽ bu-lông và toạ độ
    boltPositions.forEach((pos, idx) => {
        const bolt = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        bolt.setAttribute("cx", pos.x);
        bolt.setAttribute("cy", pos.y);
        bolt.setAttribute("r", boltR);
        bolt.setAttribute("fill", "brown");
        svg.appendChild(bolt);

        const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
        label.textContent = `(${(pos.x - offsetX).toFixed(0)}, ${(pos.y - offsetY).toFixed(0)})`;
        label.setAttribute("x", pos.x + 6);
        label.setAttribute("y", pos.y - 6);
        label.setAttribute("font-size", "10");
        label.setAttribute("fill", "black");
        svg.appendChild(label);
    });
}

$(document).ready(function () {
    $('#bolt_grade').on('change', function () {
        $('#custom_fn').toggle($(this).val() === 'custom');
    }).trigger('change');

    $('#nBoltOutPlane, #nBoltInPlane, #pitchX, #pitchY, #edgeX, #edgeY, #plateW, #plateH, input[name="bolt_d_mm"]').on('input', drawBolts);

    drawBolts();
});