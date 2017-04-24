
<?php echo form_open('event/edit/'.$event['sid'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="signature" class="col-md-4 control-label">Signature</label>
		<div class="col-md-8">
			<input type="text" name="signature" value="<?php echo ($this->input->post('signature') ? $this->input->post('signature') : $event['signature']); ?>" class="form-control" id="signature" />
		</div>
	</div>
	<div class="form-group">
		<label for="timestamp" class="col-md-4 control-label">Timestamp</label>
		<div class="col-md-8">
			<input type="text" name="timestamp" value="<?php echo ($this->input->post('timestamp') ? $this->input->post('timestamp') : $event['timestamp']); ?>" class="form-control" id="timestamp" />
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>