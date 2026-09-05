document.addEventListener('DOMContentLoaded', function () {
  const profileBtn = document.querySelector('.profile-btn');

  if (profileBtn) {
    profileBtn.addEventListener('click', function () {
      document.querySelector('.profile-dropdown').classList.toggle('show');
    });
  }

  window.addEventListener('click', function (e) {
    if (!e.target.matches('.profile-btn')) {
      const dropdown = document.querySelector('.profile-dropdown');
      if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    }
  });
});
