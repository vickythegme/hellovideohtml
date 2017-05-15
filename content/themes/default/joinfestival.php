<?php include('includes/header.php'); ?>

<div class="container-fluid">
	<div class="container">
		<h3>Join Festival</h3>
		<h4 class="h4title">Submission Guidelines</h4>
		<p>From Pixar Animation Studios, “Inside Out” opens in UK cinemas on July 24, 2015.</p>
		<p>From an adventurous balloon ride above the clouds to a monster-filled metropolis, Academy Award®-winning director Pete Docter (“Monsters, Inc.,” “Up”) has taken audiences to unique and imaginative places. In Disney•Pixar’s original movie “ Inside Out,” he will take us to the most extraordinary location of all—inside the mind.</p>
		<p>Growing up can be a bumpy road, and it's no exception for Riley, who is uprooted from her Midwest life when her father starts a new job in San Francisco. Like all of us, Riley is guided by her emotions – Joy (Amy Poehler), Fear (Bill Hader), Anger (Lewis Black), Disgust (Mindy Kaling) and Sadness (Phyllis Smith). The emotions live in Headquarters, the control center inside Riley’s mind, where they help advise her through everyday life. As Riley and her emotions struggle to adjust to a new life in San Francisco, turmoil ensues in Headquarters. Although Joy, Riley's main and most important emotion, tries to keep things positive, the emotions conflict on how best to navigate a new city, house and school.</p>
	</div>
</div>
<div class="container-fluid">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center">
				if user is not logged in
				<div class="join_form">
					<button type="submit" class="btn btn-primary btn_festival">Login to Submit your Short Film</button>
				</div>
			</div>
			<div class="col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10">
				<p class="text-center">if user is logged in</p>
				<h2 class="h2title text-center">Submission Form</h2>
				<div class="join_form">
					<form class="form-horizontal">
						<div class="form-group">
							<label class="control-label col-sm-3">Short Film Name:</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" placeholder="Short Film Name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Director Name:</label>
							<div class="col-sm-9">          
								<input type="text" class="form-control" placeholder="Director Name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Contact Person Name:</label>
							<div class="col-sm-9">          
								<input type="text" class="form-control" placeholder="Director Name">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Mobile Number:</label>
							<div class="col-sm-9">          
								<input type="text" class="form-control" placeholder="Mobile Number">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Email ID:</label>
							<div class="col-sm-9">          
								<input type="email" class="form-control" placeholder="Email ID">
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-3 col-sm-9">
								<button type="submit" class="btn btn-primary btn_festival">Click to Join Festival and Pay Fee</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>