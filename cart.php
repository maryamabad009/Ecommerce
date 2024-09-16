<?php
session_start();

// Add product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Check if the cart session exists
    if (isset($_SESSION['cart'])) {
        // Check if the product is already in the cart
        if (isset($_SESSION['cart'][$product_id])) {
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
}

// Remove product from cart
if (isset($_POST['remove_product'])) {
    $product_key = $_POST['product_key'];

    // Check if the product_key exists in the cart
    if (isset($_SESSION['cart'][$product_key])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$product_key]);
    }
    calculateTotalCart();
}

// Edit product quantity in the cart
if (isset($_POST['edit_quantity'])) {
    $product_key = $_POST['product_key'];
    $product_quantity = $_POST['product_quantity'];

    // Check if the product_key exists in the cart
    if (isset($_SESSION['cart'][$product_key])) {
        // Update the product quantity
        $_SESSION['cart'][$product_key]['product_quantity'] = $product_quantity;
    }

    calculateTotalCart();
}

// Function to calculate the total cart amount
function calculateTotalCart() {
    $total = 0;
    $total_quantity = 0;

    // Check if cart is set before trying to calculate totals
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $value) {
            $price = $value['product_price'];
            $quantity = $value['product_quantity'];
            $total += $price * $quantity;
            $total_quantity += $quantity;
        }
    }

    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $total_quantity;
}
?>

<?php include('layouts/header.php'); ?>

<!--Cart Section-->
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bolde">Your Cart</h2>
        <hr>
    </div>

    <!-- Cart Table -->
    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <!-- Display Cart Items -->
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
            <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
            <tr>
                <td>
                    <div class="product-info">
                        <img src="assets/imgs/<?php echo $value['product_image']; ?>" />
                        <div>
                            <p><?php echo $value['product_name']; ?></p>
                            <small><span>$</span><?php echo $value['product_price']; ?></small>
                            <br>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_key" value="<?php echo $value['product_id']; ?>" />
                                <input type="submit" name="remove_product" class="remove-btn" value="Remove" />
                            </form>
                        </div>
                    </div>
                </td>

                <td>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_key" value="<?php echo $value['product_id']; ?>" />
                        <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>" />
                        <input type="submit" name="edit_quantity" class="edit-btn" value="Edit" />
                    </form>
                </td>

                <td>
                    <span>$</span>
                    <span class="product-price"><?php echo $value['product_quantity'] * $value['product_price']; ?></span>
                </td>
            </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="3">
                    <p>Your cart is empty</p>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Cart Total -->
    <div class="cart-total">
        <table>
            <tr>
                <td>Total</td>
                <td>$<?php echo isset($_SESSION['total']) ? $_SESSION['total'] : '0'; ?></td>
            </tr>
        </table>
    </div>

    <!-- Checkout Button -->
    <div class="checkout-container">
        <form method="POST" action="checkout.php">
            <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
        </form>
    </div>

</section>

<?php include('layouts/footer.php'); ?>
