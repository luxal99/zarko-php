<?php
include './config/config.php';

use Firebase\JWT\JWT;

require_once('./vendor/autoload.php');

$key = "DPKNnipvwpiwVEPOJIVEWPJWVDjiopswvdJIPWVEDNPKwvdJPOIWVDnkp";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user join role r on r.id = user.id_role where username = '" . $username . "' and password = '" . $password . "'";
    $result = $conn->query($sql);
    $iat= time();
    $expirationTime = $iat + 60*60*4;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $payload = array(
                "username" => $row["username"],
                "role" => $row['name'],
                "iat" => $iat,
                "exp" => $expirationTime
            );
            $jwt = JWT::encode($payload, $key, 'HS512');
            setcookie('token', $jwt, time() + (86400 * 30), "/"); // 86400 = 1 day

            header("Location: index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 350px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</body>

</html>