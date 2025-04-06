<?php
include('includes/header.php');
include('../Admin/config/dbconnect.php');
include('../Admin/data.php');
$products = getProducts($pdo);

// Success message for edit
if (isset($_GET['edit']) && $_GET['edit'] == 'success') {
    echo "<div class='alert alert-success'>Product updated successfully!</div>";
}

?>

<div class="main-container d-flex">
    <aside class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </aside>

    <div class="content-container container">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Products List</h4>
                        <a href="add_products.php" class="btn btn-dark float-end">Add Product</a>
                    </div>
                    <?php if (!empty($products)): ?>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; ">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($products[0]) as $title): ?>
                                                <th scope="col"><?= $title ?></th>
                                            <?php endforeach ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?= $product['ID'] ?></td>
                                                <td class="Pname"><?= $product['Name'] ?></td>
                                                <td class="PBrand" style="overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; white-space: normal;"><?= $product['Brand'] ?></td>
                                                <td class="PPrice">$<?= $product['Price'] ?></td>
                                                <td class="PStock"><?= $product['Stock'] ?></td>
                                                <td><img src="<?= $product['Image'] ?>" alt="Product Image" style="width: 80px; height: 100px;"></td>
                                                <td class="PDescription" style="max-width: 300px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; white-space: normal;">
                                                    <?= htmlspecialchars($product['description']) ?>
                                                </td>

                                                <td>
                                                    <a href="edit_products.php?id=<?= $product['ID'] ?>" class="btn btn-dark">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="<?= "delete_product.php?id=" . $product['ID'] ?>" class="btn btn-danger" onclick="return confirmDelete('<?= $product['Name'] ?>');">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include('includes/footer.php'); ?>

<!-- JavaScript function to confirm deletion -->
<script>
    function confirmDelete(productName) {
        return confirm("Are you sure you want to delete the product: " + productName + "?");
    }

    // Show a success alert box if the product was successfully deleted
    <?php if (isset($_GET['delete']) && $_GET['delete'] == 'success'): ?>
        alert('Product deleted successfully!');
    <?php endif; ?>

    // Show an error alert box if there was an issue deleting the product
    <?php if (isset($_GET['delete']) && $_GET['delete'] == 'error'): ?>
        alert('Error deleting the product!');
    <?php endif; ?>
</script>