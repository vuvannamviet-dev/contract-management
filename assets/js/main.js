// Main JavaScript for Contract Management System

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục này?')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-calculate total amount in forms
    const quantityInputs = document.querySelectorAll('.item-quantity');
    const priceInputs = document.querySelectorAll('.item-price');
    
    function calculateTotal() {
        const rows = document.querySelectorAll('.item-row');
        let grandTotal = 0;
        
        rows.forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
            const total = quantity * price;
            
            const totalField = row.querySelector('.item-total');
            if (totalField) {
                totalField.value = total.toFixed(2);
            }
            
            grandTotal += total;
        });
        
        const grandTotalField = document.getElementById('grand-total');
        if (grandTotalField) {
            grandTotalField.value = grandTotal.toFixed(2);
        }
    }
    
    quantityInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
    
    priceInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
    
    // Format currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            let value = parseFloat(this.value.replace(/[^0-9.]/g, ''));
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });
    
    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName || 'Choose file';
            }
        });
    });
});

// Format number as currency
function formatCurrency(amount, currency = 'VND') {
    if (currency === 'VND') {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Print function
function printPage() {
    window.print();
}

// Export to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push('"' + col.textContent.trim() + '"');
        });
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
