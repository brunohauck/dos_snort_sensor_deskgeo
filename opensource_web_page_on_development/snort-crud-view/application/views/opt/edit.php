
<?php echo form_open('opt/edit/'.$opt['sid'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="opt_proto" class="col-md-4 control-label">Opt Proto</label>
		<div class="col-md-8">
			<input type="text" name="opt_proto" value="<?php echo ($this->input->post('opt_proto') ? $this->input->post('opt_proto') : $opt['opt_proto']); ?>" class="form-control" id="opt_proto" />
		</div>
	</div>
	<div class="form-group">
		<label for="opt_code" class="col-md-4 control-label">Opt Code</label>
		<div class="col-md-8">
			<input type="text" name="opt_code" value="<?php echo ($this->input->post('opt_code') ? $this->input->post('opt_code') : $opt['opt_code']); ?>" class="form-control" id="opt_code" />
		</div>
	</div>
	<div class="form-group">
		<label for="opt_len" class="col-md-4 control-label">Opt Len</label>
		<div class="col-md-8">
			<input type="text" name="opt_len" value="<?php echo ($this->input->post('opt_len') ? $this->input->post('opt_len') : $opt['opt_len']); ?>" class="form-control" id="opt_len" />
		</div>
	</div>
	<div class="form-group">
		<label for="opt_data" class="col-md-4 control-label">Opt Data</label>
		<div class="col-md-8">
			<textarea name="opt_data"><?php echo ($this->input->post('opt_data') ? $this->input->post('opt_data') : $opt['opt_data']); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>