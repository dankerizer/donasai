/**
 * Payment Page Scripts
 */
function showToast(message) {
	const toast = document.getElementById("donasai-toast");
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
	document.querySelectorAll(".donasai-preset-card").forEach((c) => {
		c.classList.remove("active");
	});
	card.classList.add("active");
}

function toggleZakatType(val) {
	const output = document.getElementById("zakat_calc_input_display");
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

	// Donation Amount Input
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

		// Initial sync if amount has value
		if (amountHidden.value) {
			amountDisplay.value = formatMoney(amountHidden.value);
		}
	}

	// Zakat Calculator Input
	const zakatDisplay = document.getElementById("zakat_calc_input_display");
	const zakatHidden = document.getElementById("zakat_calc_input");

	if (zakatDisplay && zakatHidden) {
		zakatDisplay.addEventListener("input", (e) => {
			// Remove non-digits
			const raw = e.target.value.replace(/\D/g, "");
			const num = parseInt(raw, 10);

			if (!Number.isNaN(num)) {
				zakatHidden.value = num;
				e.target.value = formatMoney(num);
				// Trigger calculation
				calculateZakat();
			} else {
				zakatHidden.value = "";
				e.target.value = "";
				// Reset amount if empty
				updateAmountUI(0);
			}
		});
	}

	// Zakat Type Toggle
    const zakatType = document.getElementById("zakat_type");
    if (zakatType) {
        zakatType.addEventListener("change", function() {
            toggleZakatType(this.value);
        });
    }

    // Qurban Package Selection
    document.querySelectorAll('input[name="qurban_package"]').forEach(radio => {
        radio.addEventListener("click", function() {
            selectQurbanPackage(this.value);
        });
    });

    // Preset Amount Selection
    document.querySelectorAll('.donasai-preset-card').forEach(card => {
        card.addEventListener("click", function() {
            const amount = this.getAttribute('data-amount');
            if (amount) {
                selectAmount(this, amount);
            }
        });
    });

    // Qty Buttons
    document.querySelectorAll('.donasai-qty-btn').forEach(btn => {
        btn.addEventListener("click", function() {
            const delta = parseInt(this.getAttribute('data-delta'));
            changeQty(delta);
        });
    });

    // Fee Coverage Logic
    initFeeCoverage();

	// Midtrans Snap Integration
	initMidtransSnap();
});

function initMidtransSnap() {
	if (
		typeof donasai_payment_vars === "undefined" ||
		!donasai_payment_vars.is_midtrans_active
	)
		return;

	// Inject Script if not present
	if (donasai_payment_vars.snap_url) {
		if (
			!document.querySelector(
				'script[src="' + donasai_payment_vars.snap_url + '"]',
			)
		) {
			const script = document.createElement("script");
			script.src = donasai_payment_vars.snap_url;
			if (donasai_payment_vars.client_key) {
				script.setAttribute("data-client-key", donasai_payment_vars.client_key);
			}
			document.body.appendChild(script);
		}
	}

	const form = document.getElementById("donationForm");
	if (!form) return;

	form.addEventListener("submit", function (e) {
		const paymentMethodInput = document.querySelector(
			'input[name="payment_method"]:checked',
		);
		const method = paymentMethodInput ? paymentMethodInput.value : "";

		if (method !== "midtrans") return;

		e.preventDefault();
		const btn = document.querySelector(".donasai-btn-primary");
		const originalText = btn.innerText;
		btn.innerText = "Memproses...";
		btn.disabled = true;

		const formData = new FormData(this);
		formData.append("donasai_ajax", "1");

		fetch(window.location.href, { method: "POST", body: formData })
			.then((response) => response.json())
			.then((res) => {
				if (res.success) {
					if (res.data.is_midtrans && res.data.snap_token) {
						if (window.snap) {
							window.snap.pay(res.data.snap_token, {
								onSuccess: (result) => {
									window.location.href = res.data.redirect_url;
								},
								onPending: (result) => {
									window.location.href = res.data.redirect_url;
								},
								onError: (result) => {
									showToast("Pembayaran Gagal!");
									btn.disabled = false;
									btn.innerText = originalText;
								},
								onClose: () => {
									btn.disabled = false;
									btn.innerText = originalText;
								},
							});
						} else {
							showToast("Snap JS not loaded yet.");
							btn.disabled = false;
							btn.innerText = originalText;
						}
					} else {
						// Fallback redirect
						if (res.data.redirect_url)
							window.location.href = res.data.redirect_url;
					}
				} else {
					showToast("Error: " + res.data.message);
					btn.disabled = false;
					btn.innerText = originalText;
				}
			})
			.catch((err) => {
				console.error(err);
				showToast("Terjadi kesalahan koneksi.");
				btn.disabled = false;
				btn.innerText = originalText;
			});
	});
}

