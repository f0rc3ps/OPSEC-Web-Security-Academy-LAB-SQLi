<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				if(!is_numeric($v))
					$v = $this->conn->real_escape_string(htmlspecialchars($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$this->conn->real_escape_string($name)}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = "Category successfully updated.";
			if(!empty($_FILES['img']['tmp_name'])){
				$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$dir = "uploads/category_images/";
				if(!is_dir(base_app.$dir))
					mkdir(base_app.$dir);
				$fname = $dir.$cid.'.png';
				$accept = array('image/jpeg','image/png');
				if(!in_array($_FILES['img']['type'],$accept)){
					$resp['msg'] .= "Image file type is invalid";
				}
				if($_FILES['img']['type'] == 'image/jpeg')
					$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
				elseif($_FILES['img']['type'] == 'image/png')
					$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
				if(!$uploadfile){
					$resp['msg'] .= "Image is invalid";
				}
				$temp = imagescale($uploadfile,200,200);
				if(is_file(base_app.$fname))
				unlink(base_app.$fname);
				$upload =imagepng($temp,base_app.$fname);
				if($upload){
					$qry = $this->conn->query("UPDATE category_list set image_path = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$cid}' ");
				}
				imagedestroy($temp);
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_item(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				if(!empty($v) && !is_numeric($v))
				$v = $this->conn->real_escape_string(htmlspecialchars($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `item_list` where `code` = '{$this->conn->real_escape_string($code)}' and `category_id` = '{$category_id}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Item Code already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `item_list` set {$data} ";
		}else{
			$sql = "UPDATE `item_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Item has been saved successfully.";
			else
				$resp['msg'] = " Item has been updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_item(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `item_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Item has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_record(){
		extract($_POST);
		$data = "";
		$record_field = ["type", 'availability', 'user_id'];
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && in_array($k, $record_field)){
				if(!empty($data)) $data .=",";
				if(!empty($v) && !is_numeric($v))
					$v = $this->conn->real_escape_string(htmlspecialchars($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if($type == 1){
			if(empty($returned_date)){
				if(!empty($data)) $data .=",";
				$data .= " `availability` = 0 ";
			}else{
				if(!empty($data)) $data .=",";
				$data .= " `availability` = 1 ";
			}
		}
		if(empty($id)){
			$code_iterator = 0;
			$get_code = $this->conn->query("SELECT `code` FROM `record_list` order by abs(unix_timestamp(`created_at`)) desc limit 1");
			if($get_code->num_rows > 0){
				$code_iterator = $get_code->fetch_assoc()['code'];
				$code_i_ex = explode("-", $code_iterator);
				$code_iterator = $code_i_ex[1] ?? 0;
				$code_iterator = intval($code_iterator) + 1;
			}else{
				$code_iterator++;
			}
			$__code = sprintf('REC-%010d', $code_iterator);
			if(!empty($data)) $data .=",";
			$data .= " `code` = '{$__code}' ";
			if(!empty($data)) $data .=",";
			$data .= " `user_id` = '{$_SESSION['userdata']['id']}' ";
			$sql = "INSERT INTO `record_list` set {$data} ";
		}else{
			$sql = "UPDATE `record_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$supported_meta_keys = [];
			foreach($_POST as $k => $v){
				if(!is_array($_POST[$k]) && !in_array($k, $record_field) && $k != "id"){
					$do_exists = $this->conn->query("SELECT `id` from `record_meta` where `record_id` = '{$cid}' and `meta_name` = '{$k}'");
					if($do_exists->num_rows > 0){
						$meta_id = $do_exists->fetch_assoc()['id'];
						$update = $this->conn->query("UPDATE `record_meta` set `meta_value` = '{$this->conn->real_escape_string($v)}' where `id` = '{$meta_id}'");
					}else{
						$insert = $this->conn->query("INSERT INTO `record_meta` set `meta_name` = '{$k}', `meta_value` = '{$this->conn->real_escape_string($v)}', `record_id` = '{$cid}'");
					}
					$supported_meta_keys[] = $k;
				}else if(is_array($_POST[$k]) && $k=="item_id"){
					$rids = [];
					foreach($_POST[$k] as $_k => $item_id ){
						$item_exists = $this->conn->query("SELECT `id` from `record_item_list` where `item_id` = '{$item_id}' and `record_id` = '{$cid}' ");
						if($item_exists->num_rows > 0){
							$rids[]= $item_exists->fetch_array()['id'];
						}else{
							$insert = $this->conn->query("INSERT INTO `record_item_list` set `record_id` = '{$cid}', `item_id` = '{$item_id}'");
							if($insert){
								$rids[] = $this->conn->insert_id; 
							}
						}
					}
					$delete = $this->conn->query("DELETE FROM `record_item_list` where `id` NOT IN (".implode(",", $rids).")");
				}
			}
			$meta_remove_where = " where `record_id` = '{$cid}'";
			if(!empty($supported_meta_keys)){
				$meta_remove_where .= " and `meta_name` NOT IN ('".implode("', '", $supported_meta_keys)."') ";
			}
			$delete = $this->conn->query("DELETE FROM `record_meta` {$meta_remove_where}");

			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New record has been added successfully.";
			else
				$resp['msg'] = " Record has been updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_record(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `record_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Record has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_damage(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				if(!empty($v) && !is_numeric($v))
				$v = $this->conn->real_escape_string(htmlspecialchars($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$code_iterator = 0;
			$get_code = $this->conn->query("SELECT `code` FROM `record_list` order by abs(unix_timestamp(`created_at`)) desc limit 1");
			if($get_code->num_rows > 0){
				$code_iterator = $get_code->fetch_assoc()['code'];
				$code_i_ex = explode("-", $code_iterator);
				$code_iterator = $code_i_ex[1] ?? 0;
				$code_iterator = intval($code_iterator) + 1;
			}else{
				$code_iterator++;
			}
			$__code = sprintf('D-%010d', $code_iterator);
			if(!empty($data)) $data .=",";
			$data .= " `code` = '{$__code}' ";
			$sql = "INSERT INTO `damage_list` set {$data} ";
		}else{
			$sql = "UPDATE `damage_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Damaged item has been saved successfully.";
			else
				$resp['msg'] = " Damaged item has been updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_damage(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `damage_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Damage has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_item':
		echo $Master->save_item();
	break;
	case 'delete_item':
		echo $Master->delete_item();
	break;
	case 'save_record':
		echo $Master->save_record();
	break;
	case 'delete_record':
		echo $Master->delete_record();
	break;
	case 'save_damage':
		echo $Master->save_damage();
	break;
	case 'delete_damage':
		echo $Master->delete_damage();
	break;
	default:
		// echo $sysset->index();
		break;
}