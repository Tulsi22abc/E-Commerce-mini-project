<?php
session_start();

/* DATABASE CONNECTION */
$conn = new mysqli("localhost","root","","ecommerce");
if($conn->connect_error){
    die("Connection Failed");
}

/* REGISTER */
if(isset($_POST['register'])){
    $u = $_POST['username'];
    $p = $_POST['password'];

    $check = $conn->query("SELECT * FROM users WHERE username='$u'");
    if($check->num_rows > 0){
        $error = "User already exists!";
    } else {
        $conn->query("INSERT INTO users(username,password) VALUES('$u','$p')");
        $success = "Registration Successful!";
    }
}

/* LOGIN */
if(isset($_POST['login'])){
    $u = $_POST['username'];
    $p = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username='$u' AND password='$p'");
    if($res->num_rows>0){
        $_SESSION['user']=$u;
    } else {
        $error = "Invalid Login!";
    }
}

/* LOGOUT */
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
}

/* ADD PRODUCT */
if(isset($_POST['add'])){
    $name=$_POST['name'];
    $price=$_POST['price'];
    $desc=$_POST['desc'];

    $conn->query("INSERT INTO products(name,price,description) VALUES('$name','$price','$desc')");
}

/* DELETE */
if(isset($_GET['delete'])){
    $id=$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
}

/* UPDATE */
if(isset($_POST['update'])){
    $id=$_POST['id'];
    $name=$_POST['name'];
    $price=$_POST['price'];

    $conn->query("UPDATE products SET name='$name',price='$price' WHERE id=$id");
}

/* CART */
if(isset($_GET['cart'])){
    $_SESSION['cart'][]=$_GET['cart'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mini E-Commerce</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(to right, #667eea, #764ba2);
    font-family: 'Segoe UI';
}

.title {
    text-align: center;
    color: white;
    margin: 20px;
}

.login-box {
    width: 350px;
    margin: auto;
    margin-top: 50px;
}

.product-card {
    border-radius: 15px;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.product-card:hover {
    transform: scale(1.05);
}

.cart-box {
    background: white;
    padding: 15px;
    border-radius: 10px;
}
</style>
</head>

<body>

<h1 class="title">🛒 Mini E-Commerce</h1>

<?php if(!isset($_SESSION['user'])){ ?>

<!-- LOGIN + REGISTER -->
<div class="login-box">
    <div class="card p-4">
        <h4 class="text-center">User Login / Register</h4>

        <?php if(isset($error)){ ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <?php if(isset($success)){ ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php } ?>

        <!-- LOGIN -->
        <form method="POST">
            <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
            <button class="btn btn-primary w-100 mb-2" name="login">Login</button>
        </form>

        <hr>

        <!-- REGISTER -->
        <form method="POST">
            <input type="text" name="username" class="form-control mb-2" placeholder="New Username" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="New Password" required>
            <button class="btn btn-success w-100" name="register">Register</button>
        </form>

    </div>
</div>

<?php } else { ?>

<div class="container text-end">
    <a href="?logout=1" class="btn btn-danger mt-2">Logout</a>
</div>

<!-- ADD PRODUCT -->
<div class="container mt-4">
<div class="card p-4">
<h4>Add Product</h4>
<form method="POST">
<input type="text" name="name" class="form-control mb-2" placeholder="Product Name" required>
<input type="number" name="price" class="form-control mb-2" placeholder="Price" required>
<input type="text" name="desc" class="form-control mb-2" placeholder="Description">
<button class="btn btn-success" name="add">Add Product</button>
</form>
</div>
</div>

<!-- ADMIN PRODUCT LIST -->
<div class="container mt-4">
<h3 class="text-white">Product Management</h3>
<div class="row">

<?php
$result=$conn->query("SELECT * FROM products");
while($row=$result->fetch_assoc()){
?>
<div class="col-md-3">
<div class="card product-card p-3 mb-3">
<h5><?= $row['name'] ?></h5>
<p>₹ <?= $row['price'] ?></p>

<form method="POST">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="text" name="name" value="<?= $row['name'] ?>" class="form-control mb-1">
<input type="number" name="price" value="<?= $row['price'] ?>" class="form-control mb-1">
<button class="btn btn-warning btn-sm" name="update">Update</button>
</form>

<a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm mt-2">Delete</a>
</div>
</div>
<?php } ?>

</div>
</div>

<?php } ?>

<hr>

<!-- USER PRODUCTS -->
<div class="container mt-4">
<h3 class="text-white">Products</h3>
<div class="row">

<?php
$res=$conn->query("SELECT * FROM products");
while($row=$res->fetch_assoc()){
?>
<div class="col-md-3">
<div class="card product-card p-3 mb-3">
<h5><?= $row['name'] ?></h5>
<p>₹ <?= $row['price'] ?></p>
<p><?= $row['description'] ?></p>
<a href="?cart=<?= $row['id'] ?>" class="btn btn-success">Add to Cart</a>
</div>
</div>
<?php } ?>

</div>
</div>

<!-- CART -->
<div class="container mt-4">
<div class="cart-box">
<h4>🛍 Cart</h4>

<?php
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $c){
        echo "Product ID: ".$c."<br>";
    }
} else {
    echo "Cart is empty";
}
?>

</div>
</div>

</body>
</html>
