<?php
load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/locales-all.min.js"
));

$client = "";
if (isset($client_id)) {
    $client = $client_id;
}

$is_mobile = isset($is_mobile) ? $is_mobile : false;
$view_type = isset($view_type) ? $view_type : "";

$context = isset($context) ? $context : (isset($_GET['context']) ? $_GET['context'] : "");
$is_program_context = isset($is_program_context) ? (bool)$is_program_context : ($context === "program");
$is_read_only = isset($is_read_only) ? (bool)$is_read_only : false;
$learner_id = isset($learner_id) ? $learner_id : "";
$show_event_controls = !$is_read_only && !$is_program_context;
$show_mobile_event_controls = !$is_read_only;
?>
<div id="page-content<?php echo $client; ?>" class="page-wrapper<?php echo $client; ?> clearfix">
    <div class="card full-width-button">
        <?php if ($is_mobile) { ?>
            <div class="card-header">
                <span class="fw-bold mt-1 d-inline-block"><i data-feather="calendar" class="icon-16"></i> &nbsp;<?php echo app_lang("events"); ?></span>
                <div class="float-end">
                    <?php if ($show_mobile_event_controls) { ?>
                    <?php echo modal_anchor(get_uri("events/modal_form"), "<i data-feather='plus' class='icon-16'></i> " . app_lang('add_event'), array("class" => "mr5", "title" => app_lang('add_event'), "data-post-client_id" => $client)); ?>
                    <?php } ?>

                    <?php if ($view_type) { ?>
                        <?php echo view("clients/layout_settings_dropdown", array("view_type" => $view_type, "context" => "client_details_events")); ?>
                    <?php } ?>
                </div>
                <?php if ($is_read_only) { ?>
                    <div class="mt10 text-off"><i data-feather="lock" class="icon-16"></i> Read-only (sessions only)</div>
                <?php } ?>
            </div>
            <?php if ($show_mobile_event_controls && $calendar_filter_dropdown) { ?>
                <div id="calendar-filter-dropdown" class="float-start hide"></div>
            <?php } ?>
        <?php } else { ?>
            <div class="page-title clearfix">
                <h1><?php echo $is_program_context ? "Program Calendar" : app_lang('event_calendar'); ?></h1>
                <div class="title-button-group custom-toolbar events-title-button">
                    <?php
                    if ($view_type) {
                        echo view("clients/layout_settings_dropdown", array("view_type" => $view_type, "context" => "client_details_events"));
                    }
                    ?>

                    <?php if ($show_event_controls) { ?>

                    <?php
                    echo form_input(array(
                        "id" => "event-labels-dropdown",
                        "name" => "event-labels-dropdown",
                        "class" => "select2 w200 mr10 float-start mt15"
                    ));
                    ?>

                    <?php if ($calendar_filter_dropdown) { ?>
                        <div id="calendar-filter-dropdown" class="float-start <?php echo (count($calendar_filter_dropdown) == 1) ? "hide" : ""; ?>"></div>
                    <?php } ?>

                    <?php echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-default", "title" => app_lang('manage_labels'), "data-post-type" => "event")); ?>

                    <?php
                    if (get_setting("enable_google_calendar_api") && (get_setting("google_calendar_authorized") || get_setting('user_' . $login_user->id . '_google_calendar_authorized'))) {
                        echo modal_anchor(get_uri("events/google_calendar_settings_modal_form"), "<i data-feather='settings' class='icon-16'></i> " . app_lang('google_calendar_settings'), array("class" => "btn btn-default", "title" => app_lang('google_calendar_settings')));
                    }
                    ?>

                    <?php echo modal_anchor(get_uri("events/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_event'), array("class" => "btn btn-default add-btn", "title" => app_lang('add_event'), "data-post-client_id" => $client)); ?>

                    <?php } ?>
                    <?php if ($is_read_only) { ?>
                        <div class="mt15 text-off"><i data-feather="lock" class="icon-16"></i> Read-only (sessions only)</div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

<?php echo modal_anchor(get_uri("events/modal_form"), "", array("class" => "hide", "id" => "add_event_hidden", "title" => app_lang('add_event'), "data-post-client_id" => $client)); ?>
<?php echo modal_anchor(get_uri("events/view"), "", array("class" => "hide", "id" => "show_event_hidden", "data-post-client_id" => $client, "data-post-cycle" => "0", "data-post-editable" => $is_read_only ? "0" : "1", "title" => app_lang('event_details'))); ?>

<?php echo modal_anchor(get_uri("leaves/application_details"), "", array("class" => "hide", "data-post-id" => "", "id" => "show_leave_hidden")); ?>
<?php echo modal_anchor(get_uri("tasks/view"), "", array("class" => "hide", "data-post-id" => "", "id" => "show_task_hidden", "data-modal-lg" => "1")); ?>

        <div class="card-body <?php echo $is_mobile ? "calender-mobile-view" : ""; ?>">
            <div id="event-calendar"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var filterValues = "",
        eventLabel = "";

    var isProgramContext = <?php echo $is_program_context ? 'true' : 'false'; ?>;
    var isReadOnly = <?php echo $is_read_only ? 'true' : 'false'; ?>;
    var learnerId = "<?php echo esc($learner_id); ?>";

    var openCalendarItem = function(options) {
        if (!options) {
            return;
        }

        if (options.type === "leave") {
            if (!options.leaveId) {
                return;
            }

            $("#show_leave_hidden").attr("data-post-id", options.leaveId);
            $("#show_leave_hidden").trigger("click");
            return;
        }

        if (options.type === "task") {
            if (!options.taskId) {
                return;
            }

            $("#show_task_hidden").attr("data-post-id", options.taskId);
            $("#show_task_hidden").trigger("click");
            return;
        }

        if (options.type === "project" && options.url) {
            window.location = options.url;
            return;
        }

        var $eventModal = $("#show_event_hidden");
        $eventModal.removeAttr("data-action-url");

        if (options.actionUrl) {
            $eventModal.attr("data-action-url", options.actionUrl);
            $eventModal.attr("data-post-editable", "0");
            if (options.modalLg) {
                $eventModal.attr("data-modal-lg", "1");
            } else {
                $eventModal.removeAttr("data-modal-lg");
            }
            $eventModal.removeAttr("data-post-id");
            $eventModal.attr("data-post-cycle", "0");
            $eventModal.trigger("click");
            return;
        }

        if (!options.encryptedEventId) {
            return;
        }

        $eventModal.removeAttr("data-modal-lg");
        $eventModal.attr("data-post-id", options.encryptedEventId);
        $eventModal.attr("data-post-cycle", options.cycle || "0");
        $eventModal.attr("data-post-editable", options.editable ? "1" : "0");
        $eventModal.trigger("click");
    };

    var loadCalendar = function() {
        var filter_values = filterValues || "events",
            $eventCalendar = document.getElementById('event-calendar'),
            event_label = eventLabel || "0";

        appLoader.show();

        window.fullCalendar = new FullCalendar.Calendar($eventCalendar, {
            locale: AppLanugage.locale,
            height: isMobile() ? "auto" : $(window).height() - 210,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: (function(){
                if (isReadOnly) {
                    return {
                        url: "<?php echo_uri('events/calendar_events'); ?>",
                        method: 'GET',
                        extraParams: {
                            context: 'program',
                            learner_id: learnerId
                        }
                    };
                }

                return "<?php echo_uri('events/calendar_events/'); ?>" + filter_values + "/" + event_label + "/" + "<?php echo "/$client"; ?>";
            })(),
            dayMaxEvents: false,
            editable: !isReadOnly,
            eventStartEditable: !isReadOnly,
            eventDurationEditable: !isReadOnly,
            selectable: !isReadOnly,
            dateClick: function(date, jsEvent, view) {
                if (isReadOnly) {
                    return false;
                }

                $("#add_event_hidden").attr("data-post-start_date", moment(date.date).format("YYYY-MM-DD"));
                var startTime = moment(date.date).format("HH:mm:ss");
                if (startTime === "00:00:00") {
                    startTime = "";
                }
                $("#add_event_hidden").attr("data-post-start_time", startTime);
                var endDate = moment(date.date).add(1, 'hours');

                $("#add_event_hidden").attr("data-post-end_date", endDate.format("YYYY-MM-DD"));
                var endTime = "";
                if (startTime != "") {
                    endTime = endDate.format("HH:mm:ss");
                }

                $("#add_event_hidden").attr("data-post-end_time", endTime);
                $("#add_event_hidden").trigger("click");
            },
            eventClick: function(calEvent) {
                var eventData = calEvent.event.extendedProps || {};

                if (isReadOnly) {
                    var sessionId = calEvent.event.id || eventData.session_id || eventData.id;
                    if (!sessionId) {
                        // Nothing to open
                        return false;
                    }

                    var url = "<?php echo get_uri('events/view'); ?>" + "/" + sessionId + "?context=program";
                    if (learnerId) {
                        url += "&learner_id=" + encodeURIComponent(learnerId);
                    }

                    openCalendarItem({
                        actionUrl: url,
                        modalLg: true
                    });
                    return false;
                }

                if (eventData.event_type === "event") {
                    openCalendarItem({
                        encryptedEventId: eventData.encrypted_event_id,
                        cycle: eventData.cycle,
                        editable: !isReadOnly
                    });

                } else if (eventData.event_type === "leave") {
                    openCalendarItem({
                        type: "leave",
                        leaveId: eventData.leave_id
                    });

                } else if (eventData.event_type === "project_deadline" || eventData.event_type === "project_start_date") {
                    openCalendarItem({
                        type: "project",
                        url: "<?php echo site_url('projects/view'); ?>/" + eventData.project_id
                    });
                } else if (eventData.event_type === "task_deadline" || eventData.event_type === "task_start_date") {

                    openCalendarItem({
                        type: "task",
                        taskId: eventData.task_id
                    });
                }
            },
            eventContent: function(element) {
                var icon = element.event.extendedProps.icon;
                var title = element.event.title;
                if (icon) {
                    title = "<span class='clickable p5 w100p inline-block' style='background-color: " + element.event.backgroundColor + "; color: #fff'><span><i data-feather='" + icon + "' class='icon-16'></i> " + title + "</span></span>";
                }

                return {
                    html: title
                };
            },
            loading: function(state) {
                if (state === false) {
                    appLoader.hide();
                    $(".fc-prev-button").html("<i data-feather='chevron-left' class='icon-16'></i>");
                    $(".fc-next-button").html("<i data-feather='chevron-right' class='icon-16'></i>");
                    feather.replace();
                    setTimeout(function() {
                        feather.replace();
                    }, 100);
                }
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });

        window.fullCalendar.render();
    };

    $(document).ready(function() {
        if (isReadOnly) {
            loadCalendar();
            return;
        }

        $("#calendar-filter-dropdown").appMultiSelect({
            text: "<?php echo app_lang('event_type'); ?>",
            options: <?php echo json_encode($calendar_filter_dropdown); ?>,
            onChange: function(values) {
                filterValues = values.join('-');
                loadCalendar();
                setCookie("calendar_filters_of_user_<?php echo $login_user->id; ?>", values.join('-')); //save filters on browser cookie
            },
            onInit: function(values) {
                filterValues = values.join('-');
                loadCalendar();
            }
        });

        var client = "<?php echo $client; ?>";
        if (client) {
            setTimeout(function() {
                window.fullCalendar.today();
            });
        }

        //autoload the event popover
        var encrypted_event_id = "<?php echo isset($encrypted_event_id) ? $encrypted_event_id : ''; ?>";
        if (encrypted_event_id) {
            openCalendarItem({
                encryptedEventId: encrypted_event_id,
                cycle: "0",
                editable: !isReadOnly
            });
        }

        $("#event-labels-dropdown").select2({
            data: <?php echo $event_labels_dropdown; ?>
        }).on("change", function() {
            eventLabel = $(this).val();
            loadCalendar();
        });

        $("#event-calendar .fc-header-toolbar .fc-button").click(function() {
            feather.replace();
        });
    });
</script>
