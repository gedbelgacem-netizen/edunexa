<div id="page-content" class="page-wrapper clearfix">
    <div class="page-title clearfix">
        <h1><i data-feather="users" class="icon-16"></i> &nbsp;Learners</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <ul id="learners-tabs" class="nav nav-tabs" role="tablist">
                <li><a role="presentation" href="#learners-list" data-bs-toggle="tab" class="active">List</a></li>
                <li><a role="presentation" href="#learners-kanban-status" data-bs-toggle="tab">Kanban: Status</a></li>
                <li><a role="presentation" href="#learners-kanban-sessions" data-bs-toggle="tab">Kanban: Sessions</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="learners-list">
                    <div class="alert alert-info mt15">
                        <strong>Phase 0:</strong> Learners list UI shell. Data wiring is not implemented yet.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Learner ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#1</td>
                                    <td>Sample Learner</td>
                                    <td><span class="badge bg-secondary">new</span></td>
                                    <td class="text-end">
                                        <a href="<?php echo get_uri('clients/view/1'); ?>" class="btn btn-default btn-sm"><i data-feather="eye" class="icon-16"></i> View</a>
                                        <a href="<?php echo get_uri('clients/compact_view/1'); ?>" class="btn btn-default btn-sm"><i data-feather="maximize" class="icon-16"></i> Compact</a>
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
