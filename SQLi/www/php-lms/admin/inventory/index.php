<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
	.inventory-img{
		width: 60px;
		height: 60px;
		object-fit:cover;
		object-position:center center;
	}
	table tbody td, table tbody th{
		vertical-align: middle !important;
	}
</style>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Inventory</h3>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="10%">
					<col width="20%">
					<col width="40%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Photo</th>
						<th>Category</th>
						<th>Total</th>
						<th>Available</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `category_list` where delete_flag = 0 and `status` = 1 order by `name` asc ");
						while($row = $qry->fetch_assoc()):
							$total = 0;
							$available = 0;
							$unavailable = 0;
							$total = $conn->query("SELECT `id` FROM `item_list` where `category_id` = '{$row['id']}' ")->num_rows;

							$unavailable += $conn->query("SELECT `id` FROM `damage_list` where `status` = 0 and `item_id` IN (SELECT `item_list`.`id` FROM `item_list` where `item_list`.`category_id` = '{$row['id']}') ")->num_rows;

							$unavailable += $conn->query("SELECT `id` FROM `record_item_list` where `item_id` IN (SELECT `item_list`.id FROM `item_list` where `category_id` = '{$row['id']}' ) and `record_id` IN (SELECT `record_list`.`id` FROM `record_list` where `availability` = 0) ")->num_rows;
							$available = $total - $unavailable;
					?>
						<tr>
							<td class="text-center "><?php echo $i++; ?></td>
							<td class="px-2 py-1 text-center ">
								<div class="border rounded">
									<img src="<?= validate_image($row['image_path']) ?>" alt="<?= $row['name'] ?>" class="inventory-img">
								</div>
							</td>
							<td class="px-2 py-1 "><?= $row['name'] ?></td>
							<td class="px-2 py-1 text-center "><?= number_format($total) ?></td>
							<td class="px-2 py-1 text-center "><?= number_format($available) ?></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [1] }
			],
			order:[0,'asc']
		});
	})
</script>