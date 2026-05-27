# BLAST Homology Search Web App

A simple PHP + MySQL web application for learning and exploring BLAST terminology and homolog search concepts.

The application allows users to:

- View predefined BLAST-related terms
- Search for definitions dynamically using HTMX
- Add custom biological terms and definitions
- Store user-defined terms in a MySQL database
- Interact with the interface without page reloads using AJAX

---

## Features

- Dynamic term lookup with HTMX
- User-defined term submission
- MySQL database integration
- Duplicate term prevention
- Interactive interface
- Case-sensitive term search using `BINARY`

---

## Technologies Used

- PHP
- MySQL
- JavaScript (Fetch API)
- HTMX
- HTML5 / CSS3

---

## Database Setup

### Create Database

```sql
CREATE DATABASE blast_db;
```

### Create Table

```sql
CREATE TABLE test_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term VARCHAR(255) NOT NULL UNIQUE,
    definition TEXT NOT NULL
);
```

---

## Database Configuration

Create a file named `baza_connect.php` and add:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blast_db";
?>
```

---

## How It Works

### Term Lookup

The application contains predefined BLAST-related terms such as:

- Hit
- E-value
- Identity

Clicking a term sends an HTMX request:

```html
hx-get="index.php?term=Hit"
```

The server searches the database for the selected term and dynamically inserts the definition into the page without reloading.

Definitions are retrieved using prepared SQL statements:

```php
$stmt = $conn->prepare("SELECT definition FROM test_table WHERE BINARY term = ?");
```

---

### Adding New Terms

Users can submit:

- A term
- Its definition

Submitted data is validated and checked for duplicates before insertion:

```php
$check = $conn->prepare("SELECT id FROM test_table WHERE term = ?");
```

If the term does not already exist, it is inserted into the database:

```php
$stmt = $conn->prepare("INSERT INTO test_table (term, definition) VALUES (?, ?)");
```

The frontend uses JavaScript Fetch API to send asynchronous requests:

```javascript
fetch("index.php", {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" }
})
```

After successful insertion, a new button for the term is dynamically added to the interface without page reload.

---

## Security

The application includes:

- Prepared statements for SQL queries
- SQL injection prevention
- Output sanitization using `htmlspecialchars()`
- Duplicate term protection
