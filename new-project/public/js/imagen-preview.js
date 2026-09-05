document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('imagen');
  if (!input) return;

  input.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagen-preview');
    const img = document.getElementById('preview-img');
    const currentImage = document.getElementById('current-image');

    if (file) {
      const reader = new FileReader();
      reader.onload = function (event) {
        img.src = event.target.result;
        preview.style.display = 'block';
        if (currentImage) currentImage.style.display = 'none';
      };
      reader.readAsDataURL(file);
    } else {
      preview.style.display = 'none';
      if (currentImage) currentImage.style.display = 'block';
    }
  });
});
