// ============================================
// PharmaCare - Transaction JavaScript
// File: transaction.js
// ============================================

// Toggle detail row (expand/collapse)
function toggleDetail(id, type) {
    const key = id + '-' + type;
    const detailRow = document.getElementById('detail-' + key);
    const mainRow = document.getElementById('row-' + key);
    const icon = document.getElementById('icon-' + key);
    
    // Debug log
    console.log('Toggle detail:', { id, type, key, detailRow, mainRow, icon });
    
    if (detailRow && mainRow && icon) {
        detailRow.classList.toggle('show');
        mainRow.classList.toggle('expanded');
        icon.classList.toggle('rotated');
        
        // Log status
        console.log('Detail row is now:', detailRow.classList.contains('show') ? 'visible' : 'hidden');
    } else {
        console.error('Element not found:', { 
            detailRow: !!detailRow, 
            mainRow: !!mainRow, 
            icon: !!icon 
        });
    }
}

// Real-time search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(function() {
            const searchValue = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.transaction-row');
            let visibleCount = 0;
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                const detailRow = row.nextElementSibling;
                
                if (text.includes(searchValue)) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Keep detail row display state based on whether it's expanded
                    if (detailRow && detailRow.classList.contains('detail-row')) {
                        if (detailRow.classList.contains('show')) {
                            detailRow.style.display = '';
                        } else {
                            detailRow.style.display = 'none';
                        }
                    }
                } else {
                    row.style.display = 'none';
                    if (detailRow && detailRow.classList.contains('detail-row')) {
                        detailRow.style.display = 'none';
                    }
                }
            });
            
            console.log('Search results:', visibleCount + ' transactions found');
            
            // Show "no results" message if needed
            if (visibleCount === 0 && searchValue !== '') {
                showNoResults();
            } else {
                hideNoResults();
            }
        }, 300); // Debounce 300ms
    });
    
    console.log('Search functionality initialized');
} else {
    console.warn('Search input not found');
}

// Show no results message
function showNoResults() {
    const tbody = document.getElementById('transactionTableBody');
    let noResultsRow = document.getElementById('noResultsRow');
    
    if (!noResultsRow && tbody) {
        noResultsRow = document.createElement('tr');
        noResultsRow.id = 'noResultsRow';
        noResultsRow.innerHTML = `
            <td colspan="8" style="text-align: center; padding: 40px;">
                <h3 style="color: #999;">🔍 Tidak ada hasil ditemukan</h3>
                <p style="color: #ccc; margin-top: 10px;">Coba kata kunci lain</p>
            </td>
        `;
        tbody.appendChild(noResultsRow);
    }
}

// Hide no results message
function hideNoResults() {
    const noResultsRow = document.getElementById('noResultsRow');
    if (noResultsRow) {
        noResultsRow.remove();
    }
}

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('id-ID', options);
}

// Export to Excel (optional feature)
function exportToExcel() {
    const table = document.querySelector('table');
    if (!table) {
        alert('Tidak ada data untuk di-export');
        return;
    }
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    // Get headers (skip first expand column)
    const headers = [];
    const headerCells = rows[0].querySelectorAll('th');
    for (let i = 1; i < headerCells.length; i++) {
        headers.push(headerCells[i].textContent.trim());
    }
    csv.push(headers.join(','));
    
    // Get data rows (only main transaction rows, not detail rows)
    const dataRows = document.querySelectorAll('.transaction-row');
    dataRows.forEach(function(row) {
        if (row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            const rowData = [];
            
            // Skip first column (expand icon)
            for (let i = 1; i < cols.length; i++) {
                let cellText = cols[i].textContent.trim();
                // Remove commas and clean text
                cellText = cellText.replace(/,/g, '');
                rowData.push('"' + cellText + '"');
            }
            csv.push(rowData.join(','));
        }
    });
    
    // Create download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const today = new Date().toISOString().split('T')[0];
    link.setAttribute('href', url);
    link.setAttribute('download', 'PharmaCare_Transactions_' + today + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert('✅ Data berhasil di-export!');
    console.log('Exported ' + (csv.length - 1) + ' transactions');
}

// Print transaction
function printTransaction() {
    window.print();
}

// Confirm delete
function confirmDelete(id, type) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        window.location.href = 'delete_transaction.php?id=' + id + '&type=' + type;
        return true;
    }
    return false;
}

