# Database Schema Documentation

## Overview
Contract Management System uses MySQL/MariaDB with 9 main tables to manage contracts, orders, and related entities.

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│   suppliers     │
│─────────────────│
│ id (PK)         │
│ code (UNIQUE)   │
│ name            │
│ contact_person  │
│ phone           │
│ email           │
│ address         │
│ tax_code        │
│ bank_account    │
│ bank_name       │
│ notes           │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│  purchase_contracts     │
│─────────────────────────│
│ id (PK)                 │
│ contract_number (UNIQUE)│
│ supplier_id (FK)        │
│ contract_date           │
│ contract_year           │
│ archive_number          │
│ title                   │
│ description             │
│ total_amount            │
│ currency                │
│ status                  │
│ start_date              │
│ end_date                │
│ payment_terms           │
│ notes                   │
│ folder_path             │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│   purchase_orders       │
│─────────────────────────│
│ id (PK)                 │
│ po_number (UNIQUE)      │
│ purchase_contract_id(FK)│
│ supplier_id (FK)        │
│ po_date                 │
│ po_year                 │
│ title                   │
│ description             │
│ total_amount            │
│ currency                │
│ status                  │
│ delivery_date           │
│ notes                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘


┌─────────────────┐
│   customers     │
│─────────────────│
│ id (PK)         │
│ code (UNIQUE)   │
│ name            │
│ contact_person  │
│ phone           │
│ email           │
│ address         │
│ tax_code        │
│ bank_account    │
│ bank_name       │
│ notes           │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│   sales_contracts       │
│─────────────────────────│
│ id (PK)                 │
│ contract_number (UNIQUE)│
│ customer_id (FK)        │
│ contract_date           │
│ contract_year           │
│ archive_number          │
│ title                   │
│ description             │
│ total_amount            │
│ cost_amount             │
│ currency                │
│ status                  │
│ start_date              │
│ end_date                │
│ payment_terms           │
│ notes                   │
│ folder_path             │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│     sales_orders        │
│─────────────────────────│
│ id (PK)                 │
│ so_number (UNIQUE)      │
│ sales_contract_id (FK)  │
│ customer_id (FK)        │
│ so_date                 │
│ so_year                 │
│ title                   │
│ description             │
│ total_amount            │
│ currency                │
│ status                  │
│ delivery_date           │
│ notes                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘


┌─────────────────────────┐
│  contract_documents     │
│─────────────────────────│
│ id (PK)                 │
│ contract_type           │
│ contract_id             │
│ document_type           │
│ file_name               │
│ file_path               │
│ file_size               │
│ uploaded_by             │
│ upload_date             │
│ notes                   │
└─────────────────────────┘


┌─────────────────────────┐
│    contract_items       │
│─────────────────────────│
│ id (PK)                 │
│ contract_type           │
│ contract_id             │
│ item_number             │
│ item_name               │
│ description             │
│ quantity                │
│ unit                    │
│ unit_price              │
│ total_price             │
│ cost_price              │
│ notes                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘


