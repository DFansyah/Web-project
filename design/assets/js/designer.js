let canvas;
const drawingArea = document.getElementById('drawing-area');

document.addEventListener("DOMContentLoaded", () => {
	// 1. Init Canvas
	canvas = new fabric.Canvas('tshirt-canvas', {
		width: drawingArea.clientWidth,
		height: drawingArea.clientHeight,
		preserveObjectStacking: true
	});

	// Event Listener Standar
	canvas.on('selection:created', showActions);
	canvas.on('selection:updated', showActions);
	canvas.on('selection:cleared', hideActions);

	// Responsive Resize
	const resizeObserver = new ResizeObserver(() => {
		resizeCanvas();
	});
	resizeObserver.observe(drawingArea);

	// Color Input
	document.getElementById('colorInput').addEventListener('input', function (e) {
		const active = canvas.getActiveObject();
		if (active) {
			active.set('fill', e.target.value);
			if (active.type === 'path') active.set('stroke', e.target.value);
			canvas.renderAll();
		}
	});

	// File Input
	document.getElementById('fileInput').addEventListener('change', function (e) {
		const file = e.target.files[0];
		if (!file) return;
		const reader = new FileReader();
		reader.onload = function (f) {
			fabric.Image.fromURL(f.target.result, function (img) {
				const maxDim = canvas.width * 0.6;
				if (img.width > maxDim) img.scaleToWidth(maxDim);
				canvas.add(img);
				img.center();
				img.setCoords();
				canvas.setActiveObject(img);
			});
		};
		reader.readAsDataURL(file);
		this.value = '';
	});
});

function resizeCanvas() {
	if (!canvas) return;
	const newWidth = drawingArea.clientWidth;
	const newHeight = drawingArea.clientHeight;
	if (canvas.width === newWidth && canvas.height === newHeight) return;
	const scaleX = newWidth / canvas.width;
	const scaleY = newHeight / canvas.height;
	canvas.setWidth(newWidth);
	canvas.setHeight(newHeight);
	canvas.getObjects().forEach((obj) => {
		obj.set({
			left: obj.left * scaleX,
			top: obj.top * scaleY,
			scaleX: obj.scaleX * scaleX,
			scaleY: obj.scaleY * scaleY
		});
		obj.setCoords();
	});
	canvas.renderAll();
}

function addText() {
	let input = prompt("Masukkan Teks:", "BRAND");
	if (input && input.trim() !== "") {
		const color = document.getElementById('colorInput').value;
		const text = new fabric.Text(input, {
			fontSize: canvas.width * 0.15,
			fill: color,
			fontFamily: 'Poppins',
			fontWeight: 'bold'
		});
		canvas.add(text);
		text.center();
		canvas.setActiveObject(text);
	}
}

function showActions() {
	document.getElementById('action-buttons').style.display = 'flex';
}

function hideActions() {
	document.getElementById('action-buttons').style.display = 'none';
}

function deleteObj() {
	const active = canvas.getActiveObject();
	if (active) {
		canvas.remove(active);
		hideActions();
	}
}

function editSelectedText() {
	const active = canvas.getActiveObject();
	if (active && active.type === 'text') {
		let newText = prompt("Ubah Teks:", active.text);
		if (newText !== null) {
			active.set('text', newText);
			canvas.renderAll();
		}
	}
}

function downloadDesign() {
	canvas.discardActiveObject().renderAll();
	const guide = document.getElementById('guide-lines');
	guide.style.display = 'none';
	html2canvas(document.getElementById('tshirt-wrapper'), {
		useCORS: true,
		scale: 2,
		backgroundColor: null
	}).then(final => {
		const link = document.createElement('a');
		link.download = 'desain-saya.png';
		link.href = final.toDataURL('image/png');
		link.click();
		guide.style.display = 'block';
	});
}

// --- FUNGSI BARU: KIRIM KE ADMIN ---
function finish() {
	let userName = prompt("Masukkan Nama Anda:", "User");
	if (!userName) return;
	let contact = prompt("No. WhatsApp (untuk konfirmasi):", "0812...");

	const btn = document.querySelector("button[onclick='finish()']");
	const originalText = btn.innerText;
	btn.innerText = "Mengirim...";
	btn.disabled = true;

	canvas.discardActiveObject().renderAll();
	const guide = document.getElementById('guide-lines');
	guide.style.display = 'none';

	html2canvas(document.getElementById('tshirt-wrapper'), {
		useCORS: true,
		scale: 2,
		backgroundColor: null
	}).then(finalCanvas => {
		const imageData = finalCanvas.toDataURL('image/png');
		const urlParams = new URLSearchParams(window.location.search);
		const productId = urlParams.get('product_id') || 1;

		fetch('save_design.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					image: imageData,
					product_id: productId,
					user_name: userName,
					contact: contact
				})
			})
			.then(response => response.json())
			.then(data => {
				guide.style.display = 'block';
				btn.innerText = originalText;
				btn.disabled = false;
				if (data.status === 'success') {
					alert("Berhasil! Desain dikirim ke Admin.");
					window.location.href = "index.php";
				}
				else {
					alert("Gagal: " + data.message);
				}
			});
	});
}