<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form id="loginForm">
        <input type="email" name="email" placeholder="Email" required /><br>
        <input type="password" name="password" placeholder="Password" required /><br>
        <button type="submit">Login</button>
    </form>

    <div id="message"></div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", async function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const body = JSON.stringify({
                email: formData.get("email"),
                password: formData.get("password")
            });

            const response = await fetch("http://localhost:8000/api/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body
            });

            const result = await response.json();
            document.getElementById("message").textContent = result.message;
        });
    </script>
</body>
</html>


<!-- backend index.php-->
<?php
require_once __DIR__ . '/../routes/web.php';
