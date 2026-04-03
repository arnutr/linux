const fileInput = document.getElementById('checkin_photo');
if (fileInput) {
  fileInput.addEventListener('change', () => {
    const preview = document.getElementById('photo_preview');
    const file = fileInput.files[0];
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.classList.remove('d-none');
    }
  });

  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: true }).then((stream) => {
      const video = document.getElementById('video');
      if (video) video.srcObject = stream;
    }).catch(() => {});
  }
}

function captureFromCamera() {
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  if (!video || !canvas || !fileInput) return;

  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
  canvas.toBlob((blob) => {
    const file = new File([blob], `webcam_${Date.now()}.png`, { type: 'image/png' });
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;
    fileInput.dispatchEvent(new Event('change'));
  }, 'image/png');
}

function getGeoLocation() {
  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition((pos) => {
    document.getElementById('latitude').value = pos.coords.latitude;
    document.getElementById('longitude').value = pos.coords.longitude;
  });
}
