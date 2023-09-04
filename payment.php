<?php
session_start();

if (isset($_POST['order_pay_btn'])) {
    $order_status = $_POST['order_status'];
    $order_total_price = $_POST['order_total_price'];
}
?>

<?php include('layouts/header.php'); ?>
<!-- Payment -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Payment</h2>
        <hr class="orange-line">
    </div>
    <div class="mx-auto container text-center">
    <?php  if (isset($_POST['order_status']) && $_POST['order_status'] == "not paid") { ?>
            <?php $amount = strval($_POST['order_total_price']); ?>
          <?php $order_id= $_POST['order_id'];?>
            <p>Total payment: $ <?php echo $_POST['order_total_price']; ?></p>
            <!-- PayPal Button Container -->
            <div class="text-centere">
    <div id="paypal-button-container"></div>
</div>
        <?php } else if (isset($_SESSION['total']) && $_SESSION['total'] != 0) { ?>
            <?php $amount = strval($_SESSION['total']); ?>
          <?php $order_id= $_SESSION['order_id'];?>
            <p>Total payment: $ <?php echo $_SESSION['total']; ?></p>
            <!-- PayPal Button Container -->
            <div class="text-centere">
    <div id="paypal-button-container"></div>
            </div>

        <?php } else { ?>
            <p>You don't have an order</p>
        <?php } ?>
    </div>
</section>

<!-- Include PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=ASHIl-VSwLZamidEHx2j7DLHHCKiB7BO2sJD-FHrku6BIVpKdUqycarg5L6jb3WYUci-EfkaKZcSg_Xk&currency=USD"></script>

<script>
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $amount;?>'
                        //currency_code: 'USD'
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (orderData) {
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2)); 
var transaction = orderData.purchase_units[0].payments.captures[0];
alert('Transaction' + transaction.status +': '+transaction.id + '\n\nSee console for all available details');
                // Handle successful payment, e.g., redirect or display a thank-you message
             window.location.href = 'thankyou.php';
 //window.location.href = "server/complete_payment.php?transaction_id=" + transaction.id + "&order_id=<?php echo $order_id; ?>";

            });
        }
    }).render('#paypal-button-container');
</script>

<?php include('layouts/footer.php'); ?>
