CREATE DATABASE wearebugs;
GRANT ALL PRIVILEGES ON wearebugs.* TO 'dbuser'@'localhost';

USE wearebugs;

-- Bảng user
CREATE TABLE user (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE COLLATE utf8mb4_0900_ai_ci,
    mail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COLLATE utf8mb4_0900_ai_ci,
    token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng store
CREATE TABLE store (
    storeid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    sname VARCHAR(100) NOT NULL COLLATE utf8mb4_0900_ai_ci,
    address VARCHAR(255), 
    tel VARCHAR(20), 
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user FOREIGN KEY (userid) REFERENCES user(userid) 
    ON DELETE CASCADE 
);

-- Bảng category
CREATE TABLE category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    cname VARCHAR(100) NOT NULL COLLATE utf8mb4_0900_ai_ci
);

-- Bảng product
CREATE TABLE product (
    productid INT PRIMARY KEY AUTO_INCREMENT,
    storeid INT,
    category_id INT,
    pname VARCHAR(255) NOT NULL COLLATE utf8mb4_0900_ai_ci,
    price DECIMAL(10, 2) NOT NULL,
    costPrice DECIMAL(10, 2) NOT NULL,
    description TEXT,
    stock_quantity INT NOT NULL,
    barcode VARCHAR(13) UNIQUE NOT NULL,
    productImage VARCHAR(255) NOT NULL,  -- Lưu đường dẫn ảnh
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_store FOREIGN KEY (storeid) REFERENCES store(storeid),
    CONSTRAINT fk_category FOREIGN KEY (category_id) REFERENCES category(category_id),
    CONSTRAINT unique_pname UNIQUE (pname), -- Đảm bảo tên sản phẩm duy nhất
    CONSTRAINT unique_barcode UNIQUE (barcode) -- Đảm bảo barcode duy nhất
);
