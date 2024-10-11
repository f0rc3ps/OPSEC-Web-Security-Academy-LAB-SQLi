<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT d.*,c.name as `category` from `damage_list` d inner join `item_list` i on d.item_id = i.id inner join category_list c on i.category_id = c.id where d.id = '{$_GET['id']}' and c.`delete_flag` = 0 and i.`delete_flag` = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="damage-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="item_id" class="control-label">Item</label>
			<select name="item_id" id="item_id" class="form-control form-control-sm rounded-0" required="required">
				<option value="" <?= !isset($item_id) ? 'selected' : '' ?>></option>
				<?php 
				$item = $conn->query("SELECT i.*, c.name as category FROM `item_list` i inner join `category_list` c on i.category_id = c.id where i.delete_flag = 0 and c.delete_flag = 0 and c.`status` = 1 order by `name` asc");
				while($row = $item->fetch_assoc()):
				?>
				<option value="<?= $row['id'] ?>" <?= isset($item_id) && $item_id == $row['id'] ? 'selected' : '' ?>><?= $row["category"] . " | " . $row['code'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Description</label>
			<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
				<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Unfixed</option>
				<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Fixed</option>
			</select>
		</div>
	</form>
</div>
<script>
	$(document).ready(function(){
		$('#uni_modal').on('shown.bs.modal',function(){
			$('#item_id').select2({
				placeholder:"Please select item here",
				width:'100%',
				dropdownParent:$('#uni_modal'),
				containerCssClass:'form-control form-control-sm rounded-0'
			})
		})
		$('#damage-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_damage",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						// location.reload()
						alert_toast(resp.msg, 'success')
						uni_modal("<i class='fa fa-th-list'></i> Damge Details ","damage/view_damage.php?id="+resp.cid)
						$('#uni_modal').on('hide.bs.modal', function(){
							location.reload()
						})
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").scrollTop(0);
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>