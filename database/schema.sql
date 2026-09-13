

CREATE DATABASE IF NOT EXISTS urbancart_db;
USE urbancart_db;

-- ---------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','member') NOT NULL DEFAULT 'member',
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: categories
-- ---------------------------------------------------------
CREATE TABLE categories (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    image   VARCHAR(255)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: products
-- ---------------------------------------------------------
CREATE TABLE products (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category_id   INT,
    name          VARCHAR(150)  NOT NULL,
    description   TEXT,
    price         DECIMAL(10,2) NOT NULL,
    image         VARCHAR(255),
    stock         INT           NOT NULL DEFAULT 0,
    featured      TINYINT(1)    NOT NULL DEFAULT 0,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: orders
-- ---------------------------------------------------------
CREATE TABLE orders (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    full_name   VARCHAR(150) NOT NULL,
    address     VARCHAR(255) NOT NULL,
    phone       VARCHAR(30)  NOT NULL,
    subtotal    DECIMAL(10,2) NOT NULL,
    shipping    DECIMAL(10,2) NOT NULL DEFAULT 5.00,
    total       DECIMAL(10,2) NOT NULL,
    status      ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: order_items
-- ---------------------------------------------------------
CREATE TABLE order_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT NOT NULL,
    product_id    INT,
    product_name  VARCHAR(150) NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    quantity      INT NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: contact_messages
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    order_number  VARCHAR(50),
    subject       VARCHAR(50)  NOT NULL,
    message       TEXT         NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- Seed data
-- =========================================================

-- Categories (match the existing category banner images)
INSERT INTO categories (name, image) VALUES
('Fashion',      'categories/category1.jpg'),
('Electronics',  'categories/category2.jpg'),
('Accessories',  'categories/category3.jpg');

-- Products (match the existing product images from the static site)
INSERT INTO products (category_id, name, description, price, image, stock, featured) VALUES
(1, 'Sport Sneakers',
    'Lightweight, breathable sport sneakers built for all-day comfort and everyday training.',
    59.99, 'products/product1.jpg', 25, 1),
(3, 'Classic Watch',
    'A timeless analogue wrist watch with a stainless steel case and genuine leather strap.',
    89.99, 'products/product2.jpg', 15, 1),
(1, 'Leather Handbag',
    'Premium genuine-leather handbag with a spacious interior and adjustable strap.',
    74.99, 'products/product3.jpg', 12, 0),
(2, 'Wireless Headphones',
    'Experience premium sound quality with these comfortable wireless headphones, featuring noise cancellation, a 20-hour battery life, and a sleek modern design.',
    45.99, 'products/product4.jpg', 30, 1),
(3, 'Classic Sunglasses',
    'UV-protected classic sunglasses with a durable, lightweight frame.',
    29.99, 'products/product5.jpg', 40, 0),
(3, 'Signature Perfume',
    'A long-lasting signature fragrance with notes of citrus, jasmine and warm musk.',
    65.99, 'products/product6.jpg', 20, 0);

-- Seed users
-- Admin login:  admin@urbancart.com  / Admin@123
-- Member login: jane@urbancart.com   / Member@123
-- (Passwords below are bcrypt hashes produced by PHP's password_hash();
--  change them after first login in a real deployment.)
INSERT INTO users (name, email, password, role) VALUES
('Site Admin', 'admin@urbancart.com', '$2y$10$kXZSjcWTDDof2djfijF5jOwi2KTeCXExvuQRsHVUZ0ZKeproqb9gO', 'admin'),
('Jane Doe',    'jane@urbancart.com',  '$2y$10$Htm1va7arBFf7e.f4gf.n.05hGE3iKeKLtHLqRPfsitmAcBD2VIei', 'member');
