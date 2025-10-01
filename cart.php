<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}
include 'db.php';

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;
if ($cart) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE id IN ($ids)");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $item) {
        $total += $item['price'] * $cart[$item['id']];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); margin: 0; padding: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); animation: fadeIn 1s; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        button { background: #e21b70; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #c2185b; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
    <h2>Your Cart</h2>
    <table>
        <tr><th>Item</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $cart[$item['id']]; ?></td>
                <td>$<?php echo $item['price']; ?></td>
                <td>$<?php echo $item['price'] * $cart[$item['id']]; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr><td colspan="3">Total</td><td>$<?php echo $total; ?></td></tr>
    </table>
    <button onclick="location.href='checkout.php'">Checkout</button>
    <p style="text-align:center;"><a href="restaurants.php">Continue Shopping</a></p>
</body>
</html>
