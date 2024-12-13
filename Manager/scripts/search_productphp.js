document.addEventListener('DOMContentLoaded', function () {
    const searchBox = document.getElementById('barcode-input'); // Ô nhập liệu
    const suggestionList = document.getElementById('barcode-suggestions'); // Danh sách gợi ý

    searchBox.addEventListener('input', function () {
        const keyword = searchBox.value.trim();
        if (keyword.length > 0) {
            fetch('./php/search_product.php?keyword=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    suggestionList.innerHTML = '';
                    suggestionList.style.display = 'block';

                    data.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';

                        const nameDiv = document.createElement('div');
                        nameDiv.textContent = `${product.pname}`;
                        div.appendChild(nameDiv);

                        const img = document.createElement('img');
                        img.src = product.productImage;
                        img.alt = product.pname;
                        img.style.width = '50px';
                        img.style.marginLeft = '10px';
                        div.appendChild(img);

                        div.dataset.id = product.productid; // Lưu ID của sản phẩm vào dataset

                        // Sự kiện click cho từng mục gợi ý
                        div.addEventListener('click', () => {
                            searchBox.value = '';
                            suggestionList.innerHTML = '';
                            suggestionList.style.display = 'none';

                            // Cuộn đến phần tử sản phẩm tương ứng
                            const targetElement = document.querySelector(`.product-card[data-product-id="${product.productid}"]`);
                            if (targetElement) {
                                targetElement.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                console.warn('Không tìm thấy phần tử với ID:', product.productid);
                            }
                        });

                        suggestionList.appendChild(div);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });

    // Đóng danh sách gợi ý khi nhấp ra ngoài
    document.addEventListener('click', function (e) {
        if (!suggestionList.contains(e.target) && e.target !== searchBox) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });
});
