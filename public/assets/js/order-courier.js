const pickupDateEl = document.getElementById('pickupDate');

setTimeout(() => {
    flatpickr(pickupDateEl, {
        minDate: 'today',
        weekNumbers: true,
    });
}, 1000);

new SlimSelect({
    select: '#orders',
    showSearch: true,
    searchHighlight: true,
    closeOnSelect: false,
    allowDeselectOption: true
});

const ordersContainerEl = document.getElementById('orders-container');

const ordersSelectEl = document.getElementById('orders');

ordersSelectEl.addEventListener('change', () => {
    ordersContainerEl.classList.remove('red-border');
});

const orderCourierButtonEl = document.getElementById('orderCourier');

orderCourierButtonEl.addEventListener('click', (e) => {
    if (0 === ordersSelectEl.selectedOptions.length) {
        e.preventDefault();

        ordersContainerEl.classList.add('red-border');
    }
});
