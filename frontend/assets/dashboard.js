function wpdCancelSub(id, confirmMsg, successMsg, failMsg, nonce) {
    if (!confirm(confirmMsg)) return;

    fetch('/wp-json/wpd/v1/subscriptions/' + id + '/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce
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
