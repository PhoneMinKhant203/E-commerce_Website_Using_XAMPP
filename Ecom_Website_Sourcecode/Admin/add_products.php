<?php include('includes/header.php'); ?>

<div class="main-container d-flex">
  <aside class="sidebar">
    <?php include('includes/sidebar.php'); ?>
  </aside>

  <div class="content-container container">
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Add Products</h4>
            <a href="products.php" class="btn btn-danger float-end">Back</a>
          </div>
          <div class="card-body">
            <form action="add_code.php" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6">
                  <label for="">Name</label>
                  <input type="text" name="product_name" placeholder="Enter Product Name" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="">Category</label>
                  <input type="text" placeholder="Enter Product Category" name="product_category" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="">Price $</label>
                  <input type="text" placeholder="Enter Product Price" name="product_price" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="">Stock</label>
                  <input type="text" name="product_stock" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="">Upload Image</label>
                  <input type="file" name="image" class="form-control">
                </div> 
                
                <div class="col-md-12">
                  <label for="">Description</label>
                  <textarea name="product_description" rows="4" placeholder="Enter Product Description" class="form-control"></textarea>
                </div>

                <div class="col-md-12">
                <br>  <button type="submit" class="btn btn-dark" name="add_products_btn">Save</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
