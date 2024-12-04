
function addToCart(product) {
    const tableBody = document.querySelector('#product-table tbody');

    // Kiểm tra xem sản phẩm đã tồn tại trong bảng chưa
    const existingRow = Array.from(tableBody.rows).find(row => {
        const barcode = row.querySelector('input.product-quantity').dataset.barcode;
        return barcode === product.barcode;
    });

    if (existingRow) {
        // Nếu sản phẩm đã tồn tại, tăng số lượng
        const quantityInput = existingRow.querySelector('input.product-quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;

        const priceCell = existingRow.querySelector('.price');
        const unitPrice = parseFloat(product.price);
        priceCell.textContent = `${(unitPrice * parseInt(quantityInput.value)).toFixed(2)}¥`;

        existingRow.classList.add('highlight');
        setTimeout(() => {
            existingRow.classList.remove('highlight');
        }, 1500);
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm hàng mới
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="stt"></td> <!-- Cột STT -->
            <td>${product.pname}</td>
            <td class="num">
                <input 
                    type="number" 
                    class="product-quantity" 
                    value="1" 
                    min="1" 
                    data-barcode="${product.barcode}" 
                    onchange="updateProductPrice(this, ${product.price})">
            </td>
            <td>${parseFloat(product.price).toFixed(2)}¥</td>
            <td class="price">${parseFloat(product.price).toFixed(2)}¥</td>
            <td>
                <button class="delete-btn" title="Xóa">X</button>
            </td>
        `;

        // Thêm dòng mới vào đầu bảng (đẩy các dòng trống xuống)
        tableBody.insertBefore(row, tableBody.firstChild);

        // Thêm sự kiện xóa hàng
        row.querySelector('.delete-btn').addEventListener('click', () => {
            row.classList.add('fade-out'); // Thêm lớp hiệu ứng làm mờ dần

            // Chờ hiệu ứng hoàn tất (300ms) rồi xóa hàng
            setTimeout(() => {
                row.remove(); // Xóa hàng
                updateSerialNumbers(); // Cập nhật lại STT
                updateTotal(); // Cập nhật tổng tiền
                calculateChange();
            }, 300);
        });

        // Thêm hiệu ứng làm nổi bật dòng mới
        row.classList.add('highlight');
        setTimeout(() => {
            row.classList.remove('highlight');
        }, 1500);
    }

    // Cập nhật lại số thứ tự (STT)
    updateSerialNumbers();

    // Cập nhật tổng tiền mỗi lần thêm sản phẩm
    updateTotal();
    calculateChange();
}

function updateSerialNumbers() {
    const tableRows = document.querySelectorAll('#product-table tbody tr');
    tableRows.forEach((row, index) => {
        const sttCell = row.querySelector('.stt');
        sttCell.textContent = index + 1; // Gán STT bắt đầu từ 1
    });
}

//tổng tiền từng món hàng
function updateProductPrice(input, unitPrice) {
    const quantity = parseInt(input.value);

    // Kiểm tra nếu số lượng <= 0, tự động xóa hàng
    if (isNaN(quantity) || quantity <= 0) {
        const row = input.closest('tr'); // Lấy hàng chứa ô nhập liệu
        row.remove(); // Xóa hàng khỏi bảng
        updateSerialNumbers(); // Cập nhật lại số thứ tự (STT)
        updateTotal(); // Cập nhật lại tổng tiền
        calculateChange();
        return; // Kết thúc hàm để không tiếp tục tính toán
    }

    // Tính toán lại giá thành tiền khi số lượng hợp lệ
    const row = input.closest('tr'); // Lấy hàng chứa ô nhập liệu
    const priceCell = row.querySelector('.price'); // Tìm ô giá của hàng
    priceCell.textContent = `${(unitPrice * quantity).toFixed(2)}¥`; // Cập nhật giá tiền

    // Cập nhật tổng tiền
    updateTotal();
    calculateChange();
}
//tổng tiền
function updateTotal() {
    const tableRows = document.querySelectorAll('#product-table tbody tr');
    let total = 0;

    tableRows.forEach(row => {
        const quantityInput = row.querySelector('.product-quantity');
        const priceCell = row.querySelector('.price');
        const quantity = parseInt(quantityInput.value);
        const price = parseFloat(priceCell.textContent.replace('¥', ''));
//price là giá x số lượng
        total +=  price;
    });

    const discountInput = document.getElementById('waribiki-input');
    const discount = parseFloat(discountInput.value) || 0;

    // Áp dụng giảm giá
    const discountedTotal = total - (total * (discount / 100));

    // Làm tròn xuống để chỉ giữ phần nguyên
    document.getElementById('total-price').textContent = `${Math.floor(discountedTotal)}¥`;
}

function calculateChange() {
    const totalElement = document.getElementById('total-price');
    const total = parseFloat(totalElement.textContent.replace('¥', '')) || 0;

    const receivedAmountInput = document.getElementById('received-amount');
    const receivedAmount = parseFloat(receivedAmountInput.value) || 0;

    const change = receivedAmount - total;

    // Hiển thị tiền thừa là số nguyên
    document.getElementById('change-amount').textContent = `${Math.floor(change)}¥`;
}

////////////////////thanh toan //////////////////////
// function getCartData() {
//     const cartItems = [];
//     const rows = document.querySelectorAll('#product-table tbody tr');
    
//     rows.forEach(row => {
//         const barcode = row.querySelector('.product-quantity').dataset.barcode;
//         const quantity = parseInt(row.querySelector('.product-quantity').value);
//         const price = parseFloat(row.querySelector('.price').textContent.replace('¥', ''));
        
//         cartItems.push({
//             barcode: barcode,
//             quantity: quantity,
//             price: price
//         });
//     });
    
//     return cartItems;
// }
// document.querySelector('.button-pay').addEventListener('click', function(event) {
//     event.preventDefault();  // Ngừng hành động mặc định của form

//     // Lấy dữ liệu giỏ hàng
//     const cartData = getCartData();
//     const totalPrice = parseFloat(document.getElementById('total-price').textContent.replace('¥', '')) || 0;
//     const receivedAmount = parseFloat(document.getElementById('received-amount').value) || 0;

//     // Kiểm tra số tiền nhận có đủ không
//     if (receivedAmount < totalPrice) {
//         alert("Số tiền nhận không đủ.");
//         return;
//     }

    // Gửi dữ liệu qua AJAX
//     fetch('./php/process_payment.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({
//             complete: true,
//             total_price: totalPrice,
//             received_amount: receivedAmount,
//             cart: cartData
//         })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             alert(`Thanh toán thành công! Mã đơn hàng: ${data.order_id}`);
//             // Làm mới giỏ hàng hoặc chuyển hướng trang
//         } else {
//             alert(`Lỗi: ${data.error}`);
//         }
//     })
//     .catch(err => {
//         console.error("Lỗi kết nối:", err);
//         alert("Không thể kết nối tới server.");
//     });
// });
//////////////////end thanh toan///////////////////







// let totalAmount = 1;
// function updateTotal() {
//     const discountInput = document.getElementById('waribiki-input');
//     const totalPriceElement = document.getElementById('total-price');
    
//     let discountPercentage = parseFloat(discountInput.value) || 0; // Lấy giá trị giảm giá
//     if (discountPercentage > 100 || discountPercentage < 0) {
//         alert("割引きは0から100の間で指定してください");
//         return;
//     }
    
//     // Tính tổng tiền sau khi giảm giá
//     let discountedTotal = totalAmount * (1 - discountPercentage / 100);
//     totalPriceElement.textContent = `${discountedTotal.toFixed(2)}¥`; // Hiển thị tổng tiền
// }
// document.addEventListener("DOMContentLoaded", () => {
//     // Cập nhật ngày và giờ theo thời gian thực
//     const updateDateTime = () => {
//         const now = new Date();
//         const date = now.toLocaleDateString("ja-JP", { year: "numeric", month: "2-digit", day: "2-digit" });
//         const time = now.toLocaleTimeString("ja-JP", { hour: "2-digit", minute: "2-digit", second: "2-digit" });
//         document.getElementById("date").textContent = `日付: ${date}`;
//         document.getElementById("time").textContent = `時間: ${time}`;
//     };
//     updateDateTime();
//     setInterval(updateDateTime, 1000);

//     // Xử lý quét barcode
//     document.getElementById("barcode-input").addEventListener("keydown", (event) => {
//         if (event.key === "Enter") {
//             event.preventDefault();
//             const barcode = event.target.value.trim();
//             if (barcode) processBarcode(barcode);
//             else alert("Vui lòng nhập mã barcode.");
//         }
//     });
//      // Xử lý thay đổi số lượng sản phẩm
//      document.querySelector("#product-table").addEventListener("input", (event) => {
//         if (event.target.classList.contains("product-quantity")) {
//             const quantity = parseInt(event.target.value) || 0;
//             const barcode = event.target.getAttribute("data-barcode");
//             updateProductPrice(barcode, quantity);
//             updateTotalPrice();
//         }
//     });
//     // Xử lý thanh toán
//     document.querySelector(".button-pay").addEventListener("click", () => {
//         const totalPrice = parseFloat(document.getElementById("hidden-total-price").value) || 0;
//         const receivedAmount = parseFloat(document.getElementById("hidden-received-amount").value) || 0;

//         if (receivedAmount < totalPrice) {
//             alert("Số tiền nhận không đủ.");
//             return;
//         }

//         fetch("/POS.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/x-www-form-urlencoded" },
//             body: new URLSearchParams({
//                 complete: true,
//                 total_price: totalPrice,
//                 received_amount: receivedAmount
//             })
//         })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) alert(`Thanh toán thành công! Mã đơn hàng: ${data.order_id}`);
//                 else alert(`Lỗi: ${data.error}`);
//             })
//             .catch(err => alert("Không thể kết nối tới server."));
//     });
// });

// function processBarcode(barcode) {
//     fetch("/POS.php", {
//         method: "POST",
//         headers: { "Content-Type": "application/x-www-form-urlencoded" },
//         body: new URLSearchParams({ barcode })
//     })
//         .then(response => response.json())
//         .then(data => {
//             if (data.error) {
//                 alert(data.error);
//             } else {
//                 updateProductTable(data);
//             }
//         })
//         .catch(err => console.error("Lỗi khi gửi mã barcode:", err));
// }
// function updateQuantity(inputElement) {
//     const barcode = inputElement.getAttribute("data-barcode");
//     const quantity = parseInt(inputElement.value) || 0;
//     updateProductPrice(barcode, quantity);
//     updateTotalPrice();
// }

// function updateProductTable(products) {
//     const tableBody = document.querySelector("#product-table tbody");
//     products.forEach(product => {
//         const row = document.createElement("tr");
//         row.innerHTML = `
//             <td>${product.pname}</td>
//             <td><input type="number" value="1" class="product-quantity" data-barcode="${product.barcode}" min="1"></td>
//             <td class="product-price">${product.price}¥</td>
//         `;
//         tableBody.appendChild(row);
//     });
//     updateTotalPrice(); 
// }

// function updateProductPrice(barcode, quantity) {
//     const rows = document.querySelectorAll("#product-table tbody tr");
//     rows.forEach(row => {
//         const productBarcode = row.querySelector(".product-quantity").getAttribute("data-barcode");
//         if (productBarcode === barcode) {
//             const price = parseFloat(row.querySelector(".product-price").textContent.replace('¥', '')) || 0;
//             const newPrice = price / row.querySelector(".product-quantity").value * quantity; 
//             row.querySelector(".product-price").textContent = `${newPrice.toFixed(2)}¥`;
//         }
//     });
// }
// function updateTotalPrice() {
//     let totalPrice = 0;
//     const rows = document.querySelectorAll("#product-table tbody tr");
//     rows.forEach(row => {
//         const price = parseFloat(row.querySelector(".product-price").textContent.replace('¥', '')) || 0;
//         totalPrice += price;
//     });
//     document.getElementById("total-price").textContent = `${totalPrice.toFixed(2)}¥`;
//     document.getElementById("hidden-total-price").value = totalPrice.toFixed(2);
// }


