// document.addEventListener("DOMContentLoaded", () => {
//     // Hiển thị ngày và giờ
//     const now = new Date();
//     const formattedDate = now.toLocaleDateString("ja-JP", { year: "numeric", month: "2-digit", day: "2-digit" });
//     const formattedTime = now.toLocaleTimeString("ja-JP", { hour: "2-digit", minute: "2-digit", second: "2-digit" });
//     document.getElementById("date").textContent = `日付: ${formattedDate}`;
//     document.getElementById("time").textContent = `時間: ${formattedTime}`;
// });
document.addEventListener("DOMContentLoaded", () => {
    // Cập nhật ngày và giờ theo thời gian thực
    const updateDateTime = () => {
        const now = new Date();
        const formattedDate = now.toLocaleDateString("ja-JP", { year: "numeric", month: "2-digit", day: "2-digit" });
        const formattedTime = now.toLocaleTimeString("ja-JP", { hour: "2-digit", minute: "2-digit", second: "2-digit" });
        document.getElementById("date").textContent = `日付: ${formattedDate}`;
        document.getElementById("time").textContent = `時間: ${formattedTime}`;
    };
    updateDateTime();
    setInterval(updateDateTime, 1000);
});

// Biến toàn cục để lưu tổng giá, tổng số lượng và giảm giá
let totalPrice = 0;
let totalQuantity = 0;
let discountPercentage = 0; // Phần trăm giảm giá mặc định là 0

// Lắng nghe sự kiện 'barcodeDetected' từ camera.js
document.addEventListener('barcodeDetected', function (event) {
    const barcode = event.detail; // Mã barcode quét được
    processBarcode(barcode); // Gửi mã qua AJAX để xử lý
});
document.getElementById("barcode-input").addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        event.preventDefault(); // Ngăn form gửi đi mặc định
        const barcode = event.target.value.trim(); // Lấy mã barcode từ input

        if (barcode) {
            fetchProduct(barcode); // Gửi yêu cầu AJAX tìm kiếm sản phẩm
        } else {
            alert("Vui lòng nhập mã barcode.");
        }
    }
});

function fetchProduct(barcode) {
    fetch('./php/fetch_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `barcode=${encodeURIComponent(barcode)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.length === 0) {
            alert("Không tìm thấy sản phẩm.");
        } else {
            updateProductTable(data);
        }
    })
    .catch(err => console.error("Error:", err));
}

function updateProductTable(products) {
    const tableBody = document.querySelector("#product-table tbody");
    tableBody.innerHTML = ""; // Xóa nội dung cũ

    products.forEach(product => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${product.pname}</td>
            <td><input type="number" value="1" min="1" class="product-quantity"></td>
            <td>${product.price}¥</td>
        `;
        tableBody.appendChild(row);
    });

    updateTotalPrice(); // Tính lại tổng tiền
}

function updateTotalPrice() {
    const quantities = document.querySelectorAll(".product-quantity");
    const prices = document.querySelectorAll("#product-table tbody tr td:nth-child(3)");
    let total = 0;

    quantities.forEach((input, index) => {
        total += parseInt(input.value) * parseFloat(prices[index].textContent);
    });

    document.getElementById("total-price").textContent = total.toFixed(2) + "¥";
}

// Hàm gửi mã barcode qua AJAX và cập nhật bảng sản phẩm
function processBarcode(barcode) {
    fetch("POS.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `barcode=${barcode}`
    })
        .then(response => {
            if (!response.ok) throw new Error("Lỗi khi kết nối server");
            return response.json();
        })
        .then(products => {
            if (!Array.isArray(products) || products.length === 0) {
                alert("Không tìm thấy sản phẩm.");
                return;
            }
            const table = document.querySelector("#product-table tbody");
            if (!table) throw new Error("Bảng sản phẩm không tồn tại trên trang.");

            table.innerHTML = ''; // Xóa các sản phẩm cũ

            products.forEach(product => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${product.pname}</td>
                    <td><input type="number" value="1" min="1" class="product-quantity"></td>
                    <td>${product.price}¥</td>
                `;
                table.appendChild(row);

                // Gán sự kiện thay đổi số lượng
                let debounceTimeout;
                row.querySelector(".product-quantity").addEventListener("input", () => {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(updateTotalPrice, 300);
                });
            });

            updateTotalPrice(); // Cập nhật tổng tiền sau khi thêm sản phẩm
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Không thể kết nối tới server.");
        });
}

// Cập nhật tổng tiền hiển thị (bao gồm giảm giá)
function updateTotalPrice() {
    totalPrice = 0;
    totalQuantity = 0;

    const rows = document.querySelectorAll("#product-table tbody tr");
    if (rows.length === 0) {
        document.getElementById("total-price").textContent = "0¥";
        document.getElementById("total-quantity").textContent = "0";
        return;
    }

    rows.forEach(row => {
        const quantityInput = row.querySelector(".product-quantity");
        const priceText = row.querySelector("td:nth-child(3)").textContent;
        const price = parseFloat(priceText.replace("¥", "")) || 0;
        const quantity = parseInt(quantityInput.value) || 0;

        totalPrice += price * quantity;
        totalQuantity += quantity;
    });

    const discountAmount = (totalPrice * discountPercentage) / 100;
    const finalPrice = totalPrice - discountAmount;

    document.getElementById("total-price").textContent = `${finalPrice.toFixed(2)}¥`;
    document.getElementById("hidden-total-price").value = finalPrice.toFixed(2);
    document.getElementById("total-quantity").textContent = totalQuantity;
}

// Xử lý sự kiện nhập số tiền nhận được và tính tiền thừa
document.querySelector('#payment-form').addEventListener('input', () => {
    const receivedAmount = parseFloat(document.getElementById('received-amount').value) || 0;
    const discountAmount = (totalPrice * discountPercentage) / 100;
    const finalPrice = totalPrice - discountAmount;

    let changeAmount = receivedAmount - finalPrice;
    if (changeAmount < 0) {
        document.getElementById("change-amount").textContent = `不足: ${Math.abs(changeAmount).toFixed(2)}¥`;
        changeAmount = 0;
    } else {
        document.getElementById("change-amount").textContent = `${changeAmount.toFixed(2)}¥`;
    }

    document.getElementById('hidden-total-price').value = finalPrice.toFixed(2);
    document.getElementById('hidden-received-amount').value = receivedAmount.toFixed(2);
});

// Lắng nghe sự kiện thay đổi giảm giá
document.getElementById("waribiki-input").addEventListener("input", () => {
    let discountInput = parseFloat(document.getElementById("waribiki-input").value) || 0;
    discountInput = Math.max(0, Math.min(discountInput, 100)); // Đưa về giới hạn 0-100
    document.getElementById("waribiki-input").value = discountInput;

    discountPercentage = discountInput;
    updateTotalPrice();
});

// Cập nhật tổng tiền ngay khi tải trang
updateTotalPrice();
