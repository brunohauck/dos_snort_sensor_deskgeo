
<?php echo form_open('datum/add',array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="data_payload" class="col-md-4 control-label">Data Payload</label>
		<div class="col-md-8">
			<textarea name="data_payload" class="form-control" id="data_payload"><?php echo $this->input->post('data_payload'); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>

<?php echo form_close(); ?>