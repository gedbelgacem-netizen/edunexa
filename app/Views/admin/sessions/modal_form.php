<?php
$session_id = isset($session_info) && $session_info ? $session_info->id : "";
$learner_id = isset($session_info) && $session_info ? $session_info->learner_id : "";
$start_value = "";
$end_value = "";

if (isset($session_info) && $session_info && $session_info->start) {
    $start_value = date("Y-m-d\TH:i", strtotime($session_info->start));
}

if (isset($session_info) && $session_info && $session_info->end) {
    $end_value = date("Y-m-d\TH:i", strtotime($session_info->end));
}
?>

<?php echo form_open(get_uri("admin/save_session"), array("id" => "edx-session-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">

        <input type="hidden" name="id" value="<?php echo $session_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="learner_id" class="col-md-3">Learner</label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("learner_id", isset($learners_dropdown) ? $learners_dropdown : array("" => "-"), $learner_id, array(
                        "id" => "learner_id",
                        "class" => "form-control",
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="start" class="col-md-3">Start</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "start",
                        "name" => "start",
                        "value" => $start_value,
                        "class" => "form-control",
                        "type" => "datetime-local",
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="end" class="col-md-3">End</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "end",
                        "name" => "end",
                        "value" => $end_value,
                        "class" => "form-control",
                        "type" => "datetime-local",
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="text-muted">
            Planned minutes will be calculated automatically from (end - start).
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
    <button type="submit" class="btn btn-primary"><i data-feather="check-circle" class="icon-16"></i> Save</button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();

        $("#edx-session-form").appForm({
            onSuccess: function (result) {
                $("#edx-sessions-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        setTimeout(function () {
            $("#learner_id").focus();
        }, 200);
    });
</script>
