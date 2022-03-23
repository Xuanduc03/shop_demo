<?php 
session_start();
$products_id = array();
//$session_destroy();
$connect = mysqli_connect('localhost', 'root', '', 'cart');

//check ìf add to cart button has been submit
if(filter_input(INPUT_POST, 'add_to_cart')){
    if(isset($_SESSION['shopping_cart'])){
        //dem xem co bao nhieu gia tri khi them vao cart
        $count = count($_SESSION['shopping_cart']);
        //Trả về một mảng giá trị đại diện cho một cột từ mảng đầu vào
        $products_id = array_column($_SESSION['shopping_cart'] , 'id');
        //Trả về TRUE nếu giá trị được tìm thấy trong mảng hoặc FALSE nếu không
        if(!in_array(filter_input(INPUT_GET, 'id'), $products_id)){
            $_SESSION['shopping_cart'][$count] = array(
                    'id' => filter_input(INPUT_GET, 'id'),
                    'name' => filter_input(INPUT_POST,"hidden_name"),
                    'price' => filter_input(INPUT_POST,"hidden_price"),
                    'quantity' => filter_input(INPUT_POST,"quantity"),
            );
        }else{
            // đối sánh khóa mảng với id của sản phẩm được thêm vào giỏ hàng
            for($i = 0; $i < count($products_id); $i++){
                if($products_id[$i] == filter_input(INPUT_GET,'id')){
                    //cong tung so luong don hang da chon va ket noi voi shop
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST,'quantity');
                }
            }
        }
    }else{//neu shop ko chay khoi ct, tao mot product moi voi array = 0
        //tao mot array moi khi lay tu form data, lay tu gia tri 0 va gan cho tung gia tri 
        $_SESSION['shopping_cart'][0] = array(
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST,"hidden_name"),
                'price' => filter_input(INPUT_POST,"hidden_price"),
                'quantity' => filter_input(INPUT_POST,"quantity"),
        );
    }
}
//delete cart 
if(filter_input(INPUT_GET, 'action') == 'delete'){
    //lap qua tat ca cac sp va tim dung san pham can remove
    foreach($_SESSION['shopping_cart'] as $key => $product){
        if($product['id'] == filter_input(INPUT_GET, 'id')){
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="cart.css">
    <title>Shopping Cart</title>
</head>
<body>
<br>
<div class="container" style="width: 80%">
        <h2 align='center'>Shopping Cart</h2>
        <?php
            $query = "SELECT * FROM model_x ORDER BY id ASC ";
            $result = mysqli_query($connect, $query);
            if(mysqli_num_rows($result) > 0) {

                while ($product = mysqli_fetch_array($result)) {

                    ?>
                    <div class="col-sm-4 col-md-3">

                        <form method="post" action="cart.php?action=add&id=<?php echo $product["id"]; ?>">

                            <div class="product">
                                <img src="<?php echo $product["image"]; ?>" class="img-responsive">
                                <h5 class="text-info"><?php echo $product["name"]; ?></h5>
                                <h5 class="text-danger"><?php echo $product["price"]; ?> $</h5>
                                <input type="text" name="quantity" class="form-control" value="1">
                                <input type="hidden" name="hidden_name" value="<?php echo $product["name"]; ?>">
                                <input type="hidden" name="hidden_price" value="<?php echo $product["price"]; ?>">
                                <input type="submit" name="add_to_cart" style="margin-top: 5px;" class="btn btn-info"
                                       value="Add to Cart">
                            </div>
                        </form>
                    </div>
                    <?php
                }
            }
        ?>
<div style="clear: both"></div>
    <br><br>
        <div class="table-reponsive">
            <table class="table">
                 <tr><th colspan="5"><h3>Order Details</h3></th></tr>
                    <tr>
                        <th width="40%">Product Name</th>
                        <th width="10%">Quantity</th>
                        <th width="20%">Price</th>
                        <th width="15%">Total</th>
                        <th width="5%">Action</th>
                             </tr>
                                <?php 
                                    if(!empty($_SESSION['shopping_cart'])){
                                        $total = 0;
                                         foreach ($_SESSION['shopping_cart'] as $key => $product):
                                           ?>
                                         <tr>
                                    <td><?php echo $product['name']; ?> </td>
                                    <td><?php echo $product["quantity"]; ?> </td>
                                    <td><?php echo $product["price"]; ?> $ </td>
                                    <td><?php echo number_format($product["quantity"] * $product["price"], 2); ?> $</td>
                                    <td>
                                <a href="cart.php?action=delete&id=<?php echo $product["id"]; ?>">
                                <div class="btn btn-danger">Remove</div>
                                </a>
                             </td>
                        </tr>
                    <?php
                $total = $total + ($product["quantity"] * $product["price"]);
                endforeach;
            ?>
        <tr>
            <td colspan="3" align="right">Total</td>
            <td align="right"><?php echo number_format($total, 2); ?> $</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">
                <?php
                if(isset($_SESSION['shopping_cart'])):
                    if(count($_SESSION['shopping_cart']) > 0):
                ?>
                <a href="#" class="button">Checkout</a>
                <?php endif;  endif; ?> 
            </td>
        </tr>
        <?php
    }
?>
</table>
</div>
<div id="smart-button-container">
      <div style="text-align: center;">
        <div id="paypal-button-container"></div>
      </div>
    </div>
  <script src="https://www.paypal.com/sdk/js?client-id=sb&enable-funding=venmo&currency=USD" data-sdk-integration-source="button-factory"></script>
  <script>
    function initPayPalButton() {
      paypal.Buttons({
        style: {
          shape: 'pill',
          color: 'blue',
          layout: 'vertical',
          label: 'checkout',
          
        },

        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{"amount":{"currency_code":"USD","value":1}}]
          });
        },

        onApprove: function(data, actions) {
          return actions.order.capture().then(function(orderData) {
            
            // Full available details
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));

            // Show a success message within this page, e.g.
            const element = document.getElementById('paypal-button-container');
            element.innerHTML = '';
            element.innerHTML = '<h3>Thank you for your payment!</h3>';

            // Or go to another URL:  actions.redirect('thank_you.html');
            
          });
        },

        onError: function(err) {
          console.log(err);
        }
      }).render('#paypal-button-container');
    }
    initPayPalButton();
  </script>
</body>
</html>
