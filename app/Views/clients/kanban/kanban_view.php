<?php
// --------------------------------------------------------------------
// EDUNEXA PHASE 0: Learners Kanban partial
// Response shape: HTML partial (no layout wrapper)
// Columns are provided by controller (status / sessions).
// Cards are intentionally empty in Phase 0.
// --------------------------------------------------------------------

$board = isset($board) ? $board : "status";
$search = isset($search) ? $search : "";
$columns = isset($columns) ? $columns : array();
$cards_by_column = isset($cards_by_column) ? $cards_by_column : array();
?>

<div class="mb10 text-off">
    <i data-feather="layout" class="icon-16"></i>
    <?php echo $board === "sessions" ? "Sessions board" : "Status board"; ?>
    <?php if ($search) { ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <i data-feather="search" class="icon-16"></i>
        Search: <strong><?php echo esc($search); ?></strong>
    <?php } ?>
</div>

<div class="kanban-wrapper">
    <div class="clearfix" style="white-space: nowrap; overflow-x: auto; padding-bottom: 10px;">
        <?php foreach ($columns as $col) {
            $col_id = get_array_value($col, "id");
            $col_title = get_array_value($col, "title");
            $cards = get_array_value($cards_by_column, $col_id);
            $cards = is_array($cards) ? $cards : array();
            $count = count($cards);
            ?>

            <div class="card kanban-container" style="width: 320px; display: inline-block; vertical-align: top; margin-right: 10px;">
                <div class="card-header">
                    <span class="kanban-col-title fw-bold"><?php echo esc($col_title); ?></span>
                    <span class="badge bg-light text-dark float-end"><?php echo $count; ?></span>
                </div>
                <div class="kanban-card-body" style="min-height: 120px;">
                    <ul class="kanban-content" id="<?php echo esc($col_id); ?>">
                        <?php if ($count) { ?>
                            <?php foreach ($cards as $card) { ?>
                                <li class="kanban-card"><?php echo $card; ?></li>
                            <?php } ?>
                        <?php } else { ?>
                            <li class="kanban-card mt10 p10 text-off border rounded">
                                No learners yet.
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

        <?php } ?>
    </div>
</div>

<script>
    // Keep feather icons working inside ajax-loaded kanban partial
    feather.replace();
</script>
