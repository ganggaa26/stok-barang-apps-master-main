//
// Sidebar menu js
//

'use strict';

function initjQuerySidebar() {
    // Cek apakah jQuery ($) sudah siap dimuat oleh browser
    if (window.jQuery) {

        (function () {
            var url = window.location + "";
            var path = url.replace(window.location.protocol + "//" + window.location.host + "/", "");
            var element = $('ul#sidebarnav a').filter(function () {
                return this.href === url || this.href === path;
            });
            
            element.parentsUntil(".sidebar-nav").each(function (index) {
                if ($(this).is("li") && $(this).children("a").length !== 0) {
                    $(this).children("a").addClass("active");
                    $(this).parent("ul#sidebarnav").length === 0 ?
                        $(this).addClass("active") :
                        $(this).addClass("active");
                } else if (!$(this).is("ul") && $(this).children("a").length === 0) {
                    $(this).addClass("active");
                } else if ($(this).is("ul")) {
                    $(this).addClass('in');
                }
            });
        })();

    } else {
        // Jika belum siap, tunggu 50 milidetik lalu coba jalankan ulang
        setTimeout(initjQuerySidebar, 50);
    }
}

// Jalankan fungsi pengecekan pertama kali
initjQuerySidebar();