
<?php echo form_open('reference/edit/'.$reference['ref_id'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="ref_system_id" class="col-md-4 control-label">Ref System Id</label>
		<div class="col-md-8">
			<input type="text" name="ref_system_id" value="<?php echo ($this->input->post('ref_system_id') ? $this->input->post('ref_system_id') : $reference['ref_system_id']); ?>" class="form-control" id="ref_system_id" />
		</div>
	</div>
	<div class="form-group">
		<label for="ref_tag" class="col-md-4 control-label">Ref Tag</label>
		<div class="col-md-8">
			<textarea name="ref_tag"><?php echo ($this->input->post('ref_tag') ? $this->input->post('ref_tag') : $reference['ref_tag']); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>