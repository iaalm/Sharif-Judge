<?php

/*
 * @file list_problems
 * @author iaalm <iaalmsimon@gmail.com>
 * @date Oct 31, 2013
 */

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>


<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/assignments.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">

		<?php foreach ($success_messages as $success_message): ?>
			<p class="shj_ok"><?php echo $success_message ?></p>
		<?php endforeach ?>
		<?php foreach ($error_messages as $error_message): ?>
			<p class="shj_error"><?php echo $error_message ?></p>
		<?php endforeach ?>

		<?php if (count($problems)==0): ?>
			<p style="text-align: center;">Nothing to show...</p>
		<?php endif ?>
		<?php foreach($problems as $item): ?>
			<div class="assignment_block" id="<?php echo $item['id'] ?>">
                            <!--
				<div class="c1">
					<div class="select_assignment <?php echo ($item['id']==$assignment['id']?'check checked':'check') ?> i<?php echo $item['id'] ?>" id="<?php echo $item['id'] ?>"></div>
				</div>
                            -->
				<div class="assignment_item">
                                    <div class="assignment_subitem"><a href="<?php echo site_url('submit/problem/'. $item['id']) ?>"><?php echo $item['name'] ?></a></div>
                                </div>
                        </div>
					
		<?php endforeach ?>

	</div> <!-- main_content -->

</div> <!-- main_container -->