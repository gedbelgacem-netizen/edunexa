<?php
// --------------------------------------------------------------------
// EDUNEXA PHASE 0: Learner sessions calendar tab (read-only)
// Uses the same FullCalendar assets as Events module.
// Feed: /events/calendar_events?context=program&learner_id={id}
// --------------------------------------------------------------------

load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/locales-all.min.js"
));

$learner_id = isset($learner_id) ? $learner_id : "";
$calendar_dom_id = "learner-sessions-calendar-" . $learner_id;
$modal_dom_id = "show_program_session_hidden_" . $learner_id;
?>

<div class="alert alert-warning mb15">
    <strong>Read-only (sessions only)</strong>
</div>

<?php echo modal_anchor(get_uri("events/view"), "", array("class" => "hide", "id" => $modal_dom_id, "data-modal-lg" => "1", "title" => "Session details")); ?>

<div id="<?php echo esc($calendar_dom_id); ?>"></div>

<script type="text/javascript">
    (function() {
        var calendarEl = document.getElementById("<?php echo esc($calendar_dom_id); ?>");
        if (!calendarEl) {
            return;
        }

        var learnerId = "<?php echo esc($learner_id); ?>";
        var sessionModal = "#<?php echo esc($modal_dom_id); ?>";

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: AppLanugage.locale,
            height: isMobile() ? "auto" : 650,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: {
                url: "<?php echo_uri('events/calendar_events'); ?>",
                method: 'GET',
                extraParams: {
                    context: 'program',
                    learner_id: learnerId
                }
            },
            dateClick: function() {
                appAlert.warning("Read-only (sessions only)", {duration: 5000});
                return false;
            },
            eventClick: function(info) {
                var sessionId = info.event.id || info.event.extendedProps.session_id || info.event.extendedProps.id;
                if (!sessionId) {
                    return false;
                }

                var url = "<?php echo get_uri('events/view'); ?>" + "/" + sessionId + "?context=program&learner_id=" + encodeURIComponent(learnerId);
                $(sessionModal).attr("data-action-url", url);
                $(sessionModal).trigger("click");
                return false;
            },
            loading: function(state) {
                if (state === false) {
                    feather.replace();
                }
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });

        calendar.render();
        feather.replace();
    })();
</script>
