<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}
include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $payment = $_POST['payment'];
    $cart = $_SESSION['cart'];
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE id IN ($ids)");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = 0;
    $rest_id = $items[0]['restaurant_id']; // Assume all from same restaurant for simplicity
    foreach ($items as $item) {
        $total += $item['price'] * $cart[$item['id']];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, restaurant_id, total, payment_method, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $rest_id, $total, $payment, $address]);
    $order_id = $pdo->lastInsertId();

    foreach ($items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $cart[$item['id']], $item['price']]);
    }

    unset($_SESSION['cart']);
    echo "<script>alert('Order placed!'); location.href='track.php?order_id=$order_id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); margin: 0; padding: 20px; color: #333; }
        form { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); animation: slideUp 0.5s; }
        @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #e21b70; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; width: 100%; transition: background 0.3s; }
        button:hover { background: #c2185b; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Checkout</h2>
        <input type="text" name="address" placeholder="Delivery Address" value="<?php echo $user['address']; ?>" required>
        <select name="payment" required>
            <option value="cod">Cash on Delivery</option>
            <option value="online">Online Payment (Dummy)</option>
        </select>
        <button type="submit">Place Order</button>
    </form>
    <p style="text-align:center;"><a href="cart.php">Back to Cart</a></p>
</body>
</html>
