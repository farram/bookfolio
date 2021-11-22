$(document).ready(function () {

    $("#viewport-change-desktop").click(function () {
        $("iframe").width('100%');
        $("iframe").height('100%');
    });
    $("#viewport-change-laptop").click(function () {

        $("iframe").width(1024);
        $("iframe").height(768);
    });
    $("#viewport-change-tablet").click(function () {
        $("iframe").width(768);
        $("iframe").height(800);
    });
    $("#viewport-change-mobile").click(function () {
        $("iframe").width(320);
        $("iframe").height(568);
    });
});