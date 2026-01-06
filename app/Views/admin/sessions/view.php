<?php
$session = isset($session_info) ? $session_info : null;
$logs_list = isset($logs) ? $logs : array();
$log_locked = isset($log_locked) ? $log_locked : false;
$log_lock_deadline = isset($log_lock_deadline) ? $log_lock_deadline : "";
$is_admin = $login_user->is_admin ? true : false;

$input_locked = (!$is_admin && $log_locked) ? true : false;
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
                        Learner: <strong><?php echo esc($session->learner_ref); ?></strong> —
                        <?php echo esc(trim($session->first_name . " " . $session->last_name)); ?>
                    </div>
                    <div class="text-off mt5">
                        Course: <?php echo esc($session->course_name); ?>
                    </div>
                    <div class="text-off mt5">
                        Start: <?php echo esc($session->start); ?> &nbsp; | &nbsp;
                        End: <?php echo esc($session->end); ?> &nbsp; | &nbsp;
                        Planned: <?php echo esc($session->planned_minutes); ?> min
                    </div>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-md-12">
                    <h5 class="mt0">Add log</h5>

                    <?php if (!$is_admin && $log_locked) { ?>
                        <div class="alert alert-warning">
                            This session is locked for logging (more than 24 hours after session start).
                            <?php if ($log_lock_deadline) { ?>
                                Deadline was: <strong><?php echo esc($log_lock_deadline); ?></strong>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php echo form_open(get_uri("admin/save_log"), array("id" => "edx-log-form", "class" => "general-form", "role" => "form")); ?>
                    <input type="hidden" name="id" id="log_id" value="" />
                    <input type="hidden" name="session_id" value="<?php echo esc($session->id); ?>" />

                    <?php
                    $status_attrs = array("id" => "status", "class" => "form-control");
                    $delivered_attrs = array("id" => "delivered_minutes", "name" => "delivered_minutes", "class" => "form-control", "type" => "number", "min" => 0, "step" => 1);
                    $note_attrs = array("id" => "note", "name" => "note", "class" => "form-control", "rows" => 2);

                    if ($input_locked) {
                        $status_attrs["disabled"] = "disabled";
                        $delivered_attrs["disabled"] = "disabled";
                        $note_attrs["disabled"] = "disabled";
                    }
                    ?>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <?php
                                $status_options = array(
                                    "held" => "Held",
                                    "cancelled" => "Cancelled",
                                    "no_show" => "No show"
                                );
                                echo form_dropdown("status", $status_options, "", $status_attrs);
                                ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="delivered_minutes">Delivered (min)</label>
                                <?php echo form_input($delivered_attrs); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="note">Note</label>
                                <?php echo form_textarea($note_attrs); ?>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix">
                        <button type="submit" class="btn btn-primary" <?php echo $input_locked ? "disabled" : ""; ?>>
                            <i data-feather="check-circle" class="icon-16"></i> Save log
                        </button>

                        <?php if ($is_admin) { ?>
                            <button type="button" id="cancel_log_edit" class="btn btn-default hide">
                                <i data-feather="x" class="icon-16"></i> Cancel edit
                            </button>
                        <?php } ?>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-md-12">
                    <h5 class="mt0">Log history</h5>

                    <?php if ($logs_list) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Created at</th>
                                        <th>By</th>
                                        <th>Status</th>
                                        <th>Delivered (min)</th>
                                        <th>Note</th>
                                        <?php if ($is_admin) { ?>
                                            <th class="text-end">Actions</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs_list as $log) { ?>
                                        <tr>
                                            <td><?php echo esc($log->created_at); ?></td>
                                            <td><?php echo esc($log->created_by_name); ?></td>
                                            <td><?php echo esc($log->status); ?></td>
                                            <td><?php echo esc($log->delivered_minutes); ?></td>
                                            <td><?php echo nl2br(esc($log->note)); ?></td>

                                            <?php if ($is_admin) { ?>
                                                <td class="text-end">
                                                    <?php
                                                    echo js_anchor("<i data-feather='edit' class='icon-16'></i>", array(
                                                        "title" => "Edit",
                                                        "class" => "btn btn-default btn-sm edx-edit-log",
                                                        "data-id" => $log->id,
                                                        "data-status" => $log->status,
                                                        "data-delivered_minutes" => $log->delivered_minutes,
                                                        "data-note" => $log->note
                                                    ));

                                                    echo " ";

                                                    echo js_anchor("<i data-feather='trash-2' class='icon-16'></i>", array(
                                                        "title" => "Delete",
                                                        "class" => "btn btn-danger btn-sm",
                                                        "data-action-url" => get_uri("admin/delete_log/" . $log->id),
                                                        "data-action" => "delete-confirmation"
                                                    ));
                                                    ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="text-off">No logs yet.</div>
                    <?php } ?>
                </div>
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

        $("#edx-log-form").appForm({
            onSuccess: function (result) {
                window.location.reload();
            }
        });

        $(document).on("click", ".edx-edit-log", function () {
            <?php if (!$is_admin) { ?>
                return false;
            <?php } ?>

            var $btn = $(this);

            $("#log_id").val($btn.attr("data-id"));
            $("#status").val($btn.attr("data-status"));
            $("#delivered_minutes").val($btn.attr("data-delivered_minutes"));
            $("#note").val($btn.attr("data-note"));

            $("#cancel_log_edit").removeClass("hide");
            $("html, body").animate({scrollTop: $("#edx-log-form").offset().top - 100}, 300);

            return false;
        });

        $("#cancel_log_edit").on("click", function () {
            $("#log_id").val("");
            $("#delivered_minutes").val("");
            $("#note").val("");

            $(this).addClass("hide");
            return false;
        });
    });
</script>
