/**
 * Campaign Single JS
 */
function openWpdTab(tabName) {
    const x = document.getElementsByClassName("wpd-tab-content");
    for (let i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    const tabs = document.getElementsByClassName("wpd-tab-btn");
    for (let i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove("active");
        tabs[i].style.borderBottomColor = "transparent";
        tabs[i].style.color = "#6b7280";
    }
    document.getElementById("wpd-tab-" + tabName).style.display = "block";
    const activeBtn = document.getElementById("tab-btn-" + tabName);
    activeBtn.classList.add("active");
    activeBtn.style.borderBottomColor = "#2563eb";
    activeBtn.style.color = "#2563eb";
}

function wpdCopyRef() {
    const copyText = document.getElementById("wpd-ref-link");
    copyText.select();
    document.execCommand("copy");
    alert("Link copied!");
}

function wpdRegisterFundraiserHelper(campaignId, nonce) {
    fetch('/wp-json/wpd/v1/fundraisers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce
        },
        body: JSON.stringify({ campaign_id: campaignId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.referral_link) {
            const modal = document.getElementById('wpd-fundraiser-modal');
            modal.style.display = 'flex';
            document.getElementById('wpd-ref-link').value = data.referral_link;
            const text = "Yuk bantu donasi di campaign ini: " + data.referral_link;
            document.getElementById('wpd-wa-share').href = "https://wa.me/?text=" + encodeURIComponent(text);
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
        }
    })
    .catch(() => alert('Error connecting to server'));
}
