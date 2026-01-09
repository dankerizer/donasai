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

function formatMoney(val) {
	if (val === "" || val === undefined || val === null) return "";
	const num = typeof val === "string" ? parseInt(val.replace(/\D/g, "")) : val;
	if (Number.isNaN(num)) return "";
	return new Intl.NumberFormat("id-ID").format(num);
}

function updateAmountUI(rawValue) {
    const display = document.getElementById("amount_display");
    const hidden = document.getElementById("amount");
    
    hidden.value = rawValue;
    display.value = formatMoney(rawValue);
}

function selectAmount(card, amount) {
	updateAmountUI(amount);
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
    updateAmountUI(Math.round(val * 0.025));
}

function selectQurbanPackage(_price) {
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
    updateAmountUI(qty * price);
}

// Initialize listeners
document.addEventListener("DOMContentLoaded", () => {
    const amountDisplay = document.getElementById("amount_display");
    const amountHidden = document.getElementById("amount");

    if (amountDisplay && amountHidden) {
        amountDisplay.addEventListener("input", (e) => {
            // Remove non-digits
            const raw = e.target.value.replace(/\D/g, "");
            const num = parseInt(raw, 10);
            
            if (!Number.isNaN(num)) {
                amountHidden.value = num;
                e.target.value = formatMoney(num);
            } else {
                amountHidden.value = "";
                e.target.value = "";
            }
        });

        // Initial sync if amount has value (e.g. from reload)
        if (amountHidden.value) {
            amountDisplay.value = formatMoney(amountHidden.value);
        }
    }
});

