jQuery(document).ready(function($) {
    $('.announcement-bar').hover(
        function() {
            $(this).find('.marquee').css('animation-play-state', 'paused');
        },
        function() {
            $(this).find('.marquee').css('animation-play-state', 'running');
        }
    );

    $('.ab-close-button').on('click', function() {
        $(this).closest('.announcement-bar').fadeOut(300, function() {
            $(this).remove();
        });
    });

    const announcementBar = $('.announcement-bar');
    const timer = announcementBar.data('timer');

    if (timer > 0) {
        setTimeout(function() {
            announcementBar.fadeOut(300, function() {
                $(this).remove();
            });
        }, timer * 1000);
    }
});