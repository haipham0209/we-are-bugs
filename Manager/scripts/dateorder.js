// 取得相關 DOM 元素
const prevDateButton = document.getElementById('prev-date');
const nextDateButton = document.getElementById('next-date');
const datePicker = document.getElementById('date-picker');

const orderDetails = document.getElementById("order-details");
const orderDetailsBody = document.getElementById("order-details-body");
const orderSummary = document.getElementById("order-summary");
// 初始化日期為今天
let currentDate = new Date();
updateDateDisplay(currentDate);

// 更新日期選擇器的值
function updateDateDisplay(date) {
    const formattedDate = date.toISOString().split('T')[0]; // yyyy-mm-dd 格式
    datePicker.value = formattedDate;
    // Gửi yêu cầu AJAX để lấy dữ liệu doanh thu và lợi nhuận
    // fetchRevenueData(formattedDate);
    loadOrderDetails(formattedDate);
}

// 日期加一天
nextDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDateDisplay(currentDate);
});

// 日期減一天
prevDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDateDisplay(currentDate);
});

// 當使用者在日期選擇器中選擇日期時更新顯示
datePicker.addEventListener('change', (event) => {
    const selectedDate = datePicker.value;
    console.log(selectedDate);
    fetch("./php/order_process.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `selected_date=${selectedDate}`
    })
        .then(response => response.json())
        .then(data => {
            orderListBody.innerHTML = "";
            data.forEach((order, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${order.order_number}</td>
                    <td>${order.total_quantity}</td>
                    <td>${order.total_price}</td>
                `;
                row.addEventListener("click", () => loadOrderDetails(order.order_number));
                orderListBody.appendChild(row);
            });
        });
});
function loadOrderDetails(orderNumber) {
    fetch("./php/order_process.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `order_number=${orderNumber}`
    })
        .then(response => response.json())
        .then(data => {
            orderDetails.style.display = "block";
            orderDetailsBody.innerHTML = "";
            let totalPrice = 0;

            data.forEach(detail => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${detail.pname}</td>
                    <td>${detail.quantity}</td>
                    <td>${detail.price}</td>
                    <td>${detail.quantity * detail.price}</td>
                `;
                totalPrice += detail.quantity * detail.price;
                orderDetailsBody.appendChild(row);
            });

            orderSummary.innerHTML = `
                合計金額: ${totalPrice}<br>
                お預かり: ${data[0].received_amount}<br>
                お釣り: ${data[0].change_amount}<br>
                割引: ${data[0].discount}
            `;
        });
}

// Hàm gửi yêu cầu AJAX để lấy dữ liệu doanh thu và lợi nhuận
// function fetchRevenueData(date) {
//     fetch(`./php/get_revenue.php?date=${date}`)
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 salesElement.textContent = data.total_revenue ;
//                 profitElement.textContent = data.total_profit ;
//             } else {
//                 salesElement.textContent = '_';
//                 profitElement.textContent = '_';
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching revenue data:', error);
//             salesElement.textContent = '';
//             profitElement.textContent = '';
//         });
// }
