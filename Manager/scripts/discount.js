document.addEventListener('DOMContentLoaded', function () {
    // 商品カードをクリックしたときのイベントを設定
    const productCards = document.querySelectorAll('.product-card');
    const dialog = document.getElementById('product-dialog');
    const productNameElem = document.getElementById('dialog-product-name');
    const productPriceElem = document.getElementById('dialog-product-price');

    productCards.forEach(card => {
        card.addEventListener('click', function () {
            // 商品名と値段を取得
            const productName = card.querySelector('.product-info p:nth-child(1)').textContent.replace('名前：', '').trim();
            const productPrice = card.querySelector('.product-info p:nth-child(3)').textContent.replace('値段：', '').trim();

            // ダイアログに情報を表示
            productNameElem.textContent = productName;
            productPriceElem.textContent = productPrice;

            // ダイアログを表示
            dialog.style.display = 'flex';
        });
    });

    // ダイアログを閉じる処理
    window.closeDialog = function () {
        dialog.style.display = 'none';
    };
});
