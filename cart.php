<?php
session_start();

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Check if the cart session exists
    if (isset($_SESSION['cart'])) {
        // Check if the product is already in the cart
        $item = $_SESSION['cart'][$product_id];

        if ($item) {
            // Product already exists in the cart, update quantity
            $_SESSION['cart'][$product_id]['product_quantity'] += $_POST['product_quantity'];
        } else {
            // Product does not exist in the cart, add it
            $product_array = array(
                'product_id' => $product_id,
                'product_name' => $_POST['product_name'],
                'product_price' => $_POST['product_price'],
                'product_image' => $_POST['product_image'],
                'product_quantity' => $_POST['product_quantity'],
            );
            $_SESSION['cart'][$product_id] = $product_array;
        }
    } else {
        // Cart session does not exist, create it
        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'product_image' => $_POST['product_image'],
            'product_quantity' => $_POST['product_quantity'],
        );
        $_SESSION['cart'] = array($product_id => $product_array);
    }
    calculateTotalCart();
} elseif (isset($_POST['remove_product'])) {
    $product_key = $_POST['product_key'];

    // Check if the product_key exists in the cart
    if (isset($_SESSION['cart'][$product_key])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$product_key]);
    }
    calculateTotalCart();
} elseif (isset($_POST['edit_quantity'])) {
    $product_key = $_POST['product_key'];
    $product_quantity = $_POST['product_quantity'];

    // Check if the product_key exists in the cart
    if (isset($_SESSION['cart'][$product_key])) {
        // Update the product quantity
        $_SESSION['cart'][$product_key]['product_quantity'] = $product_quantity;
    }
    
    calculateTotalCart();
} else {
   // header('location: index.php');
}

// Function to calculate the total cart amount
function calculateTotalCart() {
    $total = 0;
    $total_quantity=0;
    foreach ($_SESSION['cart'] as $key => $value) {
        $product = $_SESSION['cart'][$key];
        $price = $product['product_price'];
        $quantity = $product['product_quantity'];
        $total = $total + ($price * $quantity);
        $total_quantity=$total_quantity+$quantity;
    }
    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $total_quantity;
}
?>

<?php include('layouts/header.php'); ?>
<!--Cart-->
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bolde">Your Cart</h2>
        <hr>
    </div>
    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach($_SESSION['cart'] as $key => $value) { ?>
        <tr>
            <td>
                <div class="product-info">
                    <img src="assets/imgs/<?php echo $value['product_image'];?>"/>
                    <div>
                        <p><?php echo $value['product_name'];?></p>
                        <small><span>$</span><?php echo $value['product_price'];?></small>
                        <br>
                        <form method="POST" action="cart.php">
    <input type="hidden" name="product_key" value="<?php echo $value['product_id']; ?>"/>
    <input type="submit" name="remove_product" class="remove-btn" value="Remove"/>
</form>


     </div>
                </div>
            </td>
            <td>
                
            <form method="POST" action="cart.php">
    <input type="hidden" name="product_key" value="<?php echo $value['product_id']; ?>"/>
    <input type="number" name="product_quantity" value="<?php echo $value['product_quantity'];?>"/>
    <input type="submit" name="edit_quantity" class="edit-btn" value="Edit"/>
</form>


            </td>
            <td>
                <span>$</span>
            <span class="product-price"><?php echo $value['product_quantity'] * $value['product_price'];?></span>
        </td>
        </tr>
        <?php } ?>
    </table>


<div class="cart-total">
    <table>
<!--<tr>
    <td>Subtotal</td>
    <td>$155</td>
</tr>-->
<tr>
    <td>Total</td>
    <td>$<?php echo $_SESSION['total'];?></td>
</tr>
    </table>
</div>

<div class="checkout-container">
<form method="POST" action="checkout.php">
    <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
        </form>
</div>

</section>




<?php include('layouts/footer.php'); ?>