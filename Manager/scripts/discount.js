document.addEventListener('DOMContentLoaded', function () {
    // 商品カードをクリックしたときのイベントを設定
    const productCards = document.querySelectorAll('.product-card');
    const dialog = document.getElementById('product-dialog');
    const dialogContent = document.querySelector('.dialog-content');
    const productNameElem = document.getElementById('dialog-product-name');
    const productPriceElem = document.getElementById('dialog-product-price');
    const discountRateInput = document.getElementById('discount-rate');
    const discountedPriceElem = document.getElementById('discounted-price');
    const applyDiscountButton = document.getElementById('apply-discount');
    const cancelButton = document.getElementById('cancel-discount');

    let currentProduct = null; // 現在操作中の商品を保存

    productCards.forEach(card => {
        card.addEventListener('click', function () {
            // 商品名と値段を取得
            const productName = card.querySelector('.product-info p:nth-child(1)').textContent.replace('名前：', '').trim();
            const productPriceText = card.querySelector('.product-info p:nth-child(3)').textContent.replace('元値段：', '').trim();
            const productPrice = parseFloat(productPriceText);

            // 商品価格が有効かどうかチェック
            if (isNaN(productPrice)) {
                console.error('商品価格が無効です:', productPriceText);
                alert('商品の価格が正しく取得できませんでした');
                return;
            }

            // 現在の商品情報を保存
            currentProduct = {
                productId: card.getAttribute('data-product-id'), // 商品ID（data属性から取得）
                productName: productName,
                productPrice: productPrice
            };

            // ダイアログに情報を表示
            productNameElem.textContent = productName;
            productPriceElem.textContent = productPrice.toFixed(2);
            discountedPriceElem.textContent = ''; // 割引後の価格をリセット
            discountRateInput.value = ''; // 割引率の入力をリセット

            // ダイアログを表示
            dialog.style.display = 'flex';
        });
    });

    // 割引率が入力されたときに割引後の価格を計算
    discountRateInput.addEventListener('input', function () {
        const rate = parseFloat(this.value);
        if (!isNaN(rate) && rate >= 0 && rate <= 100) {
            const discountedPrice = currentProduct.productPrice * (1 - rate / 100);
            discountedPriceElem.textContent = discountedPrice.toFixed(2);
        } else {
            discountedPriceElem.textContent = '無効な割引率';
        }
    });

    // 割引を適用する処理
    applyDiscountButton.addEventListener('click', function () {
        const discountRate = parseFloat(discountRateInput.value);
        if (isNaN(discountRate) || discountRate < 0 || discountRate > 100) {
            alert('割引率を正しく入力してください');
            return;
        }

        const discountedPrice = currentProduct.productPrice * (1 - discountRate / 100);

        // サーバーにリクエストを送信
        fetch('./php/apply_discount.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                productId: currentProduct.productId,
                discountRate: discountRate,
                discountedPrice: discountedPrice.toFixed(2)
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('割引が適用されました！');
                    closeDialog();
                    location.reload(); // ページをリロードして更新を反映
                } else {
                    alert('割引の適用に失敗しました: ' + data.message);
                    console.log({
                        productId: currentProduct.productId,
                        discountRate: discountRate,
                        discountedPrice: discountedPrice.toFixed(2)
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('割引の適用中にエラーが発生しました');
            });
    });

    // キャンセルボタンまたはダイアログの外側をクリックしたときにダイアログを閉じる処理
    window.closeDialog = function () {
        dialog.style.display = 'none';
        currentProduct = null; // 現在の商品情報をクリア
    };

    // キャンセルボタン
    cancelButton.addEventListener('click', closeDialog);

    // ダイアログの外側をクリックして閉じる
    dialog.addEventListener('click', function (event) {
        if (!dialogContent.contains(event.target)) {
            closeDialog();
        }
    });
});
