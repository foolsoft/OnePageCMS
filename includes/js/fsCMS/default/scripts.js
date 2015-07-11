$(document).ready(function() {
    $('.fancybox').fancybox();
    $('li.menu-item a[href="' + window.location.href +'"]').parent().addClass('active');
});