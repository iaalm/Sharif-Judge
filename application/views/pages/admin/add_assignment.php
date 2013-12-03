<?php
/**
 * Sharif Judge online judge
 * @file add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-1.10.3.custom.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-timepicker-addon.js") ?>"></script>
<link rel="stylesheet" href="<?php echo base_url("assets/styles/flick/jquery-ui-1.10.3.custom.min.css") ?>"/>

<script>
	var numOfProblems=<?php echo count($problems); ?>;
	var row1='<tr>\
		<td>';
	var row2='</td>\
		<td><input type="text" name="name[]" class="sharif_input short" value="Problem "/></td>\
		<input type="hidden" name="score[]" class="sharif_input tiny2" value="100"/>\
		<td><input type="text" name="c_time_limit[]" class="sharif_input tiny2" value="500"/></td>\
		<td><input type="text" name="python_time_limit[]" class="sharif_input tiny2" value="1500"/></td>\
		<td><input type="text" name="java_time_limit[]" class="sharif_input tiny2" value="2000"/></td>\
		<td><input type="text" name="memory_limit[]" class="sharif_input tiny" value="50000"/></td>\
		<td><input type="text" name="languages[]" class="sharif_input short2" value="C,C++,Python 2,Python 3,Java"/></td>\
		<td><input type="text" name="diff_cmd[]" class="sharif_input tiny" value="diff"/></td>\
		<td><input type="text" name="diff_arg[]" class="sharif_input tiny" value="-bB"/></td>\
		<td><input type="checkbox" name="is_upload_only[]" class="check" value="';
	var row3='"/><td><i class="splashy-gem_remove delete_problem"></i></td></td>\
	</tr>';
	$(document).ready(function(){
		$("#add").click(function(){
			$('#problems_table>tbody').children().last().after(row1+(numOfProblems+1)+row2+(numOfProblems+1)+row3);
			numOfProblems++;
			$('#nop').attr('value',numOfProblems);
		});
		$(document).on('click','.delete_problem',function(){
			if (numOfProblems==1) return;
			var row = $(this).parents('tr');
			var id = row.children(':first').html();
			row.remove();
			var i = 0;
			$('#problems_table>tbody').children('tr').each(function(){
				i++;
				$(this).children(':first').html(i);
				$(this).find('[type="checkbox"]').attr('value',i);
			});
			numOfProblems--;
			$('#nop').attr('value',numOfProblems);
		});
		$('#start_time').datetimepicker();
		$('#finish_time').datetimepicker();
	});
</script>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/add.png') ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item">
			<a href="http://docs.sharifjudge.ir/add_assignment" target="_blank"><i class="splashy-help"></i> Help</a>
		</span>
	</div>

	<div id="main_content">

		<?php foreach ($success_messages as $success_message): ?>
			<p class="shj_ok"><?php echo $success_message ?></p>
		<?php endforeach ?>
		<?php foreach ($error_messages as $error_message): ?>
			<p class="shj_error"><?php echo $error_message ?></p>
		<?php endforeach ?>

		<?php if ($edit): ?>
		<p>
			<i class="splashy-information"></i> If you don't want to change tests, just do not upload any file.
		</p>
		<?php endif ?>

		<?php echo form_open_multipart($edit?'assignments/edit/'.$edit_assignment['id']:'assignments/add') ?>
		<div class="panel_left">
			<input type="hidden" name="number_of_problems" id="nop" value="<?php echo $edit?$edit_assignment['problems']:count($problems); ?>"/>
			<p class="input_p">
				<label for="assignment_name">Assignment Name</label>
				<input type="text" name="assignment_name" class="sharif_input medium" value="<?php
					if ($edit)
						echo $edit_assignment['name'];
					else
						echo set_value('assignment_name');
				?>"/>
				<?php echo form_error('assignment_name','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="start_time">Start Time</label>
				<input type="text" name="start_time" id="start_time" class="sharif_input medium" value="<?php
					if ($edit)
						echo date('m/d/Y H:i',strtotime($edit_assignment['start_time']));
					else
						echo set_value('start_time');
				?>" />
				<?php echo form_error('start_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="finish_time">Finish Time</label>
				<input type="text" name="finish_time" id="finish_time" class="sharif_input medium" value="<?php
					if ($edit)
						echo date('m/d/Y H:i',strtotime($edit_assignment['finish_time']));
					else
						echo set_value('finish_time');
				?>" />
				<?php echo form_error('finish_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p clear">
				<label style="display:none" for="extra_time">
					Extra Time (minutes)<br>
					<span class="form_comment">Extra time for late submissions.</span>
				</label>
				<input style="display:none" type="text" name="extra_time" id="extra_time" class="sharif_input medium" value="0" />
				<?php echo form_error('extra_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p clear">
				<label for="participants">Participants<br>
					<span class="form_comment">Enter username of participants here (comma separated).
						Only these users are able to submit. You can use keyword "ALL".</span>
				</label>
				<textarea name="participants" rows="5" class="sharif_input medium"><?php
					if ($edit)
						echo $edit_assignment['participants'];
					else
						echo set_value('participants','ALL');
					?></textarea>
			</p>
			<p class="input_p clear">
				<label for="tests">Tests (zip file)<br>
					<span class="form_comment">
						<a href="http://docs.sharifjudge.ir/tests_structure" target="_blank">Use this structure</a>
					</span>
				</label>

				<input type="file" name="tests" class="sharif_input medium"/>
				<?php
					if (!$edit)
						echo $this->upload->display_errors('<div class="shj_error">','</div>');
				?>
			</p>
		</div>
		<div class="panel_right">
			<p class="input_p">
				<input type="checkbox" name="open" value="1" <?php if ($edit) echo $edit_assignment['open']?'checked':''; else echo set_checkbox('open','1') ?> /> Open<br>
				<span class="form_comment">Open or close this assignment.</span>
				<?php echo form_error('open','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<input type="checkbox" name="scoreboard" value="1" <?php if ($edit) echo $edit_assignment['scoreboard']?'checked':''; else echo set_checkbox('scoreboard','1') ?> /> Scoreboard<br>
				<span class="form_comment">Check this to enable scoreboard.</span>
				<?php echo form_error('scoreboard','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_h_p">
				<label for="late_rule" style="display:none">Coefficient rule (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#coefficient_rule">?</a>)</label><br>
				<span class="form_comment medium clear" style="display:none">PHP script without <?php echo htmlspecialchars('<?php ?>') ?> tags</span>
				<textarea style="display:none" name="late_rule" rows="14" class="sharif_input add_text"><?php
						if ($edit)
							echo $edit_assignment['late_rule'];
						else
							echo set_value('late_rule', $this->settings_model->get_setting('default_late_rule'))
				?></textarea>
				<?php echo form_error('late_rule','<div class="shj_error">','</div>'); ?>
			</p>
		</div>
		<p class="input_p" id="add_problems">Problems <i class="splashy-add" id="add"></i>
		<table id="problems_table">
			<thead>
			<tr>
				<th rowspan="2"></th>
				<th rowspan="2">Name</th>
				<th colspan="3" style="border-bottom: 1px solid #BDBDBD">Time Limit (ms)</th>
				<th rowspan="2">Memory<br>Limit (kB)</th>
				<th rowspan="2">Allowed<br>Languages (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#allowed_languages">?</a>)</th>
				<th rowspan="2">Diff<br>Command (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#diff_command">?</a>)</th>
				<th rowspan="2">Diff<br>Argument (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#diff_arguments">?</a>)</th>
				<th rowspan="2">Upload<br>Only (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#upload_only">?</a>)</th>
				<th rowspan="2"></th>
			</tr>
			<tr>
				<th>C/C++</th><th>Python</th><th>Java</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($problems as $problem): ?>
				<tr>
					<td><?php echo $problem['id']?></td>
					<td><input type="text" name="name[]" class="sharif_input short" value="<?php echo $problem['name'] ?>"/></td>
					<td style="display:none"><input type="text" name="score[]" class="sharif_input tiny2" value="<?php echo $problem['score'] ?>"/></td>
					<td><input type="text" name="c_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['c_time_limit'] ?>"/></td>
					<td><input type="text" name="python_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['python_time_limit'] ?>"/></td>
					<td><input type="text" name="java_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['java_time_limit'] ?>"/></td>
					<td><input type="text" name="memory_limit[]" class="sharif_input tiny" value="<?php echo $problem['memory_limit'] ?>"/></td>
					<td><input type="text" name="languages[]" class="sharif_input short2" value="<?php echo $problem['allowed_languages'] ?>"/></td>
					<td><input type="text" name="diff_cmd[]" class="sharif_input tiny" value="<?php echo $problem['diff_cmd'] ?>"/></td>
					<td><input type="text" name="diff_arg[]" class="sharif_input tiny" value="<?php echo $problem['diff_arg'] ?>"/></td>
					<td><input type="checkbox" name="is_upload_only[]" class="check" value="<?php echo $problem['id'] ?>" <?php if ($problem['is_upload_only']) echo "checked" ?>/></td>
					<td><i class="splashy-gem_remove delete_problem"></i></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
		</p>
		<?php echo form_error('name[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('score[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('c_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('python_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('java_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('memory_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('languages[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('diff_cmd[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('diff_arg[]','<div class="shj_error">','</div>'); ?>
		<p class="input_p">
			<input type="submit" value="<?php echo $edit?'Edit':'Add' ?> Assignment" class="sharif_input"/>
		</p>
		</form>

	</div> <!-- main_content -->

</div> <!-- main_container -->