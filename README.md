# online-shopping

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  image VARCHAR(255),
  category_id INT UNSIGNED,
  category_name VARCHAR(255) NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending','paid','cancelled','shipped') DEFAULT 'pending',
  stripe_session_id VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  payment_method VARCHAR(20) DEFAULT 'paypal',
  payment_status VARCHAR(20) DEFAULT 'pending',
  paypal_order_id VARCHAR(100),
  FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE single_order (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE payments (
  payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES single_order(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);