<?php
// require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `record_list` where id = '{$_GET['id']}' and `delete_flag` = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
		if(isset($id) && !empty($id)){
			$meta_qry = $conn->query("SELECT * FROM `record_meta` where record_id = '{$id}'");
			$meta = array_column($meta_qry->fetch_all(MYSQLI_ASSOC), 'meta_value', 'meta_name');
		}
    }else{
		echo '<script>alert("Borrow Record ID is not valid."); location.replace("./?page=borrow")</script>';
	}
}else{
	echo '<script>alert("Borrow Record ID is Required."); location.replace("./?page=borrow")</script>';
}
?>
<h3><strong>Borrow Record</strong></h3>
<hr>
<div class="card rounded-0 shadow">
	<div class="card-body rounded-0">
		<div class="container-fluid">
				<div class="form-group">
					<label class="control-label pr-2">Borrower:</label>
					<span><strong><?= $meta["borrower"] ?? "" ?></strong></span>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="form-group">
							<label class="control-label pr-2">Borrower Name:</label>
							<span><strong><?= $meta["borrower_name"] ?? "" ?></strong></span>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<?php if(($meta["borrower"] ?? "") == "Faculty"){ ?>
							<div class="form-group">
								<label class="control-label pr-2">Department:</label>
								<span><strong><?= $meta["faculty_department"] ?? "" ?></strong></span>
							</div>
						<?php }else if(($meta["borrower"] ?? "") == "Student"){ ?>
							<div class="form-group">
								<label class="control-label pr-2">Course/Year/Section:</label>
								<span><strong><?= $meta["student_year_sec"] ?? "" ?></strong></span>
							</div>
						<?php }else if(($meta["borrower"] ?? "") == "Staff"){ ?>
							<div class="form-group">
								<label class="control-label pr-2">Department:</label>
								<span><strong><?= $meta["staff_department"] ?? "" ?></strong></span>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="form-group">
							<label class="control-label pr-2">Borrowed Date:</label>
							<span><strong><?= $meta["borrowed_date"] ?? "" ?></strong></span>
						</div>
					</div>
					<?php if(isset($id) && !empty($id)): ?>
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="form-group">
							<label class="control-label pr-2">Returned Date:</label>
							<span><strong><?= (isset($meta["returned_date"]) && !empty($meta["returned_date"]))? $meta["returned_date"] : "---" ?></strong></span>
						</div>
					</div>
					<?php endif; ?>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="form-group">
							<label class="control-label pr-2">Status:</label>
							<?php if(isset($meta['returned_date']) && !empty($meta['returned_date'])): ?>
								<span class="badge badge-primary px-3 rounded-pill">Returned</span>
							<?php else: ?>
								<span class="badge badge-info px-3 rounded-pill">Borrowed</span>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<hr>
				<fieldset>
					<h4><strong>Borrowed Items</strong></h4>
					<div class="row">
						<div class="col-lg-7 col-md-10 col-sm-12 col-12">
							<div id="item-list" class="list-group mx-4">
								<?php if(!empty($id)): ?>
								<?php 
								$item_list_sql = "SELECT i.*, c.name as `category` FROM `item_list` i inner join `category_list` c on i.category_id = c.id where i.id IN (SELECT `item_id` FROM `record_item_list` where `record_id` = '{$id}' ) order by c.name, i.code asc";
								$item_list_qry = $conn->query($item_list_sql);
								if($item_list_qry->num_rows > 0):	
									foreach($item_list_qry->fetch_all(MYSQLI_ASSOC) as $row):
								?>
									<div class="list-group-item list-group-item-action">
										<div class="d-flex w-100 align-items-center">
											<div class="col-auto flex-grow-1">
												<input type="hidden" name="item_id[]" value="<?= $row['id'] ?>">
												<span><?= $row['category'] . " | " . $row['code'] ?></span>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
								<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
					
				</fieldset>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-12">
						<div class="form-group">
							<label for="remarks" class="control-label">Remarks</label>
							<p class="px-2"><?= $meta['remarks'] ?? "" ?></p>
						</div>
					</div>
				</div>
		</div>
	</div>
	<div class="card-footer rounded-0">
		<div class="d-flex w-100 justify-content-end">
			<a class="btn btn-primary rounded mr-2" href="./?page=borrow/manage_record&id=<?= $id ?? "" ?>">Edit</a>
			<a href="./?page=borrow" class="btn btn-secondary rounded mr-2" type="">Back to List</a>
		</div>
	</div>
</div>