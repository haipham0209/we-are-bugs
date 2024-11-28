document.addEventListener('DOMContentLoaded', function () {
    // Định nghĩa các phần tử
    const searchBox = document.getElementById('barcode-input'); // Ô nhập liệu
    const suggestionList = document.getElementById('barcode-suggestions'); // Danh sách gợi ý

    // Lắng nghe sự kiện input
    searchBox.addEventListener('input', function () {
        const keyword = searchBox.value.trim();
        if (keyword.length > 0) {
            // Gửi yêu cầu đến PHP API
            fetch('./php/search_product.php?keyword=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    // Xóa gợi ý cũ
                    suggestionList.innerHTML = '';
                    suggestionList.style.display = 'block'; // Hiển thị danh sách

                    // Duyệt danh sách sản phẩm trả về
                    data.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item'; // Thêm class để tiện style

                        // Tạo phần tử div cho tên sản phẩm
                        const nameDiv = document.createElement('div');
                        nameDiv.textContent = `${product.pname}`;
                        div.appendChild(nameDiv);

                        // Tạo phần tử img cho ảnh sản phẩm
                        const img = document.createElement('img');
                        img.src = product.productImage; // Giả sử trường productImage chứa đường dẫn đến ảnh sản phẩm
                        img.alt = product.pname;
                        img.style.width = '50px'; // Đặt kích thước ảnh (bạn có thể thay đổi theo nhu cầu)
                        img.style.marginLeft = '10px'; // Khoảng cách giữa tên sản phẩm và ảnh
                        div.appendChild(img);

                        div.dataset.id = product.productid; // Lưu ID sản phẩm

                        div.addEventListener('click', () => {
                            // Khi chọn sản phẩm, gán vào ô input
                            searchBox.value = ``;
                            suggestionList.innerHTML = ''; // Xóa danh sách gợi ý
                            suggestionList.style.display = 'none'; // Ẩn danh sách

                            // Thêm sản phẩm vào giỏ hàng (gọi hàm addToCart)
                            addToCart(product);
                        });

                        suggestionList.appendChild(div);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            // Xóa danh sách nếu từ khóa trống
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });

    // Ẩn danh sách khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (!suggestionList.contains(e.target) && e.target !== searchBox) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });

    // Hàm thêm sản phẩm vào giỏ hàng
    function addToCart(product) {
        const tableBody = document.querySelector('#product-table tbody');

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        const existingRow = Array.from(tableBody.rows).find(row => {
            const barcode = row.querySelector('input.product-quantity').dataset.barcode;
            return barcode === product.barcode;
        });

        if (existingRow) {
            // Nếu đã tồn tại, tăng số lượng
            const quantityInput = existingRow.querySelector('input.product-quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;

            // Cập nhật giá tổng
            const priceCell = existingRow.querySelector('.price');
            const unitPrice = parseFloat(product.price);
            priceCell.textContent = `${(unitPrice * parseInt(quantityInput.value)).toFixed(2)}¥`;

            // Thêm hiệu ứng highlight
            existingRow.classList.add('highlight');
            setTimeout(() => {
                existingRow.classList.add('fade-out');
                setTimeout(() => {
                    existingRow.classList.remove('highlight', 'fade-out');
                }, 1000); // Xóa class highlight và fade-out sau 1 giây
            }, 500); // Hiệu ứng highlight kéo dài 0.5 giây
        } else {
            // Nếu chưa có, thêm sản phẩm mới vào giỏ hàng
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${product.pname}</td>
                <td>
                    <input 
                        type="number" 
                        class="product-quantity" 
                        value="1" 
                        min="1" 
                        data-barcode="${product.barcode}" 
                        onchange="updateProductPrice(this, ${product.price})">
                </td>
                <td class="product-price">${parseFloat(product.price).toFixed(2)}¥</td>
                <td class="price">${parseFloat(product.price).toFixed(2)}¥</td>
            `;
            tableBody.appendChild(row);

            // Thêm hiệu ứng highlight cho dòng mới
            row.classList.add('highlight');
            setTimeout(() => {
                row.classList.add('fade-out');
                setTimeout(() => {
                    row.classList.remove('highlight', 'fade-out');
                }, 1000); // Xóa class highlight và fade-out sau 1 giây
            }, 500); // Hiệu ứng highlight kéo dài 0.5 giây
        }

        // Cập nhật tổng tiền toàn giỏ hàng
        updateTotal();
    }

    // Hàm cập nhật tổng giỏ hàng
    function updateTotal() {
        const tableRows = document.querySelectorAll('#product-table tbody tr');
        let total = 0;

        tableRows.forEach(row => {
            const quantityInput = row.querySelector('.product-quantity');
            const priceCell = row.querySelector('.price');
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(priceCell.textContent.replace('¥', ''));

            total += quantity * price;
        });

        document.querySelector('#total-price').textContent = `Tổng: ¥${total.toFixed(2)}`;
    }
});
