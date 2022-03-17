
<?php 
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
$rid='';
$faculty_id='';
$subject_id='';

if(isset($_GET['fid']))
$faculty_id = $_GET['fid'];
// $restriction = $conn->query("SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) as faculty,s.code,s.subject FROM restriction_list r inner join faculty_list f on f.id = r.faculty_id inner join subject_list s on s.id = r.subject_id where academic_id ={$_SESSION['academic']['id']} and r.id in (SELECT restriction_id from evaluation_list where academic_id ={$_SESSION['academic']['id']} and director_id = {$_SESSION['login_id']} ) group by f.id ");
$restriction = $conn->query("SELECT * from evaluation_list where academic_id ={$_SESSION['academic']['id']} and director_id = {$_SESSION['login_id']} group by faculty_id ");
$done_faculty=array();
				while($row=$restriction->fetch_array()){

							$done_faculty[] = $row['faculty_id'];
				}
						
// echo "<pre>",print_r($restriction->fetch_assoc()),"</pre>";die();
$restriction_rows = $conn->query("SELECT * from restriction_list where academic_id ={$_SESSION['academic']['id']} group by faculty_id");

if($restriction->num_rows == $restriction_rows->num_rows){
	$rid = NULL;
}else{
	$rid = 1;
}
// var_dump($restriction->num_rows);die();
// echo "SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) as faculty,s.code,s.subject FROM restriction_list r inner join faculty_list f on f.id = r.faculty_id inner join subject_list s on s.id = r.subject_id where academic_id ={$_SESSION['academic']['id']} and r.id not in (SELECT restriction_id from evaluation_list where academic_id ={$_SESSION['academic']['id']} and director_id = {$_SESSION['login_id']} ) group by f.id ";die();
?>

<div class="col-lg-12">
	<div class="row">
		<div class="col-md-3">
			<label for="faculty">Select Faculty</label>
			<div class="list-group">
			<select name="faculty_id" id="faculty_id" class="form-control form-control-sm select2">
				<option value=""></option>
				<?php 
				$faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
				$f_arr = array();
				$fname = array();
				while($row=$faculty->fetch_assoc()):
					if(!in_array($row['id'], $done_faculty)){
					$f_arr[$row['id']]= $row;
					$fname[$row['id']]= ucwords($row['name']);
				?>
				<option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
				<?php
					}
				 endwhile; ?>
			</select>
		    
			</div>
		</div>	
		<div class="col-md-9">
			<div class="card card-outline card-info">
				<div class="card-header">
					<b>Evaluation Questionnaire for Academic: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Trimester</b>
					<div class="card-tools">
						<button class="btn btn-sm btn-flat btn-warning bg-gradient-warning mx-1" form="manage-evaluation">Submit Evaluation</button>
					</div>
				</div>
				<div class="card-body">
					<fieldset class="border border-info p-2 w-100">
					   <legend  class="w-auto">Rating Legend</legend>
					   <p> 1 – Unsatisfactory: Fails to meet the minimum requirements. 2 – Needs improvement: Meets the minimum requirements.
3 – Satisfactory: Meets the standard requirements required of the activity. 4 – Very Satisfactory: Is it better than the standard.
5 – Outstanding: Is of the highest level. </p>
					</fieldset>
					<form id="manage-evaluation">
						<input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
						<input type="hidden" name="faculty_id" value="<?php echo $faculty_id?>">
						<input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
						<input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
						<input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">
					<div class="clear-fix mt-2"></div>
					<?php 
							$q_arr = array();
						$criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
						while($crow = $criteria->fetch_assoc()):
					?>
					<table class="table table-condensed">
						<thead>
							<tr class="bg-gradient-secondary">
								<th class=" p-1"><b><?php echo $crow['criteria'] ?></b><p> &nbsp; &nbsp; &nbsp;<small><?php echo $crow['criteria_notes'] ?></small></p></th>
								<th class="text-center">1<p>&nbsp;</p></th>
								<th class="text-center">2<p>&nbsp;</p></th>
								<th class="text-center">3<p>&nbsp;</p></th>
								<th class="text-center">4<p>&nbsp;</p></th>
								<th class="text-center">5<p>&nbsp;</p></th>
							</tr>
						</thead>
						<tbody class="tr-sortable">
							<?php 
							$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
							while($row=$questions->fetch_assoc()):
							$q_arr[$row['id']] = $row;
							?>
							<tr class="bg-white">
								<td class="p-1" width="40%">
									<?php echo $row['question'] ?>
									<input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
								</td>
								<?php for($c=1;$c<=5;$c++): ?>
								<td class="text-center">
									<div class="icheck-success d-inline">
				                        <input type="radio" name="rate[<?php echo $row['id'] ?>]" <?php echo $c == 5 ? "checked" : '' ?> id="qradio<?php echo $row['id'].'_'.$c ?>" value="<?php echo $c ?>">
				                        <label for="qradio<?php echo $row['id'].'_'.$c ?>">
				                        </label>
			                      </div>
								</td>
								<?php endfor; ?>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php endwhile; ?>
					<div>
					<label>Additional Comments:</label><br>
					<textarea class="form-control" name="comments"></textarea>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
		}else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
		}
		else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
		}
		if(<?php echo empty($rid) ? 1 : 0 ?> == 1)
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>done.php")
	})
	$('#manage-evaluation').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=save_evaluation',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved.","success");
					setTimeout(function(){
						location.reload()	
					},1750)
				}
			}
		})
	})

	$('select#faculty_id').on('change',function(){
		var f_id = $(this).val();
		var href= $(' a[rf='+f_id+']').attr('href');
		console.log(href);
		window.location.href='./index.php?page=evaluate&fid='+f_id;
	})
</script>