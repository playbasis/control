<div id="content" class="span10">
	<div class="box">
		<div class="heading">
			<h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
			<div class="buttons">
				<button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_enable'); ?></button>
			</div>
		</div><!-- .heading -->
		<div class="content">
			<?php if($this->session->flashdata('success')){ ?>
				<div class="content messages half-width">
					<div class="success"><?php echo $this->session->flashdata('success'); ?></div>
				</div>
			<?php }?>
			<?php if($this->session->flashdata('error')){ ?>
				<div class="content messages half-width">
					<div class="error"><?php echo $this->session->flashdata('error'); ?></div>
				</div>
			<?php }?>
			<div id="users">

<script type="text/javascript" src="<?php echo base_url();?>javascript/custom/jquery-1.7.2.min.js"></script>
<?php $attributes = array('id'=>'form');?>
<?php echo form_open('user/enable_users', $attributes);?>
<table id="user-list" class="list">
    <thead>
    <tr>
        <td width="7" class="left"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
        <td class="left">#</td>
        <td class="left"><?php echo $this->lang->line('column_title'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_username'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_email'); ?></td>
        <td class="left"><?php echo $this->lang->line('column_random_key'); ?></td>
        <td class="right"><?php echo $this->lang->line('column_date_added'); ?></td>
    </tr>
    </thead>
    <?php foreach ($users as $i => $user) { ?>
        <tbody id="user-row<?php echo $i; ?>">
        <tr>
            <td class="left"><input type="checkbox" name="selected[]" value="<?php echo $user['_id']; ?>" /></td>
            <td class="left"><?php echo $i+1; ?></td>
            <td class="left"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></td>
            <td class="left"><?php echo anchor('user/update/'.$user['_id'], $user['username']); ?></td>
            <td class="left"><?php echo $user['email']; ?></td>
            <td class="left"><?php echo $user['random_key']; ?></td>
            <td class="right"><?php echo date('j F Y g:i a', $user['date_added']->sec); ?></td>
        </tr>
        </tbody>
    <?php } ?>
    <tfoot>
    <tr>
        <td colspan="4"></td>
        <td class="left"></td>
    </tr>
    </tfoot>
</table>
<?php echo form_close();?>

			</div>

		</div><!-- .content -->
	</div><!-- .box -->
</div><!-- #content .span10 -->
