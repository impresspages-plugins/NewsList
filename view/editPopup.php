<?php
/**
 * This is an HTML of widget management pupup. Please look at AdminController.php to see how $form variable is created.
 * Feel free to modify this file, but leave intact ID attributes and classes with 'ips' prefix.
 *
 */
?>
<div class="ip" id="ipNewsListPopup">
    <div class="modal fade ipsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Plugin settings', 'NewsList'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $form->render() ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'NewsList'); ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php _e('Confirm', 'NewsList'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
