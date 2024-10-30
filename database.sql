drop database wearebugs;
create database wearebugs;
use wearebugs

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
    category_id INT NOT NULL, -- Bỏ AUTO_INCREMENT
    userid INT NOT NULL,
    cname VARCHAR(100) NOT NULL,
    PRIMARY KEY (category_id, userid), -- Thay đổi ràng buộc PRIMARY KEY
    FOREIGN KEY (userid) REFERENCES user(userid) ON DELETE CASCADE,
    CONSTRAINT unique_cname UNIQUE (cname, userid) -- Đảm bảo cname duy nhất theo userid
);

-- Bảng product
CREATE TABLE product (
    productid INT NOT NULL,
    userid INT NOT NULL,
    category_id INT NOT NULL,
    pname VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    costPrice DECIMAL(10, 2) NOT NULL,
    description TEXT,
    stock_quantity INT NOT NULL,
    barcode VARCHAR(13) NOT NULL,
    productImage VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userid, productid),
    FOREIGN KEY (userid) REFERENCES user(userid) ON DELETE CASCADE,
    FOREIGN KEY (category_id, userid) REFERENCES category(category_id, userid)
);


