<?php

include_once "chapter_handler.php";

$data = load_chapter();

ob_start();
?>
    <div class="c-blog__heading style-2 font-heading">
        <h2 class="h4">
            <i class="icon ion-ios-star"></i>
            LATEST CHAPTERS RELEASES
        </h2>
        <a href="#" title="Change Order" class="btn-reverse-order"><i class="icon ion-md-swap"></i></a>
    </div>
    <div class="page-content-listing single-page">
        <div class="listing-chapters_wrap cols-1 show-more">
            <?php if ( $data ) : ?>
                <ul class="main version-chap no-volumn">
		            <?php foreach ($data as $chapter) {
			            $time_diff = get_time_diff( $chapter['date'] );
                    ?>
                        <li class="wp-manga-chapter">
                            <a href="<?php echo $chapter['link']; ?>">
					            <?php echo $chapter['full_name']; ?>
                            </a>
	                        <?php if ( $time_diff ) { ?>
                                <span class="chapter-release-date">
										<?php echo $time_diff; ?>
									</span>
	                        <?php } ?>
                        </li>
		            <?php } ?>
                </ul>
            <?php else : ?>
	            <?php echo 'Novel has no chapter yet.'; ?>
            <?php endif; ?>
            <div class="c-chapter-readmore">
				<span class="btn btn-link chapter-readmore">
					Show more
				</span>
            </div>
        </div>
    </div>
<?php
// Get the buffered content and store it in a variable
$html = ob_get_clean();

// Return the HTML content to the browser
echo $html;
?>