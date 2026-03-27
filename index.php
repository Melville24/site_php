<?php
include_once('baza_connect.php'); 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $term = trim($_POST['term']);
    $definition = trim($_POST['definition']);

    if ($term !== "" && $definition !== "") {
        $check = $conn->prepare("SELECT id FROM test_table WHERE term = ?");
        $check->bind_param("s", $term);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO test_table (term, definition) VALUES (?, ?)");
            $stmt->bind_param("ss", $term, $definition);
            $stmt->execute();
            $stmt->close();
        }

        $check->close();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(["status" => "OK", "term" => $term]);
            $conn->close();
            exit();
        }

        header("Location: index.php");
        exit();
    }
}

if (isset($_GET['term'])) {
    $term = trim($_GET['term']);

    $stmt = $conn->prepare("SELECT definition FROM test_table WHERE BINARY term = ?");
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<div style='padding:10px;border:2px solid green;margin-top:10px;'>" . htmlspecialchars($row['definition']) . "</div>";
    } else {
        echo "<div style='color:red;padding:10px;'>Визначення не знайдено: " . htmlspecialchars($term) . "</div>";
    }

    $stmt->close();
    $conn->close();
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BLAST і пошук гомологів</title>
<link rel="stylesheet" href="css/styles.css">
<link rel="icon" type="image/x-icon" href="assets/favicon.ico">
<script src="js/htmx.min.js"></script>
</head>
<body>

<header class="masthead text-center text-white">
<div class="masthead-content">
    <div class="container px-5">
        <h1 class="masthead-heading mb-0">BLAST і пошук гомологів</h1>
        <a class="btn btn-primary btn-xl rounded-pill mt-5" href="#terms-section">Далі</a>
    </div>
</div>
<div class="bg-circle-1 bg-circle" style="background: radial-gradient(circle, #4a90e2, #ff6eb4);"></div>
<div class="bg-circle-2 bg-circle" style="background: radial-gradient(circle, #5aa1f2, #ff84c1);"></div>
<div class="bg-circle-3 bg-circle" style="background: radial-gradient(circle, #4dc0ff, #ff7ac7);"></div>
<div class="bg-circle-4 bg-circle" style="background: radial-gradient(circle, #70c8ff, #ff99d9);"></div>
</header>

<section id="content1">
<div class="container px-5">
    <div class="row gx-5 align-items-center">
        <div class="col-lg-6 order-lg-2">
            <div class="p-5"><img class="img-fluid rounded-circle thumb-img" src="img/blast_scheme.png" alt="BLAST scheme"></div>
        </div>
        <div class="col-lg-6 order-lg-1">
            <div class="p-5">
                <h2 class="display-4">Схема роботи BLAST</h2>
                <p>BLAST порівнює послідовність-запит із базою даних, знаходить локальні вирівнювання та оцінює їх статистичну значущість.</p>
            </div>
        </div>
    </div>
</div>
</section>

<section id="content2">
<div class="container px-5">
    <div class="row gx-5 align-items-center">
        <div class="col-lg-6">
            <div class="p-5"><img class="img-fluid rounded-circle thumb-img" src="img/blast_metrics.png" alt="BLAST metrics"></div>
        </div>
        <div class="col-lg-6">
            <div class="p-5">
                <h2 class="display-4">Приклад результату пошуку</h2>
                <p>На зображенні показані метрики результату BLAST: значення E, відсоток ідентичності та інформація про знайдений збіг.</p>
            </div>
        </div>
    </div>
</div>
</section>

<section id="terms-section">
<div class="container px-5">
<h2>Основні поняття</h2>
<div class="terms">
    <button class="term-btn btn btn-primary btn-sm rounded-pill" hx-get="index.php?term=Hit" hx-target="#definition-box" hx-swap="innerHTML">Hit</button>
    <button class="term-btn btn btn-primary btn-sm rounded-pill" hx-get="index.php?term=E-value" hx-target="#definition-box" hx-swap="innerHTML">E-value</button>
    <button class="term-btn btn btn-primary btn-sm rounded-pill" hx-get="index.php?term=Identity" hx-target="#definition-box" hx-swap="innerHTML">Identity</button>
</div>

<div id="definition-box"></div>

<h3>Користувацькі терміни</h3>
<div id="user-terms" class="terms user-terms">
<?php
$conn = new mysqli($servername, $username, $password, $dbname);
$builtInTerms = ["Hit", "E-value", "Identity"];
$result = $conn->query("SELECT term FROM test_table ORDER BY id ASC");
while ($row = $result->fetch_assoc()) {
    $term = trim($row['term']);
    $skip = false;
    foreach ($builtInTerms as $b) {
        if (mb_strtolower($term, 'UTF-8') === mb_strtolower($b, 'UTF-8')) {
            $skip = true;
            break;
        }
    }
    if (!$skip) {
        echo '<button class="term-btn btn btn-primary btn-sm rounded-pill user-term visible pop" hx-get="index.php?term='.urlencode($term).'" hx-target="#definition-box" hx-swap="innerHTML">'.htmlspecialchars($term).'</button>';
    }
}
$conn->close();
?>
</div>

<div id="add-term">
<h2>Додати новий термін</h2>
<form id="add-term-form" method="POST">
    <label for="term">Термін:</label><br>
    <input type="text" id="term" name="term" required><br><br>
    <label for="definition">Визначення:</label><br>
    <textarea id="definition" name="definition" rows="4" required></textarea><br><br>
    <button type="submit" class="btn btn-primary rounded-pill">Додати</button>
</form>
</div>
</div>
</section>

<footer class="py-5 bg-black">
<div class="container px-5">
    <p class="m-0 text-center text-white small">Copyright &copy; Your Website 2026</p>
</div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("add-term-form");
    const container = document.getElementById("user-terms");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const term = formData.get("term").trim();
        const definition = formData.get("definition").trim();
        if (!term || !definition) return;

        fetch("index.php", {
            method: "POST",
            body: formData,
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.status === "OK") {
                if (!document.querySelector(`[hx-get="index.php?term=${encodeURIComponent(data.term)}"]`)) {
                    const btn = document.createElement("button");
                    btn.className = "term-btn btn btn-primary btn-sm rounded-pill user-term visible pop";
                    btn.textContent = data.term;
                    btn.setAttribute("hx-get", `index.php?term=${encodeURIComponent(data.term)}`);
                    btn.setAttribute("hx-target", "#definition-box");
                    btn.setAttribute("hx-swap", "innerHTML");
                    container.appendChild(btn);
                    htmx.process(btn);
                }
                form.reset();
            }
        })
        .catch(() => alert("Помилка додавання терміну."));
    });

    document.querySelectorAll(".thumb-img").forEach(img => {
        img.addEventListener("click", function () {
            this.classList.toggle("expanded");
        });
    });
});
</script>
</body>
</html>
