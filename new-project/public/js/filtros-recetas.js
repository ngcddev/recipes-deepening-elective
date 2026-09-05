document.addEventListener('DOMContentLoaded', function () {
  const categoriaSelect = document.getElementById('categoria');
  if (categoriaSelect) {
    categoriaSelect.addEventListener('change', function () {
      if (this.value !== '0') {
        this.form.submit();
      }
    });
  }
});
