<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

$items_per_page = 6;

$total_products_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM products") or die('query failed');
$total_products = mysqli_fetch_assoc($total_products_query)['total'];

$total_pages = ceil($total_products / $items_per_page);

$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($current_page - 1) * $items_per_page;

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart_numbers) > 0){
      $message[] = 'Produk Sudah Ada Di Keranjang!';
   }else{
      mysqli_query($conn, "INSERT INTO cart(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      $message[] = 'Produk Ditambahkan Ke Keranjang!';
   }

}

$select_products = mysqli_query($conn, "SELECT * FROM products LIMIT $items_per_page OFFSET $offset") or die('query failed');


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Belanja</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Belanja</h3>
   <p> <a href="home.php">Home</a> / Belanja </p>
</div>

<section class="products">

   <h1 class="title">Daftar Produk</h1>

   <div class="box-container">

      <?php  
      $select_products = mysqli_query($conn, "SELECT * FROM products LIMIT $items_per_page OFFSET $offset") or die('query failed');
      if(mysqli_num_rows($select_products) > 0){
      while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
      <div class="name"><?php echo $fetch_products['name']; ?></div>
      <div class="price">Rp<?php echo number_format($fetch_products['price'], 0, ',', '.'); ?></div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <a href="detail_produk_user.php?id=<?php echo $fetch_products['id']; ?>&page=<?php echo $current_page; ?>" class="detail-btn">Detail</a>

      <input type="submit" value="Tambah Ke Keranjang" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">Produk Tidak Tersedia!</p>';
      }
      ?>
   </div>

</section>

<div class="pagination">
   <?php
   for ($i = 1; $i <= $total_pages; $i++) {
      echo '<a href="shop.php?page=' . $i . '"';
      if ($i == $current_page) {
         echo ' class="active"';
      }
      echo '>' . $i . '</a>';
   }
   ?>
</div>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>