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
    loadOrderDetails(formattedDate);
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
function loadOrderDetails(date) {
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
                    // 注文詳細のトグル表示
                    toggleOrderDetail(order.order_number, tr);
                });
                orderListBody.appendChild(tr);
            });
        })
        .catch(error => console.error('注文データの取得に失敗しました:', error));
}

// 注文詳細情報を取得
function fetchOrderDetail(orderNumber, orderRow) {
    return fetch(`./php/fetch_order_details.php?order_number=${orderNumber}`)
        .then(response => response.json())
        .then(orderDetails => {
            // 詳細情報を表示するための新しい行を作成
            const detailsRow = document.createElement('tr');
            detailsRow.classList.add('order-details-row'); // 詳細行にクラスを追加してスタイルを設定

            let detailsHTML = `<td colspan="3"><table><thead><tr><th>商品名</th><th>数量</th><th>単価</th><th>小計</th></tr></thead><tbody>`;

            orderDetails.forEach(detail => {
                detailsHTML += `
                    <tr>
                        <td>${detail.pname}</td>
                        <td>${detail.quantity}</td>
                        <td>${detail.price}</td>
                        <td>${detail.total_price}</td>
                    </tr>
                `;
            });

            detailsHTML += `</tbody></table></td>`;
            detailsRow.innerHTML = detailsHTML;

            return detailsRow;
        })
        .catch(error => console.error('注文詳細の取得に失敗しました:', error));
}

// 詳細情報のトグル表示
function toggleOrderDetail(orderNumber, orderRow) {
    // すでに詳細情報行が表示されているかどうかを確認
    const existingDetailRow = orderRow.nextElementSibling;

    if (existingDetailRow && existingDetailRow.classList.contains('order-details-row')) {
        // 詳細情報が表示されている場合は非表示にする
        existingDetailRow.style.display = existingDetailRow.style.display === 'none' ? '' : 'none';
    } else {
        // 詳細情報が表示されていない場合は新たに表示する
        fetchOrderDetail(orderNumber, orderRow).then(detailsRow => {
            orderListBody.insertBefore(detailsRow, orderRow.nextSibling);
        });
    }
}