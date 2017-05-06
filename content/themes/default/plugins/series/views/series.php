<?php include('content/themes/' . THEME . '/includes/header.php'); ?>
<link rel="stylesheet" type="text/css" href="/content/themes/<?= THEME ?>/plugins/series/style.css">
<?php
	
	$plugin_data = (object) array_build(PluginData::where('plugin_slug', 'series')->get(), function($key, $data) {
        return array($data->key, $data->value);
    });

    $series_url = (isset($plugin_data->series_url) && $plugin_data->series_url != "") ? $plugin_data->series_url : 'series';
?>

<style type="text/css">
.block .block-contents{
    max-height: 120px;
    min-height: 120px;
    padding:20px 0px;
}

.block .block-contents p.date{
	margin-top:-7px;
}
</style>

<?php if($series->image != 'placeholder.jpg'): ?>
	<div class="sectionHeader" style="background:url(<?= ImageHandler::getImage($series->image)  ?>); background-size:cover; min-height:200px; position:relative; "></div>
<?php else: ?>
	<div style="width:100%; height:25px; display:block"></div>
<?php endif; ?>

<div class="container">
	
	<div class="row">
		
		<?php foreach($episodes as $episode): ?>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<article class="block">

				<?php 

					if($series->youtube == 1):
						if(isset($episode->contentDetails)):
						$videoId = $episode->contentDetails->videoId;
					  else:
					  	$videoId = $episode->id->videoId;
					  endif;

					  $episode_link = URL::to($series_url) . '/' . $series->slug . '/episode/' . $videoId . '/' . $page_token;
					  $image = (isset($episode->snippet->thumbnails->maxres)) ? $episode->snippet->thumbnails->maxres->url : $episode->snippet->thumbnails->medium->url;
					  $title = $episode->snippet->title;
					  $description = $episode->snippet->description;

					else:
						$image = ImageHandler::getImage($episode->image, 'medium');
						$episode_link = URL::to($series_url) . '/' . $series->slug . '/episode/' . $episode->slug;
						$title = $episode->title;
						$description = $episode->description;
					endif;
				?>

				<a class="block-thumbnail" href="<?= $episode_link ?>">
					<div class="thumbnail-overlay"></div>
					<span class="play-button"></span>
					<img src="<?= @$image ?>">
				</a>

				<div class="block-contents">

					<?php if($series->youtube != 1): ?>
					<p class="date"><?= date("F jS, Y", strtotime($episode->created_at)); ?>
						<?php if($episode->access == 'guest'): ?>
							<span class="label label-info">Free</span>
						<?php elseif($episode->access == 'subscriber'): ?>
							<span class="label label-success">Subscribers Only</span>
						<?php elseif($episode->access == 'registered'): ?>
		    				<span class="label label-warning">Registered Users</span>
						<?php endif; ?>
					</p>
					<?php endif; ?>
					<strong style="margin-bottom:10px;"><?= $title; ?></strong>
					<p class="desc"><?php if(strlen($description) > 90){ echo substr($description, 0, 90) . '...'; } else { echo $description; } ?></p>
				</div>
			</article>
		</div>
		<?php endforeach; ?>
	
	</div>

</div>

<div class="container">
	<div class="pagination_container">
		<?php if($series->youtube == 1): ?>
			<ul class="pager">
				<li <?php if(!isset($series_data->prevPageToken)): ?>class="disabled"<?php endif; ?>><a <?php if(isset($series_data->prevPageToken)): ?>href="<?= URL::to($series_url) . '/' . $series->slug . '/' . (intval($page) - 1) . '/' . $series_data->prevPageToken; ?>"<?php endif; ?>>« Previous</a></li>
				<li <?php if(!isset($series_data->nextPageToken)): ?>class="disabled"<?php endif; ?>><a <?php if(isset($series_data->nextPageToken)): ?>href="<?= URL::to($series_url) . '/' . $series->slug . '/' . (intval($page) + 1) . '/' . $series_data->nextPageToken; ?>"<?php endif; ?>>Next »</a></li>
			</ul>
		<?php else: ?>
			<?= $episodes->render() ?>
		<?php endif; ?>
	</div>
</div>

<?php include('content/themes/' . THEME . '/includes/footer.php'); ?>