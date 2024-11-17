<!-- Modal Confirm -->
<div class="modal fade" id="ModalConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal Confirm</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Modal Body
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-confirm btn-danger" onclick="_modal_confirm(this)" data-site_name="" data-phone="">Confirm</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<script>
    window.Modal = {
        ConfirmCallback: null,
        Confirm: (title, body, callback, confirm_label = null) => {
            let modal = $('#ModalConfirm');

            modal.find('.modal-title').html(title);
            modal.find('.modal-body').html(body);
            const default_confirm_label = 'Confirm';
            modal.find('.btn-confirm').html(confirm_label? confirm_label: 'Confirm');

            Modal.ConfirmCallback = callback;

            modal.modal('show');
        },
        Close: () => {
            let modal = $('#ModalConfirm');
            modal.modal('hide');
        }
    };

    function _modal_confirm(){
        if(Modal.ConfirmCallback){
            Modal.ConfirmCallback();
            Modal.ConfirmCallback = null;
        }
    }
</script>