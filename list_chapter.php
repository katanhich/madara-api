<?php

include_once "chapter_handler.php";

$data = load_chapter();

ob_start();
?>

<?php if ( $data ) : ?>
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
<?php else : ?>
    <?php echo 'Novel has no chapter yet.'; ?>
<?php endif; ?>

<?php
// Get the buffered content and store it in a variable
$html = ob_get_clean();

// Return the HTML content to the browser
echo $html;
?>