// Auto-submit form on filter change (optional)
const filterSelects = document.querySelectorAll('#filterType, #filterStatus');
filterSelects.forEach(function(select) {
    select.addEventListener('change', function() {
        // Uncomment line below if you want auto-submit on change
        // document.getElementById('filterForm').submit();
        console.log('Filter changed:', this.id, '=', this.value);
    });
});

// Close all expanded rows
function closeAllDetails() {
    const detailRows = document.querySelectorAll('.detail-row.show');
    detailRows.forEach(function(row) {
        row.classList.remove('show');
    });
    
    const expandedRows = document.querySelectorAll('.transaction-row.expanded');
    expandedRows.forEach(function(row) {
        row.classList.remove('expanded');
    });
    
    const icons = document.querySelectorAll('.expand-icon.rotated');
    icons.forEach(function(icon) {
        icon.classList.remove('rotated');
    });
    
    console.log('All details closed');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F: Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
            console.log('Search focused via keyboard');
        }
    }
    
    // Escape: Close all expanded details
    if (e.key === 'Escape') {
        closeAllDetails();
        
        // Clear search if focused
        if (document.activeElement === searchInput) {
            searchInput.blur();
        }
    }
});

// Initialize tooltips (optional)
function initTooltips() {
    const elements = document.querySelectorAll('[data-tooltip]');
    elements.forEach(function(el) {
        el.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = el.getAttribute('data-tooltip');
            tooltip.style.position = 'absolute';
            tooltip.style.background = '#333';
            tooltip.style.color = '#fff';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '12px';
            tooltip.style.zIndex = '9999';
            tooltip.style.pointerEvents = 'none';
            document.body.appendChild(tooltip);
            
            const rect = el.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            
            el._tooltip = tooltip;
        });
        
        el.addEventListener('mouseleave', function() {
            if (el._tooltip) {
                document.body.removeChild(el._tooltip);
                el._tooltip = null;
            }
        });
    });
    
    console.log('Tooltips initialized for', elements.length, 'elements');
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide scroll to top button
window.addEventListener('scroll', function() {
    const scrollBtn = document.getElementById('scrollTopBtn');
    if (scrollBtn) {
        if (window.pageYOffset > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    }
});

// Check if table has data
function checkTableData() {
    const tbody = document.getElementById('transactionTableBody');
    if (!tbody) {
        console.error('Transaction table body not found!');
        return false;
    }
    
    const rows = tbody.querySelectorAll('.transaction-row');
    console.log('Total transaction rows found:', rows.length);
    
    if (rows.length === 0) {
        console.warn('No transaction rows found. Check if data is being loaded from PHP.');
    }
    
    return rows.length > 0;
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('=================================');
    console.log('PharmaCare Transaction Page Loaded');
    console.log('=================================');
    
    // Check if data exists
    const hasData = checkTableData();
    console.log('Has transaction data:', hasData);
    
    // Initialize features
    initTooltips();
    
    // Log filter values
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        const formData = new FormData(filterForm);
        console.log('Current filters:');
        for (let [key, value] of formData.entries()) {
            if (value) console.log('  -', key + ':', value);
        }
    }
    
    // Check for common issues
    if (!document.getElementById('transactionTableBody')) {
        console.error('❌ Transaction table body (#transactionTableBody) not found!');
    }
    
    if (!document.getElementById('searchInput')) {
        console.warn('⚠️ Search input (#searchInput) not found!');
    }
    
    console.log('=================================');
});

// Add visual feedback for row clicks
document.addEventListener('click', function(e) {
    const row = e.target.closest('.transaction-row');
    if (row) {
        console.log('Transaction row clicked:', row.id);
    }
});