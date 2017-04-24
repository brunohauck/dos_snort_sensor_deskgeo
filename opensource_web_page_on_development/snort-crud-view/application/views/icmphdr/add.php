
<?php echo form_open('icmphdr/add',array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="icmp_type" class="col-md-4 control-label">Icmp Type</label>
		<div class="col-md-8">
			<input type="text" name="icmp_type" value="<?php echo $this->input->post('icmp_type'); ?>" class="form-control" id="icmp_type" />
		</div>
	</div>
	<div class="form-group">
		<label for="icmp_code" class="col-md-4 control-label">Icmp Code</label>
		<div class="col-md-8">
			<input type="text" name="icmp_code" value="<?php echo $this->input->post('icmp_code'); ?>" class="form-control" id="icmp_code" />
		</div>
	</div>
	<div class="form-group">
		<label for="icmp_csum" class="col-md-4 control-label">Icmp Csum</label>
		<div class="col-md-8">
			<input type="text" name="icmp_csum" value="<?php echo $this->input->post('icmp_csum'); ?>" class="form-control" id="icmp_csum" />
		</div>
	</div>
	<div class="form-group">
		<label for="icmp_id" class="col-md-4 control-label">Icmp Id</label>
		<div class="col-md-8">
			<input type="text" name="icmp_id" value="<?php echo $this->input->post('icmp_id'); ?>" class="form-control" id="icmp_id" />
		</div>
	</div>
	<div class="form-group">
		<label for="icmp_seq" class="col-md-4 control-label">Icmp Seq</label>
		<div class="col-md-8">
			<input type="text" name="icmp_seq" value="<?php echo $this->input->post('icmp_seq'); ?>" class="form-control" id="icmp_seq" />
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>

<?php echo form_close(); ?>