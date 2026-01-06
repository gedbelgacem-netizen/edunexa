<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><i data-feather="inbox" class="icon-16"></i> &nbsp; Intake requests</h1>
        </div>

        <?php if (!$login_user->is_admin) { ?>
            <div class="p15">
                <div class="alert alert-warning">
                    Only admins can view and approve intake requests.
                </div>
            </div>
        <?php } else { ?>
            <div class="table-responsive">
                <table id="edx-intake-table" class="display" cellspacing="0" width="100%"></table>
            </div>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();

        <?php if ($login_user->is_admin) { ?>
            $("#edx-intake-table").appTable({
                source: '<?php echo_uri("admin/intake_list_data"); ?>',
                order: [[0, "desc"]],
                columns: [
                    {title: "ID", "class": "w10p"},
                    {title: "Learner ref"},
                    {title: "Learner"},
                    {title: "Course"},
                    {title: "Learner status"},
                    {title: "Intake status"},
                    {title: "Requested at"},
                    {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w10p"}
                ]
            });
        <?php } ?>
    });
</script>
