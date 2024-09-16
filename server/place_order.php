<?php
session_start();
include('connection.php');
if(!isset($_SESSION['logged_in'])){
    header('location: ../checkout.php?message=Please login/register to place an order');
    exit;
} else {
    if(isset($_POST['place_order'])){
        // Step 1: Capture user input
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $order_cost = $_SESSION['total'];  // This should be a string/decimal, not an integer
        $order_status = "not paid";
        $user_id = $_SESSION['user_id'];
        $order_date = date('Y-m-d H:i:s');
        
        // Step 2: Prepare the order insertion query
        $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date)
        VALUES (?,?,?,?,?,?,?)");
        
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error); // Debug the query preparation
        }

        // Step 3: Bind parameters
        $stmt->bind_param('ssissss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);
        
        // Step 4: Execute the query
        $stmt_status = $stmt->execute();
        if(!$stmt_status){
            die("Error executing statement: " . $stmt->error);  // Debug query execution
        }
        
        // Step 5: Retrieve the inserted order's ID
        $order_id = $stmt->insert_id;

        // Step 6: Insert each cart item into the `order_item` table
        foreach($_SESSION['cart'] as $key => $value){
            $product = $_SESSION['cart'][$key];
            $product_id = $product['product_id'];
            $product_name = $product['product_name'];
            $product_image = $product['product_image'];
            $product_price = $product['product_price'];
            $product_quantity = $product['product_quantity'];
            
            $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date)
            VALUES (?,?,?,?,?,?,?,?)");
            
            if (!$stmt1) {
                die("Error preparing order item statement: " . $conn->error); // Debug query preparation
            }
            
            $stmt1->bind_param('iissiiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
            $stmt1->execute();
        }

        // Step 7: Redirect to payment
        $_SESSION['order_id'] = $order_id;
        header('location: ../payment.php?order_status=Order placed successfully');
    }
}
?>
