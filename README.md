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

---

# Інформаційний вебзастосунок про пошук гомологів і BLAST

Простий вебзастосунок на PHP та MySQL для вивчення термінології BLAST і пошуку гомологів.

Застосунок дозволяє:

- Переглядати базові терміни BLAST
- Динамічно отримувати визначення за допомогою HTMX
- Додавати власні біологічні терміни та визначення
- Зберігати користувацькі терміни у базі даних MySQL
- Працювати без перезавантаження сторінки завдяки AJAX

---

## Основні можливості

- Динамічний пошук визначень через HTMX
- Додавання користувацьких термінів
- Інтеграція з MySQL
- Захист від дублювання термінів
- Інтерактивний інтерфейс
- Чутливий до регістру пошук через `BINARY`

---

## Використані технології

- PHP
- MySQL
- JavaScript (Fetch API)
- HTMX
- HTML5 / CSS3

---

## Налаштування бази даних

### Створення бази даних

```sql
CREATE DATABASE blast_db;
```

### Створення таблиці

```sql
CREATE TABLE test_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term VARCHAR(255) NOT NULL UNIQUE,
    definition TEXT NOT NULL
);
```

---

## Конфігурація бази даних

Створіть файл `baza_connect.php` та додайте:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blast_db";
?>
```

---

## Принцип роботи

### Пошук термінів

Застосунок містить базові терміни BLAST:

- Hit
- E-value
- Identity

При натисканні на термін виконується HTMX-запит:

```html
hx-get="index.php?term=Hit"
```

Сервер виконує пошук визначення у базі даних і динамічно вставляє результат на сторінку без її перезавантаження.

Отримання визначення реалізоване через підготовлені SQL-запити:

```php
$stmt = $conn->prepare("SELECT definition FROM test_table WHERE BINARY term = ?");
```

---

### Додавання нових термінів

Користувач може додати:

- Термін
- Його визначення

Перед додаванням дані перевіряються та проходять перевірку на дублікати:

```php
$check = $conn->prepare("SELECT id FROM test_table WHERE term = ?");
```

Якщо термін відсутній у базі даних, він додається:

```php
$stmt = $conn->prepare("INSERT INTO test_table (term, definition) VALUES (?, ?)");
```

Frontend використовує JavaScript Fetch API для асинхронних запитів:

```javascript
fetch("index.php", {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" }
})
```

Після успішного додавання новий термін автоматично з’являється в інтерфейсі без перезавантаження сторінки.

---

## Безпека

У застосунку реалізовано:

- Prepared statements для SQL-запитів
- Захист від SQL-інʼєкцій
- Санітизацію виводу через `htmlspecialchars()`
- Захист від дублювання термінів
