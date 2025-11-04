  document.addEventListener('DOMContentLoaded', () => {
    const filterWrap = document.getElementById('statusFilter');
    const pills = filterWrap.querySelectorAll('.pill');
    const cards = document.querySelectorAll('.order-card');

    // fungsi buat apply filter
    const applyFilter = (status) => {
      cards.forEach(c => {
        const s = c.getAttribute('data-status');
        // kalau 'semua', tampilkan semua
        c.style.display = (status === 'semua' || s === status) ? 'block' : 'none';
      });
    };

    // set default: tampil semua
    applyFilter('semua');

    pills.forEach(btn => {
      btn.addEventListener('click', () => {
        pills.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyFilter(btn.dataset.filter);
      });
    });
  });