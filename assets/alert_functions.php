<?php

function displayAlert($type = 'info', $message = '', $icon = null, $dismissible = true, $strongText = null, $autoHide = true, $autoHideDelay = 5000) {
    $icon = $icon ?? ($type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle'));
    $alertClass = 'alert-' . ($type === 'error' ? 'danger' : $type);
    $dismissButton = $dismissible ? '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '';
    $strongText = $strongText ?? ucfirst($type) . '!';
    $autoHideScript = $autoHide ? "<script>setTimeout(function() { $('.alert').alert('close'); }, $autoHideDelay);</script>" : '';
    ?>
    <div class="notification-toast alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-<?php echo $icon; ?> notification-icon"></i>
            <div>
                <strong><?php echo $strongText; ?></strong>
                <div><?php echo $message; ?></div>
            </div>
        </div>
        <?php echo $dismissButton; ?>
    </div>
    <?php echo $autoHideScript; ?>
    <?php
}
?>