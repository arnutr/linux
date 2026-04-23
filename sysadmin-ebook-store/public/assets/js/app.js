document.querySelectorAll('.js-edit-member').forEach((btn) => {
  btn.addEventListener('click', () => {
    const m = JSON.parse(btn.dataset.member || '{}');
    document.getElementById('em-id').value = m.id || '';
    document.getElementById('em-name').value = m.name || '';
    document.getElementById('em-email').value = m.email || '';
    document.getElementById('em-role').value = m.role || 'customer';
  });
});

document.querySelectorAll('.js-edit-book').forEach((btn) => {
  btn.addEventListener('click', () => {
    const b = JSON.parse(btn.dataset.book || '{}');
    document.getElementById('eb-id').value = b.id || '';
    document.getElementById('eb-title').value = b.title || '';
    document.getElementById('eb-description').value = b.description || '';
    document.getElementById('eb-price').value = b.price || '';
  });
});
