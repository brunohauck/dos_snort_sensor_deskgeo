
<?php echo form_open('encoding/edit/'.$encoding['encoding_type'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="encoding_text" class="col-md-4 control-label">Encoding Text</label>
		<div class="col-md-8">
			<textarea name="encoding_text"><?php echo ($this->input->post('encoding_text') ? $this->input->post('encoding_text') : $encoding['encoding_text']); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>