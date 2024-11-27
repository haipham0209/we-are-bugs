
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
        searchBox.value = `${product.pname}`;
        suggestionList.innerHTML = ''; // Xóa danh sách gợi ý
        suggestionList.style.display = 'none'; // Ẩn danh sách
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

    