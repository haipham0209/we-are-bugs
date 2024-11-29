function addToCart(product) {
    const tableBody = document.querySelector('#product-table tbody');

    // Tìm hàng sản phẩm đã tồn tại dựa trên barcode
    const existingRow = Array.from(tableBody.rows).find(row => {
        const barcode = row.querySelector('input.product-quantity').dataset.barcode;
        return barcode === product.barcode;
    });

    if (existingRow) {
        // Nếu sản phẩm đã tồn tại, chỉ cập nhật số lượng và tính lại giá
        const quantityInput = existingRow.querySelector('input.product-quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;

        const priceCell = existingRow.querySelector('.price');
        const unitPrice = parseFloat(product.price);
        priceCell.textContent = `${(unitPrice * parseInt(quantityInput.value)).toFixed(2)}¥`;

        // Làm nổi bật hàng để báo người dùng biết đã cập nhật
        existingRow.classList.add('highlight');
        setTimeout(() => {
            existingRow.classList.remove('highlight');
        }, 1500);
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm hàng mới vào bảng
        const row = document.createElement('tr');
        row.innerHTML = `
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
        `;
        tableBody.appendChild(row);

        // Làm nổi bật hàng mới thêm
        row.classList.add('highlight');
        setTimeout(() => {
            row.classList.remove('highlight');
        }, 1500);
    }

    // Cập nhật tổng tiền giỏ hàng
    updateTotal();
}

function updateProductPrice(inputElement, unitPrice) {
    const quantity = parseInt(inputElement.value); // Lấy số lượng từ ô input
    const priceCell = inputElement.closest('tr').querySelector('.price'); // Lấy ô chứa giá tiền

    // Tính tổng tiền cho sản phẩm
    const totalPrice = (quantity * unitPrice).toFixed(2);

    // Cập nhật giá tiền vào ô
    priceCell.textContent = `${totalPrice}¥`;

    // Cập nhật tổng giỏ hàng nếu cần
    // updateTotal();
}



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


