<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h4 class="mt0">Session #<?php echo esc($id); ?> (read-only)</h4>
                <p class="text-off mb0">Program calendar session details are read-only in Phase 0. Data wiring will be implemented in later phases.</p>
            </div>

            <?php if(isset($learner_id) && $learner_id) { ?>
                <div class="col-md-12 mt15">
                    <div class="text-off">
                        <i data-feather="user" class="icon-16"></i>
                        Learner ID: <?php echo esc($learner_id); ?>
                    </div>
                </div>
            <?php } ?>

            <div class="col-md-12 mt15">
                <div class="border rounded p15">
                    <strong>Notes (placeholder)</strong>
                    <div class="text-off mt5">No notes yet.</div>
                </div>
            </div>

            <div class="col-md-12 mt15">
                <div class="border rounded p15">
                    <strong>Coordinator comments (placeholder)</strong>
                    <div class="text-off mt5">No comments yet.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
</div>
