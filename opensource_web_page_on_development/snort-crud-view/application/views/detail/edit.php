
<?php echo form_open('detail/edit/'.$detail['detail_type'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="detail_text" class="col-md-4 control-label">Detail Text</label>
		<div class="col-md-8">
			<textarea name="detail_text"><?php echo ($this->input->post('detail_text') ? $this->input->post('detail_text') : $detail['detail_text']); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>