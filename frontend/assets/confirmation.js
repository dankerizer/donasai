function wpdUpdateFileName(input) {
    var label = document.getElementById('wpd-file-text');
    var wrapper = document.querySelector('.wpd-upload-label');
    if (input.files && input.files.length > 0) {
        label.textContent = input.files[0].name;
        label.style.color = '#111827';
        wrapper.style.borderColor = 'var(--wpd-primary, #10b981)';
        wrapper.style.background = '#f0fdf4';
    }
}
