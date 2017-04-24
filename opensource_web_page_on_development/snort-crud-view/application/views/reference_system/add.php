
<?php echo form_open('reference_system/add',array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="ref_system_name" class="col-md-4 control-label">Ref System Name</label>
		<div class="col-md-8">
			<input type="text" name="ref_system_name" value="<?php echo $this->input->post('ref_system_name'); ?>" class="form-control" id="ref_system_name" />
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>

<?php echo form_close(); ?>