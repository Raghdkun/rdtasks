'use strict';

$(document).ready(function () {

    $(document).on('click', '.reports__department-row-title', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().parent().find('.collapse-row').slideToggle();
    });
    $(document).on('click', '.reports__client-row-title', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__client-container').slideToggle();
    });
    $(document).on('click', '.reports__project-header', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__project-container').slideToggle();
    });
    $(document).on('click', '.reports__developer-header', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__task-container').slideToggle();
    });
});
