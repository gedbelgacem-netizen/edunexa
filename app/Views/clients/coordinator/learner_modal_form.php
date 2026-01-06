<?php echo form_open(get_uri("clients/save_learner"), array("id" => "edx-learner-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">

        <?php if (!isset($courses_dropdown) || !count($courses_dropdown)) { ?>
            <div class="alert alert-warning">
                No courses found. Please add at least one course in <strong>edx_courses</strong> before creating learners.
            </div>
        <?php } ?>

        <div class="form-group">
            <div class="row">
                <label for="first_name" class="col-md-3">First name</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "first_name",
                        "name" => "first_name",
                        "class" => "form-control",
                        "placeholder" => "First name",
                        "autofocus" => true,
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="last_name" class="col-md-3">Last name</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "last_name",
                        "name" => "last_name",
                        "class" => "form-control",
                        "placeholder" => "Last name",
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="email" class="col-md-3">Email</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "email",
                        "name" => "email",
                        "class" => "form-control",
                        "placeholder" => "Email",
                        "type" => "email"
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="phone" class="col-md-3">Phone</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "phone",
                        "name" => "phone",
                        "class" => "form-control",
                        "placeholder" => "Phone"
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="course_id" class="col-md-3">Course</label>
                <div class="col-md-9">
                    <?php
                    $course_options = array("" => "-");
                    if (isset($courses_dropdown) && is_array($courses_dropdown)) {
                        foreach ($courses_dropdown as $k => $v) {
                            $course_options[$k] = $v;
                        }
                    }

                    echo form_dropdown("course_id", $course_options, "", array(
                        "id" => "course_id",
                        "class" => "form-control",
                        "required" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> Close</button>
    <button type="submit" class="btn btn-primary" <?php echo (!isset($courses_dropdown) || !count($courses_dropdown)) ? "disabled" : ""; ?>><i data-feather="check-circle" class="icon-16"></i> Create</button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();

        $("#edx-learner-form").appForm({
            onSuccess: function (result) {
                // simplest: reload the page to show the new learner in the list
                window.location.reload();
            }
        });

        setTimeout(function () {
            $("#first_name").focus();
        }, 200);
    });
</script>
