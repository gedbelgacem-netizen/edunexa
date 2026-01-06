<?php
$session = isset($session_info) ? $session_info : null;
$logs_list = isset($logs) ? $logs : array();
?>

<div class="modal-body clearfix">
    <div class="container-fluid">
        <?php if (!$session) { ?>
            <div class="alert alert-warning">Session not found.</div>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="mt0 mb5">Session #<?php echo esc($session->id); ?></h4>
                    <div class="text-off">
                        <?php echo esc($session->course_name); ?>
                    </div>
                </div>

                <div class="col-md-12 mt15">
                    <div class="text-off">
                        <i data-feather="user" class="icon-16"></i>
                        Learner: <strong><?php echo esc($session->learner_ref); ?></strong> —
                        <?php echo esc(trim($session->first_name . " " . $session->last_name)); ?>
                    </div>
                </div>

                <div class="col-md-12 mt10">
                    <div class="text-off">
                        <i data-feather="clock" class="icon-16"></i>
                        Start: <?php echo esc($session->start); ?>
                    </div>
                </div>

                <div class="col-md-12 mt5">
                    <div class="text-off">
                        <i data-feather="clock" class="icon-16"></i>
                        End: <?php echo esc($session->end); ?>
                    </div>
                </div>

                <div class="col-md-12 mt5">
                    <div class="text-off">
                        <i data-feather="hash" class="icon-16"></i>
                        Planned minutes: <?php echo esc($session->planned_minutes); ?>
                    </div>
                </div>

                <div class="col-md-12 mt15">
                    <hr />
                    <h5 class="mt0">Log history</h5>

                    <?php if ($logs_list) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Created at</th>
                                        <th>Status</th>
                                        <th>Delivered (min)</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs_list as $log) { ?>
                                        <tr>
                                            <td><?php echo esc($log->created_at); ?></td>
                                            <td><?php echo esc($log->status); ?></td>
                                            <td><?php echo esc($log->delivered_minutes); ?></td>
                                            <td><?php echo nl2br(esc($log->note)); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="text-off">No logs yet.</div>
                    <?php } ?>
                </div>

                <?php if ($login_user->user_type === "staff") { ?>
                    <div class="col-md-12 mt15">
                        <a class="btn btn-default" href="<?php echo get_uri("admin/view_session/" . $session->id); ?>">
                            <i data-feather="external-link" class="icon-16"></i> Open session in Admin
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();
    });
</script>
