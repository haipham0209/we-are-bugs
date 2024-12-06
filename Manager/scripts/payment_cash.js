function prepareFormData() {
    // Thu thập dữ liệu từ bảng sản phẩm
    const rows = document.querySelectorAll("#product-table tbody tr");
    const products = [];

    rows.forEach(row => {
        const product = {
            barcode: row.querySelector("input.product-quantity").dataset.barcode,
            quantity: parseInt(row.querySelector("input.product-quantity").value, 10),
            price: parseFloat(row.querySelector("td:nth-child(4)").textContent.replace("¥", "").trim()),
        };
        products.push(product);
        console.log("11111111111111");
        console.log(products);
    });

    // Thu thập dữ liệu thanh toán
    const totalPrice = parseFloat(document.getElementById("total-price").textContent.replace("¥", "").trim());
    const receivedAmount = parseFloat(document.getElementById("received-amount").value);

    // Trả về dữ liệu dạng JSON
    return {
        products: products,
        total_price: totalPrice,
        received_amount: receivedAmount,
    };
    
}

// Gửi dữ liệu qua fetch
function sendDataToServer() {
    const data = prepareFormData();

    // Gửi qua fetch
    // console.log("Dữ liệu JSON gửi đi:", JSON.stringify(data));

    fetch('./php/process_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => {
            console.log("HTTP status code:", response.status);
            return response.text();  // Lấy nội dung phản hồi dưới dạng text
        })
        .then(text => {
            console.log("Nội dung trả về từ server:", text);  // Log nội dung trả về từ server
            try {
                const result = JSON.parse(text);  // Chuyển đổi text thành JSON
                console.log("Kết quả từ server:", result);
                if (result.success) {
                    // alert(`Thanh toán hoàn tất! Mã đơn hàng: ${result.order_id}`);
                    alert(`請求成功しました。: ${result.order_number}`);
                } else if (result.error) {
                    // alert(`Lỗi: ${result.error}`);
                    alert(`エラー: ${result.error}`);
                }
            } catch (e) {
                // console.error("Lỗi khi parse JSON:", e);
                alert("エラー発生しました。");
            }
        })
        .catch(error => {
            console.error("Lỗi:", error);
            alert("エラー" + error.message);
        });
    
}

// Gắn sự kiện bấm nút "完了"
// document.querySelector(".button-pay").addEventListener("click", function (event) {
//     event.preventDefault(); // Ngăn submit mặc định
//     sendDataToServer();
// });
