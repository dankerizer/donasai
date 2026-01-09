/**
 * Payment Page Scripts
 */
function showToast(message) {
	const toast = document.getElementById("wpd-toast");
	if (!toast) return;
	toast.textContent = message;
	toast.classList.add("show");
	setTimeout(() => {
		toast.classList.remove("show");
	}, 3000);
}

function selectAmount(card, amount) {
	document.getElementById("amount").value = amount;
	document
		.querySelectorAll(".wpd-preset-card")
		.forEach((c) => c.classList.remove("active"));
	card.classList.add("active");
}

function toggleZakatType(val) {
	const output = document.getElementById("zakat_calc_input");
	if (val === "maal") output.placeholder = "Total Harta (Rp)";
	else output.placeholder = "Total Penghasilan (Rp)";
}

function calculateZakat() {
	const val = document.getElementById("zakat_calc_input").value;
	document.getElementById("amount").value = Math.round(val * 0.025);
}

function selectQurbanPackage(price) {
	document.querySelector("#qurban_qty_wrapper").style.display = "block";
	updateQurbanTotal();
}

function changeQty(delta) {
	const input = document.getElementById("qurban_qty");
	let val = parseInt(input.value) + delta;
	if (val < 1) val = 1;
	input.value = val;
	updateQurbanTotal();
}

function updateQurbanTotal() {
	const qty = document.getElementById("qurban_qty").value;
	const price = document.querySelector(
		'input[name="qurban_package"]:checked',
	).value;
	document.getElementById("amount").value = qty * price;
}

