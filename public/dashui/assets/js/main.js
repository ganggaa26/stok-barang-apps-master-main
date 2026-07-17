//
// Main js
//

'use strict';

function initjQueryMain() {
    // Cek apakah jQuery ($) sudah dimuat oleh browser
    if (window.jQuery) {
        
        (function() {
            // Menu toggle for admin dashboard
            if ($("#nav-toggle").length) {
                $("#nav-toggle").on("click", function(e) {
                    e.preventDefault();
                    $("#db-wrapper").toggleClass("toggled");
                });
            }

            // slimscroll for sidebar nav
            if ($(".nav-scroller").length) {
                $(".nav-scroller").slimScroll({
                    height: "90%",
                });
            }

            // Notification dropdown scroll List
            if ($('.notification-list-scroll').length) {
                $(".notification-list-scroll").slimScroll({
                    height: 300,
                });
            }

            // Multi level menu dropdown
            if ($(".dropdown-menu a.dropdown-toggle").length) {
                $(".dropdown-menu a.dropdown-toggle").on("click", function (e) {
                    if (!$(this).next().hasClass("show")) {
                        $(this)
                            .parents(".dropdown-menu")
                            .first()
                            .find(".show")
                            .removeClass("show");
                    }
                    var $subMenu = $(this).next(".dropdown-menu");
                    $subMenu.toggleClass("show");

                    $(this)
                        .parents("li.nav-item.dropdown.show")
                        .on("hidden.bs.dropdown", function (e) {
                            $(".dropdown-submenu .show").removeClass("show");
                        });

                    return false;
                });
            }

            // Default Tooltip
            if ($('[data-bs-toggle="tooltip"]').length) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }

            // Default Popover
            if ($('[data-bs-toggle="popover"]').length) {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })
            }

            // Scrollspy
            if ($('[data-bs-spy="scroll"]').length) {
                var dataSpyList = [].slice.call(document.querySelectorAll('[data-bs-spy="scroll"]'))
                dataSpyList.forEach(function (dataSpyEl) {
                    bootstrap.ScrollSpy.getInstance(dataSpyEl).refresh()
                })
            }

            // Toast
            if ($('.toast').length) {
                var toastElList = [].slice.call(document.querySelectorAll('.toast'))
                var toastList = toastElList.map(function (toastEl) {
                    return new bootstrap.Toast(toastEl)
                })
            }

            // Perfomance Chart
            if ($("#perfomanceChart").length) {
                var options = {
                    series: [100, 78, 89],
                    chart: {
                        height: 320,
                        type: 'radialBar',
                    },
                    colors: ['#28a745', '#ffc107', '#dc3545'],
                    stroke: {
                        lineCap: "round",
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -168,
                            endAngle: -450,
                            hollow: {
                                size: '55%',
                            },
                            track: {
                                background: 'transparent',
                            },
                            dataLabels: {
                                show: false,
                            }
                        }
                    },
                };

                var chart = new ApexCharts(document.querySelector("#perfomanceChart"), options);
                chart.render();
            }

            // offcanvas
            if ($(".offcanvas").length) {
                var offcanvasElementList = [].slice.call(document.querySelectorAll('.offcanvas'))
                var offcanvasList = offcanvasElementList.map(function (offcanvasEl) {
                    return new bootstrap.Offcanvas(offcanvasEl)
                })
            }

            // Sidenav fixed for docs
            if ($(".sidebar-nav-fixed a").length) {
                $(".sidebar-nav-fixed a")
                    .on("click", function (event) {
                        if (
                            location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
                            location.hostname == this.hostname
                        ) {
                            var target = $(this.hash);
                            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                            if (target.length) {
                                event.preventDefault();
                                $('html, body').animate({
                                    scrollTop: target.offset().top - 90
                                }, 1000, function () {
                                    var $target = $(target);
                                    $target.focus();
                                    if ($target.is(":focus")) {
                                        return false;
                                    } else {
                                        $target.attr('tabindex', '-1');
                                        $target.focus();
                                    };
                                });
                            }
                        }
                        $('.sidebar-nav-fixed a').each(function () {
                            $(this).removeClass('active');
                        })
                        $(this).addClass('active');
                    });
            }

        })();

        // Sambungkan ke fungsi dropdown material pembantu jika ada di window global
        if (typeof window.updateItemDropdown === 'function') {
            window.actualUpdateItemDropdown = window.updateItemDropdown;
        }

    } else {
        // Jika jQuery belum siap, tunggu 50 milidetik lalu coba jalankan ulang fungsi ini
        setTimeout(initjQueryMain, 50);
    }
}

// Jalankan fungsi pengecekan pertama kali
initjQueryMain();