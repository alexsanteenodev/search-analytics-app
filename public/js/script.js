$(document).ready(function () {
 $(document).on('click', '.show-alert', function (e) {
     e.preventDefault();
     $('.section_alert .block').toggle();
 })
});