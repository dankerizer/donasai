function wpdUpdateFileName(input) {
    var label = document.getElementById('donasai-file-text');
    var wrapper = document.querySelector('.donasai-upload-label');
    if (input.files && input.files.length > 0) {
        label.textContent = input.files[0].name;
        label.style.color = '#111827';
        wrapper.style.borderColor = 'var(--donasai-primary, #10b981)';
        wrapper.style.background = '#f0fdf4';
    }
}
