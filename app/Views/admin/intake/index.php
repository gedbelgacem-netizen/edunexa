<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="clipboard" class="icon-16"></i> &nbsp;Intake</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info mb15">
                <strong>Phase 0:</strong> Intake UI skeleton. No business logic is implemented in this phase.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="border rounded p15 mb15">
                        <h4 class="mt0">New intake (stub)</h4>
                        <p class="text-off">This area will host the intake queue and forms in later phases.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p15 mb15">
                        <h4 class="mt0">Recent submissions (stub)</h4>
                        <p class="text-off">Placeholder panel for recent intake submissions.</p>
                    </div>
                </div>
            </div>

            <div class="mt15">
                <?php
                echo ajax_anchor(get_uri("admin/save_log"), "<i data-feather='save' class='icon-16'></i> Save sample log", array(
                    "class" => "btn btn-primary",
                    "data-show-response" => "1",
                    "data-real-target" => "#admin-intake-save-response"
                ));
                ?>
                <div id="admin-intake-save-response" class="mt15"></div>
            </div>
        </div>
    </div>
</div>
