const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');

const editableFields = [
  document.getElementById('fullname'),
  document.getElementById('phone'),
  document.getElementById('birth'),
  document.getElementById('gender'),
  document.getElementById('city'),
  document.getElementById('province'),
  document.getElementById('address')
];

function enableEditMode() {
  editableFields.forEach(field => {
    if (field.tagName === 'SELECT') {
      field.disabled = false;
    } else {
      field.removeAttribute('readonly');
    }
  });

  editBtn.style.display = 'none';
  saveBtn.style.display = 'inline-flex';
  cancelBtn.style.display = 'inline-flex';
}

function disableEditMode() {
  editableFields.forEach(field => {
    if (field.tagName === 'SELECT') {
      field.disabled = true;
    } else {
      field.setAttribute('readonly', true);
    }
  });

  editBtn.style.display = 'inline-flex';
  saveBtn.style.display = 'none';
  cancelBtn.style.display = 'none';
}

editBtn.addEventListener('click', enableEditMode);

// Event listener untuk tombol Cancel
cancelBtn.addEventListener('click', () => {
  restoreOriginalValues();
  disableEditMode();
});

// Optional: Konfirmasi sebelum leave page saat edit mode
let isEditMode = false;
editBtn.addEventListener('click', () => { isEditMode = true; });
saveBtn.addEventListener('click', () => { isEditMode = false; });
cancelBtn.addEventListener('click', () => { isEditMode = false; });

window.addEventListener('beforeunload', (e) => {
  if (isEditMode) {
    e.preventDefault();
    e.returnValue = '';
    return '';
  }
});