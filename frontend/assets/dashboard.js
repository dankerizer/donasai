function wpdCancelSub(id, confirmMsg, successMsg, failMsg, nonce) {
    if (!confirm(confirmMsg)) return;

    fetch(donasaiSettings.root + 'donasai/v1/subscriptions/' + id + '/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': donasaiSettings.nonce
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(successMsg);
            location.reload();
        } else {
            alert(failMsg);
        }
    });
}
