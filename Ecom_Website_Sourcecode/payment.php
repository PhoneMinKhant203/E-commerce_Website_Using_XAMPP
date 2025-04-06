<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Options - AlfredBryson Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            color: #555;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4CAF50;
        }

        p {
            line-height: 1.6;
        }

        .payment-methods {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 20px;
        }

        .payment-methods img {
            width: 80px;
            height: auto;
        }

        .back-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="payment-container">
        <h1>Payment Options</h1>
        
        <p>At AlfredBryson Store, we offer secure and convenient payment options to ensure a smooth shopping experience. Currently, we accept the following payment methods:</p>

        <h2>Accepted Payment Gateways</h2>
        <div class="payment-methods">
            <div>
                <img src="Images/master.png" alt="Mastercard Logo" title="Mastercard">
                <p>Mastercard</p>
            </div>
            <div>
                <img src="Images/visa.png" alt="Visa Logo" title="Visa">
                <p>Visa</p>
            </div>
        </div>

        <p>All payments are processed securely to protect your information, and we ensure that your data is encrypted and handled with the utmost confidentiality. When you enter your card details during checkout, they are protected by our secure payment processing system.</p>

        <p>If you have any questions or encounter issues during payment, please contact our support team at <a href="mailto:support@alfredbryson.com">support@alfredbryson.com</a>.</p>

        <button type="button" onclick="window.history.back()" class="back-btn">Back to Store</button>
    </div>

</body>

</html>
