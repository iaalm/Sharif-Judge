<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript" src="<?php echo base_url("assets/jquery-syntax/jquery.syntax.min.js") ?>"></script>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/reveal/reveal.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/reveal/jquery.reveal.js") ?>"></script>

<script>
	$(document).ready(function(){
		$(".btn").click(function(){
			var button = $(this);
			var row = button.parents('tr');
			if (button.attr('shj')=='download'){
				window.location = '<?php echo site_url('submissions') ?>/download_file/'+row.attr('u')+'/'+row.attr('a')+'/'+row.attr('p')+'/'+row.attr('s');
				return;
			}
			var view_code_request = $.ajax({
				cache: true,
				type: 'POST',
				url: '<?php echo site_url('submissions/view_code') ?>',
				data: {
					code: button.attr('code'),
					username: row.attr('u'),
					assignment: row.attr('a'),
					problem: row.attr('p'),
					submit_id: row.attr('s'),
					<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
				},
				success: function(data){
					$(".modal_inside").html(data);
					$.syntax({
						blockLayout: 'fixed',
						theme: 'paper'
					});
				}
			});
			$('#shj_modal').reveal(
				{
					on_close_modal: function(){
						$(".modal_inside").html('<div style="text-align: center;">Loading<br><img src="<?php echo base_url('assets/images/loading.gif') ?>"/></div>');
						view_code_request.abort();
					}
				}
			);

		});
		$(".shj_rejudge").click(function(){
			var row = $(this).parents('tr');
			$.post(
				'<?php echo site_url('rejudge/rejudge_one') ?>',
				{
					username: row.attr('u'),
					assignment: row.attr('a'),
					problem: row.attr('p'),
					submit_id: row.attr('s'),
					<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
				},
				function (data) {
					if (data == 'success')
						location.reload();
				}
			);
		});
	});
