<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
	.borrow-img{
		width:3em;
		height:3em;
		object-fit:cover;
		object-position:center center;
	}
</style>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Borrowing Records</h3>
		<div class="card-tools">
			<a href="./?page=borrow/manage_record" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Code</th>
						<th>Borrowed Date</th>
						<th>Returned Date</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT * from `record_list` where delete_flag = 0 and `type` = 1 order by abs(unix_timestamp(`created_at`)) asc ");
						while($row = $qry->fetch_assoc()):
							$meta_sql = "SELECT * FROM `record_meta` where record_id = '{$row['id']}'";
							
							$meta_qry = $conn->query($meta_sql);
							$meta = array_column($meta_qry->fetch_all(MYSQLI_ASSOC), 'meta_value', 'meta_name');
					?>
						<tr>
							<td class="px-2 py-1 text-center"><?php echo $i++; ?></td>
							<td class="px-2 py-1"><?php echo date("Y-m-d H:i",strtotime($row['created_at'])) ?></td>
							<td class="px-2 py-1"><?= $row['code'] ?></td>
							<td class="px-2 py-1"><?= $meta['borrowed_date'] ?? "---" ?></td>
							<td class="px-2 py-1"><?= $meta['returned_date'] ?? "---" ?></td>
							<td class="text-center">
								<?php if(isset($meta['returned_date']) && !empty($meta['returned_date'])): ?>
									<span class="badge badge-primary px-3 rounded-pill">Returned</span>
								<?php else: ?>
									<span class="badge badge-info px-3 rounded-pill">Borrowed</span>
								<?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="./?page=borrow/view_borrow&id=<?= $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="./?page=borrow/manage_record&id=<?= $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this borrow record permanently?","delete_borrow",[$(this).attr('data-id')])
		})
		$('#create_new').click(function(){
			uni_modal("<i class='far fa-plus-square'></i> Add New Borrow Record ","borrows/manage_borrow.php")
		})
		$('.edit-data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Borrow Record ","borrows/manage_borrow.php?id="+$(this).attr('data-id'))
		})
		$('.view-data').click(function(){
			uni_modal("<i class='fa fa-th-list'></i> Borrow Record ","borrows/view_borrow.php?id="+$(this).attr('data-id'))
		})
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [6] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	function delete_borrow($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_borrow",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>