(() => {
  const input = document.getElementById('checkin_photo');
  const preview = document.getElementById('preview');
  if (input && preview) {
    input.addEventListener('change', (e) => {
      const file = e.target.files?.[0];
      if (!file) return;
      preview.src = URL.createObjectURL(file);
      preview.classList.remove('d-none');
    });
  }

  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition((position) => {
      const lat = document.getElementById('latitude');
      const lng = document.getElementById('longitude');
      if (lat && lng) {
        lat.value = position.coords.latitude;
        lng.value = position.coords.longitude;
      }
    });
  }

  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const root = document.documentElement;
      const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme', next);
    });
    const saved = localStorage.getItem('theme');
    if (saved) document.documentElement.setAttribute('data-bs-theme', saved);
  }

  const chartCanvas = document.getElementById('summaryChart');
  if (chartCanvas && window.chartData) {
    new Chart(chartCanvas, {
      type: 'bar',
      data: {
        labels: ['Users', 'Courses', 'Sessions', 'Suspicious'],
        datasets: [{ label: 'Count', data: window.chartData }]
      }
    });
  }
})();
