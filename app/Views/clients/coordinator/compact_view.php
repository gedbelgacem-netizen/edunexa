<?php
$learner_id = isset($learner_id) ? $learner_id : 0;
$tab = isset($tab) ? $tab : "";
$active_tab = $tab ? $tab : "overview";
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="user" class="icon-16"></i> &nbsp;Learner #<?php echo esc($learner_id); ?> (Compact)</h1>
        <div class="title-button-group">
            <a href="<?php echo get_uri('clients/view/' . $learner_id); ?>" class="btn btn-default">
                <i data-feather="arrow-left" class="icon-16"></i> Full view
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li>
                    <a role="presentation" href="#learner-compact-overview" data-bs-toggle="tab" class="<?php echo $active_tab === 'overview' ? 'active' : ''; ?>">
                        Overview
                    </a>
                </li>
                <li>
                    <a role="presentation" href="#learner-compact-calendar" data-bs-toggle="tab" class="<?php echo $active_tab === 'calendar' ? 'active' : ''; ?>">
                        Calendar
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show <?php echo $active_tab === 'overview' ? 'active' : ''; ?>" id="learner-compact-overview">
                    <div class="alert alert-info mt15">
                        <strong>Phase 0:</strong> Compact learner view shell.
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb15">
                                <div class="card-body">
                                    <h4 class="mt0">Notes</h4>
                                    <div class="text-off">Notes placeholder</div>

                                    <hr />

                                    <h4 class="mt0">Reminders</h4>
                                    <div class="text-off">Reminders placeholder</div>

                                    <hr />

                                    <h4 class="mt0">Coordinator comments history</h4>
                                    <div class="text-off">Coordinator comments history placeholder</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade show <?php echo $active_tab === 'calendar' ? 'active' : ''; ?>" id="learner-compact-calendar">
                    <div class="mt15">
                        <?php echo view("clients/coordinator/_sessions_calendar", array("learner_id" => $learner_id)); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    feather.replace();
</script>
