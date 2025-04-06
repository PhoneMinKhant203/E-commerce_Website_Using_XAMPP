<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .shop-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .shop-container p,
        .shop-container li {
            line-height: 1.6;
            color: #555;
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

    <div class="shop-container">
        <h1>How to Shop</h1>
        <p>Shopping at Alfred D. Bryson Luxury Watch Store is designed to be a seamless and enjoyable experience. Follow these simple steps to find and purchase your ideal luxury watch:</p>

        <h2>1. Browse Our Collection</h2>
        <p>Explore our curated collection of luxury watches, featuring brands like Rolex, Omega, Patek Philippe, and Audemars Piguet. Use our search filters to refine your selection by brand, material, price, and style to quickly find what you’re looking for.</p>

        <h2>2. View Product Details</h2>
        <p>Click on any watch to view its specifications, including materials, movement, and special features. Zoom in on high-resolution images to appreciate each intricate detail of our luxury timepieces.</p>

        <h2>3. Add to Cart</h2>
        <p>Once you’ve selected a watch, choose any customization options available. Click “Add to Cart” to proceed to checkout, or continue browsing if you'd like to view more options.</p>

        <h2>4. Checkout</h2>
        <p>Review your items in the cart. Ensure everything is correct, then select from our secure payment options, which include Visa, Mastercard, Bank Transfer, and PayPal. Complete your transaction confidently with our encrypted payment gateways.</p>

        <h2>5. Choose Shipping Options</h2>
        <p>During checkout, select your preferred shipping method. We offer complimentary standard shipping, as well as expedited options for quicker delivery times.</p>

        <h2>6. Receive Order Confirmation</h2>
        <p>Once your order is confirmed, you will receive an email with details of your purchase and an estimated delivery date. Track your order via our website or contact our support team for any assistance.</p>

        <h2>7. Customer Support</h2>
        <p>If you have questions about our products or your purchase, please reach out to our support team via the <a href="contact.html">Contact Us</a> page. We’re here to help you with every step of your shopping experience.</p>

        <button class="back-btn" onclick="goBack()">Back to Store</button>
    </div>

    <script>
        function goBack() {
            const previousPage = localStorage.getItem('previousPage');
            if (previousPage) {
                window.location.href = previousPage;
            } else {
                window.history.back();
            }
        }

        // Store the referring page URL in localStorage
        if (document.referrer && document.referrer !== window.location.href) {
            localStorage.setItem('previousPage', document.referrer);
        }
    </script>

</body>

</html>
