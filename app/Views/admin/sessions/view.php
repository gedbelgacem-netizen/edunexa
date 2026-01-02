<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h4 class="mt0">Session #<?php echo esc($id); ?> (stub)</h4>
                <p class="text-off mb0">Phase 0 placeholder modal. This will be replaced with session detail UI and data wiring in later phases.</p>
            </div>

            <div class="col-md-12 mt15">
                <div class="border rounded p15">
                    <strong>Coordinator Notes (placeholder)</strong>
                    <div class="text-off mt5">No notes yet.</div>
                </div>
            </div>

            <div class="col-md-12 mt15">
                <div class="border rounded p15">
                    <strong>Logs (placeholder)</strong>
                    <div class="text-off mt5">No logs yet.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?php
    echo ajax_anchor(get_uri("admin/save_log"), "<i data-feather='save' class='icon-16'></i> Save log (stub)", array(
        "class" => "btn btn-primary",
        "data-show-response" => "1"
    ));
    ?>
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
</div>