/**
 * Fee Coverage Feature (Pro)
 */
function initFeeCoverage() {
	const feeSection = document.getElementById("fee-coverage-section");
	const feeCheckbox = document.getElementById("cover_fee_checkbox");
	const feeSummary = document.getElementById("fee_summary");
	const amountHidden = document.getElementById("amount");

	if (!feeSection || !feeCheckbox) return;

	// Show fee section
	feeSection.style.display = "block";

	// Listen for amount changes
	if (amountHidden) {
		const observer = new MutationObserver(() => {
			updateFeeCalculation();
		});
		observer.observe(amountHidden, {
			attributes: true,
			attributeFilter: ["value"],
		});

		// Also listen for direct value changes via interval (fallback)
		let lastAmount = amountHidden.value;
		setInterval(() => {
			if (amountHidden.value !== lastAmount) {
				lastAmount = amountHidden.value;
				updateFeeCalculation();
			}
		}, 300);
	}

	// Listen for payment method changes
	document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
		radio.addEventListener("change", updateFeeCalculation);
	});

	// Listen for checkbox changes
	feeCheckbox.addEventListener("change", () => {
		if (feeSummary) {
			feeSummary.style.display = feeCheckbox.checked ? "block" : "none";
		}
		updateFeeCalculation();
	});

	// Initial calculation
	updateFeeCalculation();
}

async function updateFeeCalculation() {
	const feeCheckbox = document.getElementById("cover_fee_checkbox");
	const feeAmountHidden = document.getElementById("fee_amount");
	const feeAmountDisplay = document.getElementById("fee_amount_display");
	const feeSummary = document.getElementById("fee_summary");
	const baseAmountDisplay = document.getElementById("base_amount_display");
	const feeDisplay = document.getElementById("fee_display");
	const totalDisplay = document.getElementById("total_display");
	const amountHidden = document.getElementById("amount");

	if (!feeCheckbox || !amountHidden) return;

	const baseAmount = parseInt(amountHidden.value || "0", 10);
	const paymentMethod = document.querySelector(
		'input[name="payment_method"]:checked',
	);
	let gateway = paymentMethod ? paymentMethod.value : "manual";

	// Handle multi-bank manual format (manual_123 -> manual)
	if (gateway.startsWith("manual_")) {
		gateway = "manual";
	}

	if (baseAmount <= 0) {
		if (feeAmountDisplay) feeAmountDisplay.textContent = "Rp 0";
		if (feeAmountHidden) feeAmountHidden.value = "0";
		return;
	}

	try {
		const root = donasai_payment_vars.root || "/wp-json/";
		const response = await fetch(
			`${root}donasai-pro/v1/fee/calculate?amount=${baseAmount}&gateway=${gateway}`,
		);
		const data = await response.json();

		if (feeAmountDisplay) {
			feeAmountDisplay.textContent = data.fee_formatted || "Rp 0";
		}

		if (feeCheckbox.checked) {
			if (feeAmountHidden) feeAmountHidden.value = data.fee || 0;
			if (baseAmountDisplay)
				baseAmountDisplay.textContent = "Rp " + formatMoney(baseAmount);
			if (feeDisplay) feeDisplay.textContent = data.fee_formatted || "Rp 0";
			if (totalDisplay)
				totalDisplay.textContent =
					data.total_formatted || "Rp " + formatMoney(baseAmount);
			if (feeSummary) feeSummary.style.display = "block";
		} else {
			if (feeAmountHidden) feeAmountHidden.value = "0";
			if (feeSummary) feeSummary.style.display = "none";
		}
	} catch (err) {
		console.warn("Fee calculation error:", err);
		// Fallback: no fee
		if (feeAmountHidden) feeAmountHidden.value = "0";
	}
}
