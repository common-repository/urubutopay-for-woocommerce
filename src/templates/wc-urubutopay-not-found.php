<?php
if (!defined('ABSPATH')) {
    exit;
}

function upgfc_not_found_func($message = null)
{
?>
    <div style="font-size: 20px; position: relative"><?php echo null === $message ? 'Not found' : esc_html($message); ?></div>
<?php
}
