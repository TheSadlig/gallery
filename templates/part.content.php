<?php
/**
 * @type $_ array
 */
/**
 * @type $l OC_L10N
 */
script(
	$_['appName'],
	[
		'gallery',
		'galleryconfig',
		'galleryinfobox',
		'galleryview',
		'breadcrumb',
		'album',
		'thumbnail',
		'vendor/eventsource-polyfill/dist/eventsource.min',
		'eventsource',
		'vendor/marked/marked.min',
		'vendor/bigshot/bigshot',
		'slideshow',
		'slideshowcontrols',
		'slideshowzoomablepreview'
	]
);
style(
	$_['appName'],
	[
		'styles',
		'mobile',
		'gallerybutton',
		'github-markdown',
		'slideshow'
	]
);
?>
<div id="controls">
	<div id='breadcrumbs'></div>
	<!-- toggle for opening shared picture view as file list -->
	<div id="openAsFileListButton" class="button">
		<img class="svg" src="<?php print_unescaped(
			image_path('core', 'actions/toggle-filelist.svg')
		); ?>" alt="<?php p($l->t('File list')); ?>"/>
	</div>
	<div id="sort-name-button" class="button left-sort-button">
		<img class="svg" src="<?php print_unescaped(
			image_path($_['appName'], 'nameasc.svg')
		); ?>" alt="<?php p($l->t('Sort by name')); ?>"/>
	</div>
	<div id="sort-date-button" class="button right-sort-button">
		<img class="svg" src="<?php print_unescaped(
			image_path($_['appName'], 'dateasc.svg')
		); ?>" alt="<?php p($l->t('Sort by date')); ?>"/>
	</div>
	<span class="right">
		<div id="album-info-button" class="button">
			<span class="ribbon black"></span>
			<img class="svg" src="<?php print_unescaped(
				image_path('core', 'actions/info.svg')
			); ?>" alt="<?php p($l->t('Album information')); ?>"/>
		</div>
		<div class="album-info-content markdown-body"></div>
	</span>
	<span class="right">
		<div id="share-button" class="button">
			<img class="svg" src="<?php print_unescaped(
				image_path('core', 'actions/share.svg')
			); ?>" alt="<?php p($l->t("Share")); ?>"/>
		</div>
		<a class="share" data-item-type="folder" data-item=""
		   title="<?php p($l->t("Share")); ?>"
		   data-possible-permissions="31"></a>
	</span>
</div>
<div id="gallery" class="hascontrols"></div>
<div id="emptycontent" class="hidden"><?php p(
		$l->t(
			"No pictures found! If you upload pictures in the files app, they will be displayed here."
		)
	); ?></div>
<input type="hidden" name="allowShareWithLink" id="allowShareWithLink" value="yes"/>