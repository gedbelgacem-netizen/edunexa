<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="calendar" class="icon-16"></i> &nbsp;Sessions</h1>
        <div class="title-button-group">
            <?php
            // Phase 0 stub: open a sample session modal
            echo modal_anchor(get_uri("admin/view_session/1"), "<i data-feather='eye' class='icon-16'></i> View sample session", array(
                "class" => "btn btn-default",
                "title" => "Session details",
                "data-modal-lg" => "1"
            ));
            ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info mb15">
                <strong>Phase 0:</strong> Sessions UI skeleton. Data wiring will arrive in later phases.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Session ID</th>
                            <th>Title</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Sample session (stub)</td>
                            <td>
                                <?php
                                echo modal_anchor(get_uri("admin/view_session/1"), "<i data-feather='eye' class='icon-16'></i> View", array(
                                    "title" => "Session details",
                                    "data-modal-lg" => "1",
                                    "class" => "btn btn-default btn-sm"
                                ));
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
