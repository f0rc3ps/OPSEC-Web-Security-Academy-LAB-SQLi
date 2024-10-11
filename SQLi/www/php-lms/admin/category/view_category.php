<?php
require_once(__DIR__.'/../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `category_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		echo '<script>alert("prison ID is not valid."); location.replace("./?page=prisons")</script>';
	}
}else{
	echo '<script>alert("prison ID is Required."); location.replace("./?page=prisons")</script>';
}
?>
<style>
	#uni_modal .modal-footer{
		display:none;
	}
	#cimg{
		width: 150px;
		height: 150px;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="container-fluid">
	<div class="d-flex w-100 justify-content-center">
		<img src="<?= validate_image($image_path ?? $_settings->info('logo')) ?>" alt="<?= $name ?? '' ?>" id="cimg">
	</div>
	<dl>
		<dt class="text-muted">Name</dt>
		<dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
		<dt class="text-muted">Description</dt>
		<dd class="pl-4"><?= isset($description) ? $description : "" ?></dd>
		<dt class="text-muted">Status</dt>
		<dd class="pl-4">
			<?php if($status == 1): ?>
				<span class="badge badge-success px-3 rounded-pill">Active</span>
			<?php else: ?>
				<span class="badge badge-danger px-3 rounded-pill">Inactive</span>
			<?php endif; ?>
		</dd>
	</dl>
</div>
<hr class="mx-n3">
<div class="text-right pt-1">
	<button class="btn btn-sm btn-flat btn-light bg-gradient-light border" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>