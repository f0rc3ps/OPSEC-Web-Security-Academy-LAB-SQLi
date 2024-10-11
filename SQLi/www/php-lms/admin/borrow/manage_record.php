<?php
//require_once('./../../config.php');
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
    }
}
?>
<h3><strong><?= (isset($id) && !empty($id)) ? "Edit Borrow Record" : "Add New Borrow Record" ?></strong></h3>
<hr>
<div class="card rounded-0 shadow">
	<div class="card-body rounded-0">
		<div class="container-fluid">
			<form action="" id="borrow-form">
				<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
				<input type="hidden" name ="type" value="<?= $type ?? 1 ?>">
				<div class="form-group">
					<label for="name" class="control-label">Borrower</label>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="borrower" id="borrower1" value="Faculty" <?= (!isset($meta['borrower']) || (isset($meta['borrower']) && $meta['borrower'] == "Faculty")) ? "checked" : "" ?>>
						<label class="form-check-label" for="borrower1">
							Faculty
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="borrower" id="borrower2" value="Student" <?= (isset($meta['borrower']) && $meta['borrower'] == "Student") ? "checked" : "" ?>>
						<label class="form-check-label" for="borrower2">
							Student
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="borrower" id="borrower3" value="Staff" <?= (isset($meta['borrower']) && $meta['borrower'] == "Staff") ? "checked" : "" ?>>
						<label class="form-check-label" for="borrower3">
							Staff
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="form-group">
							<label for="borrower_name" class="control-label">Borrower Name</label>
							<input type="text" name="borrower_name" id="borrower_name" class="form-control form-control-sm rounded-0" value="<?= $meta["borrower_name"] ?? "" ?>"  required/>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="form-group" id="faculty-department">
							<label for="faculty_department" class="control-label">Department</label>
							<input type="text" name="faculty_department" id="faculty_department" class="form-control form-control-sm rounded-0" value="<?= $meta["faculty_department"] ?? "" ?>"  required/>
						</div>
						<div class="form-group" id="student-year-sec">
							<label for="student_year_sec" class="control-label">Course/Year/Section</label>
							<input type="text" name="student_year_sec" id="student_year_sec" class="form-control form-control-sm rounded-0" value="<?= $meta["student_year_sec"] ?? "" ?>"  required/>
						</div>
						<div class="form-group" id="staff-department">
							<label for="staff_department" class="control-label">Department</label>
							<input type="text" name="staff_department" id="staff_department" class="form-control form-control-sm rounded-0" value="<?= $meta["student_year_sec"] ?? "" ?>"  required/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="form-group">
							<label for="borrowed_date" class="control-label">Borrowed Date</label>
							<input type="date" id="borrowed_date" name="borrowed_date" class="form-control form-control-sm rounded-0" value="<?= $meta['borrowed_date'] ?? date("Y-m-d") ?>" required>
						</div>
					</div>
					<?php if(isset($id) && !empty($id)): ?>
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="form-group">
							<label for="returned_date" class="control-label">Returned Date</label>
							<input type="date" id="returned_date" name="returned_date" class="form-control form-control-sm rounded-0" value="<?= $meta["returned_date"] ?? "" ?>">
						</div>
					</div>
					<?php endif; ?>
				</div>
				<hr>
				<fieldset>
					<div class="row">
						<div class="col-auto pr-2">
							<label for="item_select" class="control-label">Item:</label>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12">
							<?php 
							if(isset($id) && !empty($id)){
								$item_qry_where = " and `item_list`.`id` NOT IN (SELECT `record_item_list`.`id` from `record_item_list` where `record_id` IN (SELECT `record_list`.`id` FROM `record_list` where `availability` = 0 and `record_list`.`id` != '{$id}') ) and `item_list`.id NOT IN (SELECT damage_list.`item_id` FROM `damage_list` where `status` = 0) ";
							}else{
								$item_qry_where = " and `item_list`.`id` NOT IN (SELECT `record_item_list`.`id` from `record_item_list` where `record_id` IN (SELECT `record_list`.`id` FROM `record_list` where `availability` = 0) )  and `item_list`.id NOT IN (SELECT damage_list.`item_id` FROM `damage_list` where `status` = 0) ";
							}
							$item_sql = "SELECT `item_list`.`id`, `item_list`.`code`,`category_list`.`name` as `category` FROM `item_list` inner join `category_list` on `item_list`.category_id = `category_list`.id where `item_list`.delete_flag = 0 and `category_list`.delete_flag = 0 {$item_qry_where} order by `category_list`.`name`, `item_list`.`code` asc";
							$item_qry = $conn->query($item_sql);
							?>
							<select name="" id="item_select" class="custom-select custom-select-sm rounded-0">
								<option value="" disabled selected></option>
								<?php 
								
								if($item_qry->num_rows > 0):
									while($row = $item_qry->fetch_assoc()):
								?>
								<option value="<?= $row['id'] ?>"><?= $row['category']. " | ". $row['code'] ?></option>
								<?php endwhile; ?>
								<?php endif; ?>
							</select>
						</div>
						<div class="col-auto pr-2">
							<button class="btn btn-primary btn-sm rounded-0" id="add_item" type="button"><i class="fa fa-plus"></i> Add to List</button>
						</div>
					</div>
					<h4><strong>Items</strong></h4>
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
											<div class="col-auto flex-shrink-1">
												<button class="btn btn-danger btn-sm px-1 py-1 remove-item" type="button">
													<i class="fa fa-trash"></i>
												</button>
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
							<textarea id="remarks" rows="4" name="remarks" class="form-control form-control-sm rounded-0" required><?= $meta['remarks'] ?? "" ?></textarea>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer rounded-0">
		<div class="d-flex w-100 justify-content-end">
			<button class="btn btn-primary rounded mr-2" form="borrow-form">Save</button>
			<a href="./?page=borrow" class="btn btn-secondary rounded mr-2" type="">Cancel</a>
		</div>
	</div>
