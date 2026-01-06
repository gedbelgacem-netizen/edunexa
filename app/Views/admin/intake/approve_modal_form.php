<?php echo form_open(get_uri("admin/intake_approve"), array("id" => "edx-intake-approve-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $intake_info->id; ?>" />

        <div class="alert alert-info">
            Approving intake for:
            <strong>
                <?php echo $learner_info ? esc(trim($learner_info->learner_ref . " - " . $learner_info->first_name . " " . $learner_info->last_name)) : ""; ?>
            </strong>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="planned_minutes_total" class="col-md-4">Planned minutes total</label>
                <div class="col-md-8">
                    <?php
                    echo form_input(array(
                        "id" => "planned_minutes_total",
                        "name" => "planned_minutes_total",
                        "value" => $learner_info ? $learner_info->planned_minutes_total : "",
                        "class" => "form-control",
                        "placeholder" => "e.g. 600",
                        "type" => "number",
                        "min" => 1,
                        "step" => 1,
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="trainer_id" class="col-md-4">Trainer (optional)</label>
                <div class="col-md-8">
                    <?php
                    $trainer_options = array("" => "-");
                    if (isset($trainers_dropdown) && is_array($trainers_dropdown)) {
                        foreach ($trainers_dropdown as $k => $v) {
                            $trainer_options[$k] = $v;
                        }
                    }

                    echo form_dropdown("trainer_id", $trainer_options, $learner_info ? $learner_info->trainer_id : "", array(
                        "id" => "trainer_id",
                        "class" => "form-control"
                    ));
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
    <button type="submit" class="btn btn-primary"><i data-feather="check-circle" class="icon-16"></i> Approve</button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();

        $("#edx-intake-approve-form").appForm({
            onSuccess: function (result) {
                $("#edx-intake-table").appTable({reload: true});
            }
        });

        setTimeout(function () {
            $("#planned_minutes_total").focus();
        }, 200);
    });
</script>
