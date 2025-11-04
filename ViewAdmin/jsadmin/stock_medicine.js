   
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('medicineTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            }
        });

        function openModal() {
            document.getElementById('medicineModal').style.display = 'block';
            document.getElementById('modalFrame').src = 'input_medicine.php';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('medicineModal').style.display = 'none';
            document.getElementById('modalFrame').src = '';
            document.body.style.overflow = 'auto';
            // Reload page to show new data
            location.reload();
        }

        function editMedicine(id) {
            document.getElementById('medicineModal').style.display = 'block';
            document.getElementById('modalFrame').src = 'input_medicine.php?id=' + id;
            document.body.style.overflow = 'hidden';
        }

        function deleteMedicine(id) {
            if (confirm('Apakah Anda yakin ingin menghapus obat ini?')) {
                window.location.href = 'process_medicine.php?action=delete&id=' + id;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('medicineModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Listen for messages from iframe
        window.addEventListener('message', function(event) {
            if (event.data === 'closeModal') {
                closeModal();
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
