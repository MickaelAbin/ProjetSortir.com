const konamiCode = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];
let konamiCodePosition = 0;

$(document).keydown(function(e) {
    if (e.keyCode === konamiCode[konamiCodePosition++]) {
        if (konamiCodePosition === konamiCode.length) {
            $('body').css('font-family', 'my-custom-font');
            konamiCodePosition = 0;
        }
    } else {
        konamiCodePosition = 0;
    }
});