</div>

<script>
	function remove_item(_this){
		_this.remove()
	}
	function add_to_list(){
		var item = $('#item_select').val()
		var itemTxt = $(`#item_select option[value="${item}"]`).text();
		var _div = $("<div></div>")

		_div.addClass("list-group-item list-group-item-action")
		_div.html(`
			<div class="d-flex w-100 align-items-center">
				<div class="col-auto flex-grow-1">
					<input type="hidden" name="item_id[]" value="${item}">
					<span>${itemTxt}</span>
				</div>
				<div class="col-auto flex-shrink-1">
					<button class="btn btn-danger btn-sm px-1 py-1 remove-item" type="button">
						<i class="fa fa-trash"></i>
					</button>
				</div>
			</div>
		`)
		_div.find('.remove-item').click(function(e){
			e.preventDefault()
			remove_item(_div)
		})
		$('#item-list').append(_div)
		$('#item_select').val("")
		$('#item_select').trigger("change")
	}
	$(document).ready(function(){
		$('#item_select').select2({
			placeholder:"Please select category here",
			width:'100%',
			containerCssClass:'form-control form-control-sm rounded-0'
		})
		$('#add_item').click(function(){
			add_to_list();
		})
		if($('#item-list .list-group-item').length > 0){
			$('#item-list .list-group-item').each(function(){
				$(this).click(function(e){
					e.preventDefault()
					remove_item($(this))
				})
			})
		}
		$('[name="borrower"]').change(function(){
			if($(this).val() == "Faculty"){
				$("#faculty-department").toggle(true)
				if(Boolean(`<?= (isset($meta["borrower"]) && $meta["borrower"] == "Faculty") ? 1 : 0 ?>`)){
					$("#faculty_department").val(`<?= $meta["faculty_department"] ?? "" ?>`)
				}else{
					$("#faculty_department").val("")
				}
				$("#faculty_department").attr("required", true);
				$("#student-year-sec").toggle(false)
				$("#student_year_sec").val("")
				$("#student_year_sec").attr("required", false);
				$("#staff-department").toggle(false)
				$("#staff_department").val("")
				$("#staff_department").attr("required", false);
			}else if($(this).val() == "Student"){
				$("#student-year-sec").toggle(true)
				if(Boolean(`<?= (isset($meta["borrower"]) && $meta["borrower"] == "Student") ? 1 : 0 ?>`)){
					$("#student_year_sec").val(`<?= $meta["student_year_sec"] ?? "" ?>`)
				}else{
					$("#student_year_sec").val("")
				}
				$("#student_year_sec").attr("required", true);
				$("#faculty_department").attr("required", false);
				$("#staff_department").attr("required", false);
				$("#faculty-department").toggle(false)
				$("#faculty_department").val("")
				$("#staff-department").toggle(false)
				$("#staff_department").val("")
			}else if($(this).val() == "Staff"){
				$("#staff-department").toggle(true)
				if(Boolean(`<?= (isset($meta["borrower"]) && $meta["borrower"] == "Faculty") ? 1 : 0 ?>`)){
					$("#staff_department").val(`<?= $meta["staff_department"] ?? "" ?>`)
				}else{
					$("#staff_department").val("")
				}
				$("#staff_department").attr("required", true);
				$("#student_year_sec").attr("required", false);
				$("#faculty_department").attr("required", false);
				$("#student-year-sec").toggle(false)
				$("#student_year_sec").val("")
				$("#faculty-department").toggle(false)
				$("#faculty_department").val("")
			}

		})
		$('[name="borrower"]:checked').trigger("change")
		$('#borrow-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_record",
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
						$(window).unbind("beforeunload")
						location.replace("./?page=borrow/view_borrow&id="+resp.cid);
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

		$('input, textarea, select').on('input change paste cut', function(){
			$(window).bind("beforeunload",function(event) {
				// if(hasChanged) 
				return "You have unsaved changes";
			});
		})
	})
	
</script>