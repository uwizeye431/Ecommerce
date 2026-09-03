CREATE DATABASE IF NOT EXISTS ecommerce_platform;
USE ecommerce_platform;

CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50),
    Email VARCHAR(100) UNIQUE,
    Password VARCHAR(255),
    Role ENUM('customer', 'admin') DEFAULT 'customer',
    Status ENUM('active','inactive') DEFAULT 'active',
    LastLogin DATETIME NULL
);

CREATE TABLE IF NOT EXISTS Customers (
    CustomerID INT PRIMARY KEY,
    ShippingAddress TEXT,
    PhoneNumber VARCHAR(20),
    FOREIGN KEY (CustomerID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS Admins (
    AdminID INT PRIMARY KEY,
    AdminLevel VARCHAR(20),
    FOREIGN KEY (AdminID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS Products (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(200),
    Price DECIMAL(10,2),
    Stock INT,
    CategoryID INT,
    ImageURL VARCHAR(500),
    Description TEXT,
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE IF NOT EXISTS ShoppingCart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT,
    ProductID INT,
    Quantity INT,
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID),
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

CREATE TABLE IF NOT EXISTS Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT,
    OrderDate DATETIME,
    Status VARCHAR(50),
    TotalPrice DECIMAL(10,2),
    ShippingAddress TEXT,
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID)
);

CREATE TABLE IF NOT EXISTS OrderItems (
    OrderItemID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    ProductID INT,
    Quantity INT,
    Price DECIMAL(10,2),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

CREATE TABLE IF NOT EXISTS Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    Amount DECIMAL(10,2),
    PaymentMethod VARCHAR(50),
    Status VARCHAR(50),
    TransactionID VARCHAR(100),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);

CREATE TABLE IF NOT EXISTS ActivityLogs (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    Action VARCHAR(255),
    CreatedAt DATETIME,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS ArchivedUsers (
    ArchiveID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    Username VARCHAR(50),
    Email VARCHAR(100),
    Role ENUM('customer','admin'),
    Status ENUM('active','inactive'),
    ArchivedAt DATETIME
);

CREATE TABLE IF NOT EXISTS ContactMessages (
    MessageID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Email VARCHAR(150),
    Message TEXT,
    CreatedAt DATETIME
);

-- Sample data
INSERT INTO Users (Username, Email, Password, Role) VALUES
('Admin One', 'admin@example.com', '$2y$10$e0NRBQBg83CDnwuBXFz.LuAnNX0iWYwGnT1spVebBSmPju0/Zkz02', 'admin'), -- Password: Admin123
('Alice', 'alice@example.com', '$2y$10$e0NRBQBg83CDnwuBXFz.LuAnNX0iWYwGnT1spVebBSmPju0/Zkz02', 'customer'), -- Password: Admin123
('Bob', 'bob@example.com', '$2y$10$e0NRBQBg83CDnwuBXFz.LuAnNX0iWYwGnT1spVebBSmPju0/Zkz02', 'customer'); -- Password: Admin123

INSERT INTO Customers (CustomerID, ShippingAddress, PhoneNumber) VALUES
(2, 'Kigali, Rwanda', '250788000111'),
(3, 'Huye, Rwanda', '250788000222');

INSERT INTO Admins (AdminID, AdminLevel) VALUES (1, 'super');

INSERT INTO Categories (CategoryName) VALUES
('Electronics'), ('Home'), ('Fashion'), ('Groceries'), ('Accessories');

INSERT INTO Products (Name, Price, Stock, CategoryID, ImageURL, Description) VALUES
('Smartphone X', 450000, 12, 1, 'assets/images/iphone.jpg', 'High-end smartphone with great camera.'),
('Wireless Headphones', 120000, 25, 1, 'assets/images/stand.jpg', 'Noise-cancelling over-ear headphones.'),
('Laptop Pro 14"', 980000, 8, 1, 'assets/images/television.webp', 'Lightweight laptop for work and study.'),
('Coffee Maker', 80000, 15, 2, 'assets/images/stand.jpg', 'Brew rich coffee at home.'),
('Blender Max', 65000, 20, 2, 'assets/images/stand.jpg', 'Powerful blender for juices and smoothies.'),
('Sneakers', 95000, 30, 3, 'assets/images/shoes.jpg', 'Comfortable everyday sneakers.'),
('Classic T-Shirt', 25000, 50, 3, 'assets/images/tshirt.jpg', 'Soft cotton t-shirt in multiple sizes.'),
('Premium Rice 5kg', 18000, 40, 4, 'assets/images/rwandaform.jpg', 'High quality long grain rice.'),
('Sunflower Oil 3L', 21000, 35, 4, 'assets/images/rwandaform.jpg', 'Cooking oil for everyday meals.'),
('Leather Wallet', 40000, 18, 5, 'assets/images/pent.jpg', 'Durable leather wallet with card slots.'),
('Gamming tools', 50000, 12, 1, 'assets/images/joystick.jpg', 'Game joystick for immersive play.');