┌─────────────────────────┐
│  numbering_sequences    │
│─────────────────────────│
│ id (PK)                 │
│ sequence_type           │
│ year                    │
│ archive_number          │
│ last_number             │
│ prefix                  │
│ created_at              │
│ updated_at              │
│ UNIQUE(type,year,arch)  │
└─────────────────────────┘
```

## Table Details

### 1. suppliers
**Purpose**: Store supplier information

**Fields**:
- `id`: Auto-increment primary key
- `code`: Unique supplier code (e.g., SUP001)
- `name`: Company name
- `contact_person`: Contact person name
- `phone`: Phone number
- `email`: Email address
- `address`: Full address
- `tax_code`: Tax identification number
- `bank_account`: Bank account number
- `bank_name`: Bank name
- `notes`: Additional notes
- `created_at`, `updated_at`: Timestamps

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (code)
- INDEX (name)

### 2. customers
**Purpose**: Store customer information

**Structure**: Same as suppliers but for customers
**Code prefix**: CUS001, CUS002, etc.

### 3. purchase_contracts
**Purpose**: Manage purchase contracts with suppliers

**Special Fields**:
- `contract_number`: Auto-generated unique number (PC-YYYY-XXXX-NNNN)
- `contract_year`: Year extracted from contract_date
- `archive_number`: Optional archive classification (A1, B2, etc.)
- `total_amount`: Contract value
- `currency`: VND, USD, EUR
- `status`: draft, active, completed, cancelled
- `folder_path`: Auto-generated document folder path

**Foreign Keys**:
- `supplier_id` → suppliers(id) ON DELETE RESTRICT

**Business Rules**:
- Contract number must be unique
- Cannot delete supplier if contracts exist
- Status workflow: draft → active → completed

### 4. sales_contracts
**Purpose**: Manage sales contracts with customers

**Special Fields** (in addition to purchase_contracts):
- `cost_amount`: Cost/COGS for profit calculation
- Profit = total_amount - cost_amount

**Foreign Keys**:
- `customer_id` → customers(id) ON DELETE RESTRICT

### 5. purchase_orders
**Purpose**: Purchase orders (PO) from suppliers

**Fields**:
- `po_number`: Auto-generated (PO-YYYY-NNNN)
- `purchase_contract_id`: Optional link to contract
- `supplier_id`: Required supplier
- `po_year`: Year for numbering
- `status`: draft, submitted, approved, completed, cancelled
- `delivery_date`: Expected delivery date

**Foreign Keys**:
- `purchase_contract_id` → purchase_contracts(id) ON DELETE SET NULL
- `supplier_id` → suppliers(id) ON DELETE RESTRICT

### 6. sales_orders
**Purpose**: Sales orders (SO) to customers

**Structure**: Similar to purchase_orders
- `so_number`: Auto-generated (SO-YYYY-NNNN)
- Links to sales_contracts and customers

### 7. contract_documents
**Purpose**: Store contract document metadata

**Fields**:
- `contract_type`: 'purchase' or 'sale'
- `contract_id`: Reference to contract
- `document_type`: quotation, contract, handover, payment_request, other
- `file_name`: Original filename
- `file_path`: Full path to file
- `file_size`: File size in bytes
- `uploaded_by`: User who uploaded
- `upload_date`: Upload timestamp

**Note**: Polymorphic relationship - links to both purchase and sales contracts

### 8. contract_items
**Purpose**: Detailed line items in contracts

**Fields**:
- `contract_type`: 'purchase' or 'sale'
- `contract_id`: Reference to contract
- `item_number`: Sequential number
- `item_name`: Product/service name
- `quantity`: Quantity ordered
- `unit`: Unit of measurement
- `unit_price`: Price per unit
- `total_price`: quantity × unit_price
- `cost_price`: Cost price (for sales contracts)

**Calculations**:
- `total_price` = `quantity` × `unit_price`
- For sales: profit per item = `total_price` - `cost_price`

### 9. numbering_sequences
**Purpose**: Manage sequential numbering for contracts and orders

**Fields**:
- `sequence_type`: purchase_contract, sales_contract, purchase_order, sales_order
- `year`: Year for the sequence
- `archive_number`: Optional archive classifier
- `last_number`: Last used number in sequence
- `prefix`: Prefix for the number (PC, SC, PO, SO)

**Unique Constraint**: (sequence_type, year, archive_number)

**Example**:
```
sequence_type: purchase_contract
year: 2024
archive_number: A1
last_number: 5
prefix: PC

Next number generated: PC-2024-A1-0006
```

## Data Types

### Monetary Values
- All amounts use DECIMAL(15,2)
- Supports up to 999,999,999,999.99

### Dates
- DATE format for dates only
- TIMESTAMP for datetime with auto-update

### Text Fields
- VARCHAR for limited text
- TEXT for unlimited content
- All use utf8mb4 charset

### Status Fields
- ENUM type for predefined values
- Ensures data integrity

## Indexes

### Primary Keys
All tables have auto-increment INT primary keys

### Unique Indexes
- supplier.code
- customer.code
- purchase_contracts.contract_number
- sales_contracts.contract_number
- purchase_orders.po_number
- sales_orders.so_number
- numbering_sequences(type, year, archive)

### Foreign Key Indexes
Automatically created on FK columns for performance

### Additional Indexes
- Supplier/customer names for searching
- Contract dates and years for filtering
- Status fields for filtering

## Storage Engine
- InnoDB (default)
- Supports transactions
- Foreign key constraints
- Row-level locking

## Character Set
- Database: utf8mb4
- Collation: utf8mb4_unicode_ci
- Supports Vietnamese and all Unicode characters

## Sample Data

The schema includes sample data:
- 3 suppliers (SUP001, SUP002, SUP003)
- 3 customers (CUS001, CUS002, CUS003)

## Maintenance

### Backup
```sql
mysqldump -u root -p contract_management > backup.sql
```

### Restore
```sql
mysql -u root -p contract_management < backup.sql
```

### Cleanup old data (example)
```sql
DELETE FROM numbering_sequences WHERE year < 2020;
DELETE FROM contract_documents WHERE upload_date < DATE_SUB(NOW(), INTERVAL 5 YEAR);
```

## Performance Considerations

### Indexes
- All foreign keys are indexed
- Search fields (codes, names) are indexed
- Year fields for partitioning potential

### Query Optimization
- Use prepared statements (already implemented)
- Avoid SELECT * (use specific columns)
- Use LIMIT for pagination
- Use JOINs efficiently

### Scaling
- Consider partitioning by year for large datasets
- Archive old contracts to separate tables
- Implement caching for reports

## Future Enhancements

### Potential Schema Changes
1. Add user authentication tables
2. Add audit trail table
3. Add email notification queue
4. Add contract templates
5. Add approval workflow tables
6. Add multi-currency exchange rates
7. Add payment tracking tables
