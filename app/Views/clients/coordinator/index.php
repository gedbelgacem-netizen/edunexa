<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="users" class="icon-16"></i> &nbsp;Learners</h1>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("clients/learner_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> Add learner", array("class" => "btn btn-default", "title" => "Add learner")); ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <ul id="learners-tabs" class="nav nav-tabs" role="tablist">
                <li><a role="presentation" href="#learners-list" data-bs-toggle="tab" class="active">List</a></li>
                <li><a role="presentation" href="#l<div role="tabpanel" class="tab-pane fade show active" id="learners-list">
                    <?php $search_value = get_array_value($_GET, "search"); ?>

                    <?php if (!isset($courses_dropdown) || !count($courses_dropdown)) { ?>
                        <div class="alert alert-warning mt15">
                            No courses found. Please add at least one course in <strong>edx_courses</strong>.
                        </div>
                    <?php } ?>

                    <div class="mt15 mb15">
                        <form action="" method="GET" class="clearfix">
                            <div class="input-group">
                                <input type="text" name="search" value="<?php echo esc($search_value); ?>" class="form-control" placeholder="Search by ref or name">
                                <button class="btn btn-default" type="submit"><i data-feather="search" class="icon-16"></i></button>
                                <a class="btn btn-default" href="<?php echo get_uri("clients"); ?>"><i data-feather="x" class="icon-16"></i></a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Learner</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Intake</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($learners) && $learners) { ?>
                                    <?php foreach ($learners as $learner) { ?>
                                        <tr>
                                            <td><?php echo esc($learner->learner_ref); ?></td>
                                            <td><?php echo esc(trim($learner->first_name . " " . $learner->last_name)); ?></td>
                                            <td><?php echo esc($learner->course_name); ?></td>
                                            <td><?php echo esc($learner->status); ?></td>
                                            <td><?php echo esc($learner->latest_intake_status); ?></td>
                                            <td class="text-end">
                                                <a href="<?php echo get_uri("clients/view/" . $learner->id); ?>" class="btn btn-default btn-sm" title="View">
                                                    <i data-feather="eye" class="icon-16"></i>
                                                </a>
                                                <a href="<?php echo get_uri("clients/compact_view/" . $learner->id); ?>" class="btn btn-default btn-sm" title="Compact view">
                                                    <i data-feather="grid" class="icon-16"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No learners found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

er="maximize" class="icon-16"></i> Compact</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="learners-kanban-status">
                    <div class="mt15 mb15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="learners-kanban-search" type="text" class="form-control" placeholder="Search by learner name or learner ID">
                                    <button id="learners-kanban-search-btn" class="btn btn-default" type="button"><i data-feather="search" class="icon-16"></i></button>
                                </div>
                                <div class="text-off mt5">Read-only Kanban (Phase 0).</div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button id="learners-kanban-reload-status" class="btn btn-default" type="button"><i data-feather="refresh-cw" class="icon-16"></i> Reload</button>
                            </div>
                        </div>
                    </div>

                    <div id="load-learners-kanban-status"></div>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="learners-kanban-sessions">
                    <div class="mt15 mb15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="learners-kanban-search-2" type="text" class="form-control" placeholder="Search by learner name or learner ID">
                                    <button id="learners-kanban-search-btn-2" class="btn btn-default" type="button"><i data-feather="search" class="icon-16"></i></button>
                                </div>
                                <div class="text-off mt5">Read-only Kanban (Phase 0).</div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button id="learners-kanban-reload-sessions" class="btn btn-default" type="button"><i data-feather="refresh-cw" class="icon-16"></i> Reload</button>
                            </div>
                        </div>
                    </div>

                    <div id="load-learners-kanban-sessions"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function() {
        var kanbanEndpoint = "<?php echo get_uri('clients/all_clients_kanban_data'); ?>";

        var loadKanban = function(board, searchValue) {
            appLoader.show();
            appAjaxRequest({
                url: kanbanEndpoint,
                type: "POST",
                data: {
                    board: board,
                    search: searchValue || ""
                },
                success: function(response) {
                    if (board === "sessions") {
                        $("#load-learners-kanban-sessions").html(response);
                    } else {
                        $("#load-learners-kanban-status").html(response);
                    }
                    appLoader.hide();
                },
                error: function() {
                    appLoader.hide();
                }
            });
        };

        $(document).ready(function() {
            // Preload the first kanban board
            loadKanban("status", $("#learners-kanban-search").val());

            // Tab switch lazy-load
            $('a[href="#learners-kanban-status"]').on("shown.bs.tab", function() {
                loadKanban("status", $("#learners-kanban-search").val());
            });
            $('a[href="#learners-kanban-sessions"]').on("shown.bs.tab", function() {
                loadKanban("sessions", $("#learners-kanban-search-2").val());
            });

            // Search / reload actions
            $("#learners-kanban-search-btn, #learners-kanban-reload-status").on("click", function() {
                loadKanban("status", $("#learners-kanban-search").val());
            });
            $("#learners-kanban-search").on("keypress", function(e) {
                if (e.which === 13) {
                    loadKanban("status", $(this).val());
                }
            });

            $("#learners-kanban-search-btn-2, #learners-kanban-reload-sessions").on("click", function() {
                loadKanban("sessions", $("#learners-kanban-search-2").val());
            });
            $("#learners-kanban-search-2").on("keypress", function(e) {
                if (e.which === 13) {
                    loadKanban("sessions", $(this).val());
                }
            });
        });
    })();
</script>
