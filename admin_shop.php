<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);

$orders = mysql_select_multi('SELECT * FROM `znote_shop_orders` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfits', 6 =>'Mounts');
$items = getItemList();
?>
<h1>Shop Logs</h1>

<h2>Pending Orders</h2>
<p>These are pending orders, like items bought, but not received or used yet.</p>
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
			<td><?php echo '(' . $order['itemid'] . ') ', (isset($items[$order['itemid']])) ? $items[$order['itemid']] : ''; ?></td>
			<td><?php echo $order['count'] ?></td>
			<td><?php echo date('Y/m/d H:i', $order['time']) ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php
$orders = mysql_select_multi('SELECT * FROM `znote_shop_logs` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfit', 6 =>'Mount', 7 =>'Custom');
?>
<h2>Order History</h2>
<p>This list contains all transactions bought in the shop.</p>
<table>
	<thead>
		<th>Id</th>
		<th>Account</th>
		<th>Type</th>
		<th>Item</th>
		<th>Count</th>
		<th>points</th>
		<th>Date</th>
	</thead>
	<tbody>
		<?php foreach(($orders ? $orders : array()) as $order) { ?>
		<tr>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo $order['account_id']; ?></td>
			<td><?php echo $order_types[$order['type']] ?></td>
			<td><?php echo '(' . $order['itemid'] . ') ', (isset($items[$order['itemid']])) ? $items[$order['itemid']] : ''; ?></td>
			<td><?php echo $order['count'] ?></td>
			<td><?php echo $order['points'] ?></td>
			<td><?php echo getClock($order['time'], true, false); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
include 'layout/overall/footer.php';
?>
