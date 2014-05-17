<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);
$orders_ammount = isset($_GET['orders_ammount']) && $_GET['orders_ammount'] > 0 && $_GET['orders_ammount'] <= 200 ? (int)$_GET['orders_ammount'] : 50; 
$orders = mysql_select_multi('SELECT * FROM `znote_shop_orders` ORDER BY `id` DESC LIMIT ' . $orders_ammount);
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Sex Change', 4 => 'Custom');
?>
<h1>Admin Shop</h1>
<h2>Shop Orders</h2>
<p>Shows latest <?php echo $orders_ammount; ?> shop orders.</p>
<form action="" method="get">
	<label for="orders_length">Enter ammount of orders to show:</label> <input type="number" max="200" min="1" name="orders_ammount" value="<?php echo $orders_ammount; ?>"/>
	<input type="submit" value="Submit"/>
</form>
<table>
	<thead>
		<th>Id</th>
		<th>Account</th>
		<th>Type</th>
		<th>Item</th>
		<th>Count</th>
		<th>Date</th>
	</thead>
	<tbody>
		<?php foreach(($orders ? $orders : array()) as $order) { ?>
		<tr>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo user_account_id_from_name($order['account_id']); ?></td>
			<td><?php echo $order_types[$order['type']] ?></td>
			<td><?php echo getItemNameById($order['itemid']) . ' (' . $order['itemid'] . ')' ?></td>
			<td><?php echo $order['count'] ?></td>
			<td><?php echo date('Y/m/d H:i', $order['time']) ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
include 'layout/overall/footer.php';
?>