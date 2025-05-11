// Global Axios configuration
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Transaction Form Handler
const transactionForm = {
    init: function() {
        const form = document.getElementById('transaction-form');
        if (!form) return;
        
        form.addEventListener('submit', this.handleSubmit);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const isEdit = form.dataset.edit === 'true';
        const url = isEdit ? `/api/transactions/${form.dataset.id}` : '/api/transactions';
        const method = isEdit ? 'put' : 'post';
        
        axios({
            method: method,
            url: url,
            data: data
        })
        .then(response => {
            if (response.data.success) {
                window.location.href = '/transactions';
            }
        })
        .catch(error => {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach(field => {
                const input = document.getElementById(field);
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.innerText = errors[field][0];
                input.parentNode.appendChild(feedback);
            });
        });
    }
};

// Category Form Handler
const categoryForm = {
    init: function() {
        const form = document.getElementById('category-form');
        if (!form) return;
        
        form.addEventListener('submit', this.handleSubmit);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const isEdit = form.dataset.edit === 'true';
        const url = isEdit ? `/api/categories/${form.dataset.id}` : '/api/categories';
        const method = isEdit ? 'put' : 'post';
        
        axios({
            method: method,
            url: url,
            data: data
        })
        .then(response => {
            if (response.data.success) {
                window.location.href = '/categories';
            }
        })
        .catch(error => {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach(field => {
                const input = document.getElementById(field);
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.innerText = errors[field][0];
                input.parentNode.appendChild(feedback);
            });
        });
    }
};

// Reports Handler
const reportsHandler = {
    init: function() {
        const form = document.getElementById('report-form');
        if (!form) return;
        
        form.addEventListener('submit', this.handleSubmit);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        const form = e.target;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        axios.get('/api/reports', {
            params: {
                start_date: startDate,
                end_date: endDate
            }
        })
        .then(response => {
            if (response.data.success) {
                const data = response.data.data;
                
                // Update summary cards
                document.getElementById('total-income').innerText = `Rp${data.total_income.toLocaleString('id-ID')}`;
                document.getElementById('total-expense').innerText = `Rp${data.total_expense.toLocaleString('id-ID')}`;
                document.getElementById('net-balance').innerText = `Rp${data.net_balance.toLocaleString('id-ID')}`;
                
                // Update charts
                updateCharts(data);
                
                // Update daily summary table
                updateDailySummaryTable(data.daily_summary);
            }
        })
        .catch(error => {
            console.error('Error fetching report data:', error);
        });
    }
};

// Initialize all handlers
document.addEventListener('DOMContentLoaded', function() {
    transactionForm.init();
    categoryForm.init();
    reportsHandler.init();
});

// Helper functions
function updateCharts(data) {
    // Update income chart
    if (window.incomeChart) {
        window.incomeChart.data.labels = data.income_by_category.map(category => category.name);
        window.incomeChart.data.datasets[0].data = data.income_by_category.map(category => category.transactions_sum_amount);
        window.incomeChart.data.datasets[0].backgroundColor = data.income_by_category.map(category => category.color);
        window.incomeChart.update();
    }
    
    // Update expense chart
    if (window.expenseChart) {
        window.expenseChart.data.labels = data.expense_by_category.map(category => category.name);
        window.expenseChart.data.datasets[0].data = data.expense_by_category.map(category => category.transactions_sum_amount);
        window.expenseChart.data.datasets[0].backgroundColor = data.expense_by_category.map(category => category.color);
        window.expenseChart.update();
    }
}

function updateDailySummaryTable(dailySummary) {
    const tableBody = document.querySelector('#daily-summary-table tbody');
    tableBody.innerHTML = '';
    
    dailySummary.forEach(summary => {
        const row = document.createElement('tr');
        
        const dateCell = document.createElement('td');
        dateCell.innerText = new Date(summary.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        row.appendChild(dateCell);
        
        const incomeCell = document.createElement('td');
        incomeCell.innerText = `Rp${summary.income.toLocaleString('id-ID')}`;
        incomeCell.classList.add('text-success');
        row.appendChild(incomeCell);
        
        const expenseCell = document.createElement('td');
        expenseCell.innerText = `Rp${summary.expense.toLocaleString('id-ID')}`;
        expenseCell.classList.add('text-danger');
        row.appendChild(expenseCell);
        
        const balanceCell = document.createElement('td');
        balanceCell.innerText = `Rp${summary.balance.toLocaleString('id-ID')}`;
        balanceCell.classList.add(summary.balance >= 0 ? 'text-success' : 'text-danger');
        row.appendChild(balanceCell);
        
        tableBody.appendChild(row);
    });
}
