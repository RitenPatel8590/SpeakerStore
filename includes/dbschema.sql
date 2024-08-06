CREATE DATABASE ecommerce;

USE ecommerce;

Drop TABLE users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO categories (category_name) VALUES ('Wireless Speakers'), ('Party Speakers');

SELECT * FROM categories;


INSERT INTO products (product_name, description, price, category_id, image_url) VALUES
('ULT TOWER 10', 'Powerful rich, deep bass sound to make your place feel like a live concert venue.
Enjoy 360° party sound and light to liven up any gathering.
Lift the party with karaoke and guitar', 1499.99, 1, 'images/speaker1.png'),
('ULT FIELD 7', 'Punchy, deep bass sound to boost your music
30 hours battery life, waterproof and dustproof with a stylish handle
Lift the party with karaoke and guitar input', 499.99, 1, 'images/speaker2.png'),
('ULT FIELD 1', 'Enhanced bass and powerful sound from a compact speaker
Easy to grab design and multi-way strap to take it anywhere
Waterproof, dustproof and shockproof with 12 hours of battery life', 129.99, 1, 'images/speaker3.png'),
('SRS-XV500', 'Powerful Party Sound with X-Balanced Speaker Units and front tweeters
25-hour battery life, easy-to-carry handle and IPX4 water-resistant
Karaoke and guitar input to elevate your party.', 549.99, 1, 'images/speaker4.png'),
('SRS-XE300', 'Wider sound with Line-Shape Diffuser
24 hours of battery life and quick charging
Portable size you can take anywhere.', 269.99, 1, 'images/speaker5.png'),
('BRAVIA Theatre U', 'Wireless neckband speaker for cinematic movies and music
Low-latency gaming with wired cable connection
360 Spatial Sound Personalizer creates Dolby Atmos® phantom speakers for immersive surround
Lightweight and flexible with IPX4 splash-proof design for all-day wearing.', 399.99, 1, 'images/speaker6.png'),
('SRS-NS7', 'Wireless neckband speaker for movies and music
360 Spatial Sound Personalizer creates Dolby Atmos® phantom speakers for immersive surround sound
Flexible and ergonomic form-fitting design, and IPX4 splash-proof design for all-day wearing.', 399.99, 1, 'images/speaker7.png'),
('LSPX-S3 GLASS SOUND SPEAKER', 'LED lighting, including candlelight setting with music sync
Intuitive operation by touch and hands-free function
Eight hours of battery life and fully portable.', 399.99, 1, 'images/speaker8.png'),
('XB13 EXTRA BASS™ PORTABLE WIRELESS SPEAKER', 'Powerful and clear sound with Passive Radiator and Off-Centre Diaphragm.
Portable, water- and dust-proof and with a 16-hour battery life
Uses recycled plastics in both the body and the multiway strap.', 79.99, 1, 'images/speaker9.png'),
('SRS-XB32', 'Enjoy three-dimensional sound experience with a LIVE SOUND mode
Line light-Flashing strobe
Waterproof and dustproof, IP67 rated.', 199.99, 1, 'images/speaker10.png'),
('JBL PartyBox Ultimate1', 'The JBL PartyBox Ultimate is a huge, powerful party speaker featuring superior JBL Pro sound and a vibrant lightshow. Its splash-proof and has sturdy wheels for easier transport.', 1799.99, 2, 'images/speaker11.png'),
('JBL PartyBox On-the-Go Essential', 'Portable party speaker with built-in lights and wireless mic', 499.99, 2, 'images/speaker12.png'),
('JBL PartyBox Stage 320', 'Portable party speaker with powerful JBL Pro Sound, an adaptive lightshow, splashproof, replaceable battery, telescopic handle and wheels.', 399.99, 2, 'images/speaker13.png'),
('JBL PartyBox Encore', 'Portable party speaker with 100W powerful sound, built-in dynamic light show, included digital wireless mics, and splash proof design.', 499.99, 2, 'images/speaker14.png'),
('JBL PartyBox 1000', 'Powerful Bluetooth party speaker with full panel light effects.', 1499.99, 2, 'images/speaker15.png'),
('JBL PartyBox On-The-Go', 'Portable party speaker with built-in lights and wireless mic.', 499.99, 2, 'images/speaker16.png'),
('JBL PartyBox 300', 'Portable Bluetooth party speaker with light effects.', 649.99, 2, 'images/speaker17.png'),
('JBL PartyBox 100', 'Powerful portable Bluetooth party speaker with dynamic light show.', 499.99, 2, 'images/speaker18.png'),
('JBL Partybox 710', 'Party speaker with 800W RMS powerful sound, built-in lights and splashproof design.', 949.99, 2, 'images/speaker19.png'),
('JBL Partybox 310', 'Portable party speaker with dazzling lights and powerful JBL Pro Sound.', 699.99, 2, 'images/speaker20.png');

SELECT * FROM products;
