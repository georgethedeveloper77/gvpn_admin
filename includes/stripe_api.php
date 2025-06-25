<?php
include("connection.php");

$sql= "SELECT * FROM willdev_payment_method WHERE name='Stripe'";
$row_method = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));

require_once '../vendor/stripe-php/init.php';
$stripe = new \Stripe\StripeClient($row_method['private_key']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = $_POST['email'];
    $name = $_POST['name'];
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $currency = $row_method['currency'];
    
    $sql= "SELECT stripe_cus FROM willdev_users WHERE email='$email'";
    $row = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
    
    if ($row['stripe_cus'] == "" && $row['stripe_cus'] == NULL) {
        //$customer = $stripe->customers->create(['email' => $email, 'name' => $name]);
        
        $customer = $stripe->customers->create([
          'name' => $name,
          'email' => $email,
        ]);
                
        $customer_id = $customer->id;
        $sql= "UPDATE willdev_users SET stripe_cus='$customer_id' WHERE email='$email'";
        mysqli_query($mysqli, $sql);
    } else {
        $customer_id = $row['stripe_cus'];
    }
    
    
    $subscription = $stripe->subscriptions->create([
        'customer' => $customer_id,
        'items' => [[
            'price' => $product_id,
        ]],
        'description' => $product_name,
        'payment_behavior' => 'default_incomplete',
        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
        'expand' => ['latest_invoice.payment_intent'],
    ]);
    
    $subscription_id = $subscription->id;
    $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;
    
    $ephemeralKey = $stripe->ephemeralKeys->create([
      'customer' => $customer_id,
    ], [
      'stripe_version' => '2022-08-01',
    ]);
    
    echo json_encode(
      [
        'subscriptionId' => $subscription_id,  
        'paymentIntent' => $clientSecret,
        'ephemeralKey' => $ephemeralKey->secret,
        'customer' => $customer_id,
        'publishableKey' => $row_method['public_key']
      ]
    );
    http_response_code(200);
}