</script>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>"{$view}_submissions")); ?>
<?php
$finish = strtotime($assignment['finish_time']);
?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url("assets/images/icons/{$view}_submissions.png") ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item">
			<a href="<?php echo $excel_link ?>"><i class="splashy-document_small_download"></i> Excel</a>
		</span>
		<?php if ($filter_user): ?>
		<span class="title_menu_item">
			<a href="<?php echo site_url('submissions/'.$view.($filter_problem?'/problem/'.$filter_problem:'')) ?>"><i class="splashy-tag_remove"></i> Remove Username Filter</a>
		</span>
		<?php endif ?>
		<?php if ($filter_problem): ?>
		<span class="title_menu_item">
			<a href="<?php echo site_url('submissions/'.$view.($filter_user?'/user/'.$filter_user:'')) ?>"><i class="splashy-tag_remove"></i> Remove Problem Filter</a>
		</span>
		<?php endif ?>
	</div>

	<div id="main_content">
		<p><?php echo ucfirst($view); ?> Submissions of <?php echo $assignment['name']; ?></p>
		<table class="sharif_table">
			<thead>
				<tr>
				<?php if ($user_level>0): ?>
						<th width="5%">submit ID</th>
						<th width="6%">Username</th>
						<th width="14%">Display Name</th>
						<th width="10%">Problem</th>
						<th width="14%">Submit Time</th>
						<th width="10%">Score</th>
						<th width="1%">Language</th>
						<th width="6%">Status</th>
						<th width="6%">Code</th>
						<?php if ($user_level>=2): ?>
						<th width="1%">Rejudge</th>
						<?php endif ?>
						<th width="1%">#</th>
					</tr>
			
				<?php else: ?>
						<th width="10%">Problem</th>
						<th width="30%">Submit Time</th>
						<th width="7%">Score</th>
						<th width="1%">Language</th>
						<th width="30%">Status</th>
						<th width="15%">Code</th>
						<th width="5%">#</th>
					</tr>
				<?php endif ?>
			</thead>
			<?php $i=0; $j=0; $un=''; ?>
			<?php foreach ($items as $item): ?>
				<?php
				$i++;
				if ($item['username']!=$un)
					$j++;
				$un = $item['username'];
				?>
				<tr u="<?php echo $item['username'] ?>" a="<?php echo $item['assignment'] ?>" p="<?php echo $item['problem'] ?>" s="<?php echo $item['submit_id'] ?>" <?php if ($view=='final' && $j%2==0){ echo 'class="hl"';} ?>>
				<?php if ($user_level>0): ?>
					<?php if ($view=='all'): ?>
						<td><?php echo $item['submit_id'] ?></td>
					<?php else: ?>
						<td><?php echo $i; ?></td>
						<td><?php echo $j; ?></td>
					<?php endif ?>

					<td><a title="Filter Submissions by This Username" href="<?php echo site_url('submissions/'.$view.'/user/'.$item['username'].($filter_problem?'/problem/'.$filter_problem:'')) ?>"><?php echo $item['username'] ?></a></td>
					<td><?php
						if(!isset($name[$item['username']]))
							$name[$item['username']]=$this->user_model->get_user($item['username'])->display_name;
						echo $name[$item['username']];
					?></td>
				<?php endif ?>
					<td><?php
                                                $pi = $this->assignment_model->problem_info($assignment['id'],$item['problem']);
						echo '<a title="Filter Submissions by This Problem" href="'.site_url('submissions/'.$view.($filter_user?'/user/'.$filter_user:'').'/problem/'.$item['problem']).'"><span>('.$item['problem'].')</span><span dir>'.$pi['name'].'</span> </a>';
					?></td>
					<td><?php echo $item['time'] ?></td>
					<td><?php
						$pre_score = ceil($item['pre_score']/60);
						if ($pre_score!=0) echo $pre_score." min";
					?></td>
					<td>
						<?php echo filetype_to_language($item['file_type']) ?>
					</td>
					<td>
						<?php if (substr($item['status'],0,8) == 'Uploaded'): ?>
							<?php echo $item['status'] ?>
						<?php else: ?>
							<?php
								$class = strtolower($item['status']);
								if ($class=='score')
								{
									if ($item['pre_score']>0){
										$class='ok';
                                                                                $item['status'] = 'Accepted';
                                                                        }
									else {
										$class='wrong';
                                                                                $item['status'] = 'Wrong Answer';
                                                                        }
								}
							?>
							<div class="btn <?php echo $class ?>" code="0" >
								<?php
									if ($item['status']==='SCORE')
										echo $final_score;
									else
										echo $item['status'];
								?>
							</div>
						<?php endif ?>
					</td>
					<td>
						<?php if ($item['file_type'] === 'zip' OR $item['file_type'] === 'pdf'): ?>
							<div class="btn view_code" shj="download">Download</div>
						<?php else: ?>
							<div class="btn view_code" code="1" >Code</div>
						<?php endif ?>
					</td>
					<?php if($view === 'final' && $user_level>0): ?>
						<td>
							<?php if ($item['file_type'] === 'zip' OR $item['file_type'] === 'pdf'): ?>
								---
							<?php else: ?>
								<div class="btn" code="2" >Log</div>
							<?php endif ?>
						</td>
					<?php endif ?>
					<?php if ($user_level>=2): ?>
						<td>
							<a href="#" class="shj_rejudge"><i class="splashy-refresh"></i></a>
						</td>
					<?php endif ?>
					<td><?php
						if ($view=="final")
							echo $item['submit_count'];
						else
							echo $item['submit_number'];
						?>
					</td>
				</tr>
			<?php endforeach ?>
		</table>
		<p>
		<?php echo $this->pagination->create_links(); ?>
		</p>

	</div> <!-- main_content -->

</div> <!-- main_container -->

<div id="shj_modal" class="reveal-modal xlarge">
	<div class="modal_inside">
		<div style="text-align: center;">Loading<br><img src="<?php echo base_url('assets/images/loading.gif') ?>"/></div>
	</div>
	<a class="close-reveal-modal">&#215;</a>
</div>
