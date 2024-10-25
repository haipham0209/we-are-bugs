
l
ko cho sửa tên sname ở Edit
so sánh 2 password khi đăng ký
mỗi lần tạo page mới phải include
xóa cookie ở màn hình cửa hàng
mỗi cửa hàng 1 folder image riêng   
hiện tại chỉ đăng nhập dc 1 thiết bị

ghi nhớ đăng nhập -> sửa thời gian coookie

sau khi đăng nhập->so sánh và tạo token lưu vao cookie, khi vào màn hình authcheck-> lấy dữ liệu vào session

ubuntu:
icacls haikey2.pem /inheritance:r
icacls haikey2.pem /grant:r "${env:USERNAME}:(R)"

scp -i "C:\Sites\ec2\haikey2.pem" -r "C:\Sites\we are" ubuntu@54.145.40.61:/var/www/html

cd "C:\Sites\ec2\"

ssh -i "haikey2.pem" ubuntu@54.145.40.61


cd /var/www/html/we-are-bugs
git pull origin main



git clone https://github.com/haipham0209/we-are-bugs.git











<section id="product-section" class="category">
    <?php foreach ($groupedProducts as $categoryId => $products): ?>
        <div class="group" id="category-<?php echo $categoryId; ?>">
            <h1 class="title">
                <?php 
                    
                    echo ($categoryId == 1) ? "Men" : (($categoryId == 2) ? "Women" : "Child"); 
                ?>
            </h1>
            <div class="product-showcase">
                <?php renderProducts($products); ?>
            </div>
            <button class="show-more-btn" data-group="category-<?php echo $categoryId; ?>">Show More</button>
        </div>
    <?php endforeach; ?>
</section>

<?php include 'products.php'; ?>

<div class="product-container">
    <?php foreach ($categories as $categoryName => $products): ?>
        <div class="group" id="<?php echo $categoryName; ?>">
            <h1 class="title"><?php echo ucfirst($categoryName); ?></h1>
            <div class="product-showcase">
                <?php foreach ($products as $product): ?>
                    <div class="product-content">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <p class="rotated-text">
                            <?php echo $product['name']; ?><br><?php echo $product['price']; ?> ¥
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($products) > 3): ?>
                <button class="show-more-btn" data-group="<?php echo $categoryName; ?>">Show More</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>


<!-- ----------------------------------------------------------- -->
<section class="category">

<h1 class="title">Men</h1>

<!-- <div class="filter-buttons">
    <button class="filter-button">Show All</button>
    <button class="filter-button">Men</button>
    <button class="filter-button">Women</button>
    <button class="filter-button">Children</button>
    <button class="filter-button">Special Events</button>
</div> -->
<!-- Product Showcase -->
<div class="product-showcase">
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
</div>
<button class="show-more-btn">Show More</button>
<h2 class="collection-title">Fall-Winter Collection</h2>
</section>



<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>