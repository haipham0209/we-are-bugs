const prevDateButton = document.getElementById('prev-date');
const nextDateButton = document.getElementById('next-date');
const datePicker = document.getElementById('date-picker');

const orderListBody = document.getElementById('order-list-body');
const orderDetailsBody = document.getElementById('order-details-body');
const orderDetailsDiv = document.getElementById('order-details');

let currentDate = new Date();
updateDateDisplay(currentDate);

function updateDateDisplay(date) {
    const formattedDate = date.toISOString().split('T')[0];
    datePicker.value = formattedDate;
    loadOrder(formattedDate);
}

nextDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDateDisplay(currentDate);
});

prevDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDateDisplay(currentDate);
});

datePicker.addEventListener('change', (event) => {
    const selectedDate = new Date(event.target.value);
    if (!isNaN(selectedDate)) {
        currentDate = selectedDate;
        updateDateDisplay(currentDate);
    }
});

// 注文データを表示
function loadOrder(date) {
    fetch(`./php/fetch_orders.php?date=${date}`)
        .then(response => response.json())
        .then(orders => {
            // 既存のリストをクリア
            orderListBody.innerHTML = '';
            let index = 1;
            orders.forEach(order => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index++}</td>
                    <td>${order.order_number}</td>
                    <td>${order.total_quantity}</td>
                    <td>${order.total_price}</td>
                `;
                tr.addEventListener('click', () => {
                    // 注文詳細を表示
                    fetchOrderDetail(order.order_number);
                });
                orderListBody.appendChild(tr);
            });
        })
        .catch(error => console.error('注文データの取得に失敗しました:', error));
}

// 注文詳細情報を取得
function fetchOrderDetail(orderNumber) {
    fetch(`./php/fetch_order_details.php?order_number=${orderNumber}`)
        .then(response => response.json())
        .then(orderDetails => {
            // 詳細情報を表示するテーブルをクリア
            orderDetailsBody.innerHTML = '';
            orderDetails.forEach(detail => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${detail.pname}</td>
                    <td>${detail.quantity}</td>
                    <td>${detail.price}</td>
                    <td>${detail.total_price}</td>
                `;
                orderDetailsBody.appendChild(tr);
            });
            // 詳細を表示
            orderDetailsDiv.style.display = 'block';
        })
        .catch(error => console.error('注文詳細の取得に失敗しました:', error));
}