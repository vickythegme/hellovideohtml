@extends('admin.master')

@section('content')

	

	<div class="admin-section-title">
		<h3><i class="fa fa-plug"></i> Hello Example Plugin</h3>
	</div>

	<form method="POST" action="">

		<div class="panel panel-primary" data-collapsed="0"> <div class="panel-heading"> 
			<div class="panel-title">Plugin Settings</div> <div class="panel-options"> <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a> </div></div> 
			<div class="panel-body" style="display: block;"> 
				
				<p>Value 1:</p> 
				<input type="text" class="form-control" name="value1" value="@if(isset($value1)){{ $value1 }}@endif" />
				<br />

				<p>Value 2:</p> 
				<input type="text" class="form-control" name="value2" value="@if(isset($value2)){{ $value2 }}@endif" />
				<br />
			
			</div> 
		</div>
		
		<input type="submit" class="btn btn-success pull-right" value="Save Settings" />
		<div class="clear"></div>

	</form>


@stop