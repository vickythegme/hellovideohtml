<?php include('content/themes/' . THEME . '/includes/header.php'); ?>
<link rel="stylesheet" type="text/css" href="content/themes/<?= THEME ?>/plugins/series/style.css">
<div class="container">
	<h3><?= $title ?> <span><?= $description ?></span></h3>
</div>

<div class="container">
	
	<div class="row series">
		<?php foreach($series as $serie): ?>
			<?php if($image_type == 'wide'): ?>
				<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
				<?php $series_image = $serie->image_normal; ?>
			<?php else: ?>
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-4">
				<?php $series_image = $serie->cover_image; ?>
			<?php endif; ?>
				<article class="block">
					<a class="block-thumbnail" href="<?= URL::to('series') . '/' . $serie->slug ?>">
						<img src="<?= ImageHandler::getImage($series_image)  ?>">
						<div class="details">
							<h2><?= $serie->title; ?></h2>
							<span><?= TimeHelper::convert_seconds_to_HMS($serie->duration); ?></span>
						</div>
					</a>
				</article>
			</div>
		<?php endforeach; ?>
	</div>

</div>

<div style="height:25px; display:block; width:100%"></div>

<?php include('content/themes/' . THEME . '/includes/footer.php'); ?>