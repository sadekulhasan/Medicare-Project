<?php

require 'database_connection.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    
    $query = "SELECT * FROM medical_products WHERE product_id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['product_id' => $product_id]);

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        
        $cart_query = "SELECT * FROM cart WHERE product_id = :product_id";
        $cart_stmt = $pdo->prepare($cart_query);
        $cart_stmt->execute(['product_id' => $product_id]);

        if ($cart_stmt->rowCount() > 0) {
            
            $update_query = "UPDATE cart SET product_quantity = product_quantity + 1 WHERE product_id = :product_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute(['product_id' => $product_id]);
        } else {
            
            $insert_query = "INSERT INTO cart (product_id, product_name, product_brand, product_category, product_price, product_quantity)
                             VALUES (:product_id, :product_name, :product_brand, :product_category, :product_price, 1)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute([
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'product_brand' => $product['product_brand'],
                'product_category' => $product['product_category'],
                'product_price' => $product['product_price']
            ]);
        }
        echo "success";
    } else {
        echo "failure";
    }
} else {
    echo "failure";
}
?>
