/**
 * Summary JS
 */
function showToast(message) {
    let toast = document.getElementById('wpd-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'wpd-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

function copyToClipboard(text, successMsg, failMsg) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showToast(successMsg || 'Salin sukses!');
        }).catch(err => {
            fallbackCopyTextToClipboard(text, successMsg, failMsg);
        });
    } else {
        fallbackCopyTextToClipboard(text, successMsg, failMsg);
    }
}

function fallbackCopyTextToClipboard(text, successMsg, failMsg) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? (successMsg || 'Salin sukses!') : (failMsg || 'Gagal salin');
        showToast(msg);
    } catch (err) {
        showToast(failMsg || 'Gagal salin');
    }
    document.body.removeChild(textArea);
}

function handleConfirmPayment(confUrl, phone, donationId, amount) {
    if (confUrl) {
         window.location.href = confUrl + (confUrl.indexOf('?') === -1 ? '?' : '&') + 'donation_id=' + donationId;
    } else if (phone) {
         const cleanPhone = phone.replace(/\D/g, '');
         const text = encodeURIComponent(`Halo Admin, saya sudah transfer untuk donasi #${donationId} sebesar Rp ${amount}. Mohon dicek. Terima kasih.`);
         window.open(`https://wa.me/${cleanPhone}?text=${text}`, '_blank');
    } else {
         alert('Silakan hubungi admin untuk konfirmasi.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Confetti
    if (window.wpd_donation_status === 'complete' && typeof confetti === 'function') {
        var count = 200;
        var defaults = { origin: { y: 0.7 }, zIndex: 9999 };

        function fire(particleRatio, opts) {
            confetti(Object.assign({}, defaults, opts, {
                particleCount: Math.floor(count * particleRatio)
            }));
        }

        fire(0.25, { spread: 26, startVelocity: 55 });
        fire(0.2, { spread: 60 });
        fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
        fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
        fire(0.1, { spread: 120, startVelocity: 45 });
    }

    // Confirmation Button
    const confirmBtn = document.getElementById('wpd-confirm-btn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = confirmBtn.getAttribute('data-url');
            const phone = confirmBtn.getAttribute('data-phone');
            const id = confirmBtn.getAttribute('data-id');
            const amount = confirmBtn.getAttribute('data-amount');
            handleConfirmPayment(url, phone, id, amount);
        });
    }

    // Copy Button (if any)
    document.querySelectorAll('.wpd-copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
             const text = btn.getAttribute('data-copy');
             copyToClipboard(text, 'Disalin!');
        });
    });
});
