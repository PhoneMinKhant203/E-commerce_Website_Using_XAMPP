<?php
function getProducts($pdo){
    $sql = "SELECT 
                `product_id` as ID, 
                `product_name` as Name, 
                `product_category` as Brand, 
                `product_price` as Price, 
                `product_stock` as Stock, 
                `product_image` as Image,
                `description` as description
            FROM `products`;";

    $stmt = $pdo->query($sql);
    

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $products;
}


function getSpecificProduct($pdo, $id){
    $sql ="SELECT * FROM products WHERE product_id= :id";
    $stmt = $pdo->prepare($sql);
    $stmt-> execute([':id'=>$id]);
    $product=$stmt->fetch(PDO::FETCH_ASSOC);
    return $product;
}
?>
