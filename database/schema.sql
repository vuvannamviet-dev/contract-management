-- Contract Management System Database Schema
-- Version: 1.0.0

-- Drop existing database if exists
DROP DATABASE IF EXISTS contract_management;

-- Create database
CREATE DATABASE contract_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE contract_management;

-- Suppliers table
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    tax_code VARCHAR(50),
    bank_account VARCHAR(100),
    bank_name VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    tax_code VARCHAR(50),
    bank_account VARCHAR(100),
    bank_name VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Contracts table
CREATE TABLE purchase_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_number VARCHAR(100) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    contract_date DATE NOT NULL,
    contract_year INT NOT NULL,
    archive_number VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'VND',
    status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    payment_terms TEXT,
    notes TEXT,
    folder_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    INDEX idx_contract_number (contract_number),
    INDEX idx_contract_year (contract_year),
    INDEX idx_supplier (supplier_id),
    INDEX idx_status (status),
    INDEX idx_contract_date (contract_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales Contracts table
CREATE TABLE sales_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_number VARCHAR(100) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    contract_date DATE NOT NULL,
    contract_year INT NOT NULL,
    archive_number VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    cost_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'VND',
    status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    payment_terms TEXT,
    notes TEXT,
    folder_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_contract_number (contract_number),
    INDEX idx_contract_year (contract_year),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_contract_date (contract_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Orders table
CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(100) UNIQUE NOT NULL,
    purchase_contract_id INT,
    supplier_id INT NOT NULL,
    po_date DATE NOT NULL,
    po_year INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'VND',
    status ENUM('draft', 'submitted', 'approved', 'completed', 'cancelled') DEFAULT 'draft',
    delivery_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_contract_id) REFERENCES purchase_contracts(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    INDEX idx_po_number (po_number),
    INDEX idx_po_year (po_year),
    INDEX idx_supplier (supplier_id),
    INDEX idx_contract (purchase_contract_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales Orders table
CREATE TABLE sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    so_number VARCHAR(100) UNIQUE NOT NULL,
    sales_contract_id INT,
    customer_id INT NOT NULL,
    so_date DATE NOT NULL,
    so_year INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'VND',
    status ENUM('draft', 'submitted', 'approved', 'completed', 'cancelled') DEFAULT 'draft',
    delivery_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sales_contract_id) REFERENCES sales_contracts(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_so_number (so_number),
    INDEX idx_so_year (so_year),
    INDEX idx_customer (customer_id),
    INDEX idx_contract (sales_contract_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contract Documents table
CREATE TABLE contract_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_type ENUM('purchase', 'sale') NOT NULL,
    contract_id INT NOT NULL,
    document_type ENUM('quotation', 'contract', 'handover', 'payment_request', 'other') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    uploaded_by VARCHAR(100),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX idx_contract (contract_type, contract_id),
    INDEX idx_document_type (document_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contract Items table (for detailed items in contracts)
CREATE TABLE contract_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_type ENUM('purchase', 'sale') NOT NULL,
    contract_id INT NOT NULL,
    item_number INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    unit VARCHAR(50),
    unit_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    cost_price DECIMAL(15,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contract (contract_type, contract_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Numbering Sequences table (for managing contract/order numbering)
CREATE TABLE numbering_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sequence_type ENUM('purchase_contract', 'sales_contract', 'purchase_order', 'sales_order') NOT NULL,
    year INT NOT NULL,
    archive_number VARCHAR(50),
    last_number INT NOT NULL DEFAULT 0,
    prefix VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_sequence (sequence_type, year, archive_number),
    INDEX idx_type_year (sequence_type, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing

-- Sample suppliers
INSERT INTO suppliers (code, name, contact_person, phone, email, address, tax_code) VALUES
('SUP001', 'Công ty TNHH ABC', 'Nguyễn Văn A', '0901234567', 'contact@abc.com', '123 Đường Lê Lợi, Q1, TP.HCM', '0123456789'),
('SUP002', 'Công ty Cổ phần XYZ', 'Trần Thị B', '0912345678', 'info@xyz.com', '456 Đường Nguyễn Huệ, Q1, TP.HCM', '9876543210'),
('SUP003', 'Công ty TNHH Thiết bị 123', 'Lê Văn C', '0923456789', 'sales@123.com', '789 Đường Pasteur, Q3, TP.HCM', '1122334455');

-- Sample customers
INSERT INTO customers (code, name, contact_person, phone, email, address, tax_code) VALUES
('CUS001', 'Công ty TNHH DEF', 'Phạm Văn D', '0934567890', 'contact@def.com', '321 Đường Võ Văn Tần, Q3, TP.HCM', '5544332211'),
('CUS002', 'Công ty Cổ phần GHI', 'Hoàng Thị E', '0945678901', 'info@ghi.com', '654 Đường Cách Mạng Tháng 8, Q10, TP.HCM', '6677889900'),
('CUS003', 'Công ty TNHH JKL', 'Ngô Văn F', '0956789012', 'sales@jkl.com', '987 Đường Điện Biên Phủ, Q.Bình Thạnh, TP.HCM', '9988776655');
