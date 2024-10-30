root: 
create database wearebugs;
GRANT ALL PRIVILEGES ON wearebugs.* TO 'dbuser'@'localhost';

dbuser:
//COLLATE utf8mb4_0900_ai_ci để phân biệt chữ a và A
CREATE TABLE user (
    userid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE COLLATE utf8mb4_0900_ai_ci,
    mail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COLLATE utf8mb4_0900_ai_ci,
    token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
// COLLATE utf8mb4_0900_ai_ci
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
    FOREIGN KEY (storeid) REFERENCES store(storeid),
    FOREIGN KEY (category_id) REFERENCES category(category_id)
);


CREATE TABLE category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    cname VARCHAR(100) NOT NULL COLLATE utf8mb4_0900_ai_ci
);

CREATE TABLE pending_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




GRANT ALL PRIVILEGES ON wearebugs.* TO 'dbuser'@'localhost';
