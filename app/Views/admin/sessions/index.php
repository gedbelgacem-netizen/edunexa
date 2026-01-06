<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="calendar" class="icon-16"></i> &nbsp;Sessions</h1>

        <?php if ($login_user->is_admin) { ?>
            <div class="title-button-group">
                <?php
                echo modal_anchor(get_uri("admin/session_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> Add session", array(
                    "class" => "btn btn-default",
                    "title" => "Add session",
                    "data-modal-lg" => "1"
                ));
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table id="edx-sessions-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();

        $("#edx-sessions-table").appTable({
            source: '<?php echo_uri("admin/sessions_list_data"); ?>',
            order: [[0, "desc"]],
            columns: [
                {title: "ID", "class": "w10p"},
                {title: "Learner ref"},
                {title: "Learner"},
                {title: "Course"},
                {title: "Start"},
                {title: "End"},
                {title: "Planned (min)"},
                {title: "Status"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w10p"}
            ]
        });
    });
</script>
