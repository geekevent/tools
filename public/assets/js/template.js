(function () {
    let eventEnded = true;
    let navBarHeight = $('#nav-bar').height()
    if (undefined === navBarHeight) {
        navBarHeight = 0;
    }
    let windowHeight = window.innerHeight;
    let sideBar = $('#side-nav');
    let mainContent = $('#main-content');
    console.log((parseInt(windowHeight) - parseInt(navBarHeight)));
    $('.full-height').height((parseInt(windowHeight) - parseInt(navBarHeight))+'px');
    $('.inner-height').height('100%');

    $('#side-nav-control').on('click', function () {
        if (!eventEnded) {
            return;
        }
        eventEnded = false;
        if (sideBar.hasClass('displayed')) {
            sideBar
                .addClass('slideOutLeft')
                .removeClass('displayed')
                .removeClass('slideInLeft')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    sideBar
                        .addClass('d-none')
                    mainContent
                        .removeClass('col-md-10')
                        .addClass('col-12');
                        eventEnded = true;
                })
            ;
        } else {
            mainContent
                .removeClass('col-12')
                .addClass('col-md-10');
            sideBar
                .addClass('slideInLeft')
                .addClass('displayed')
                .removeClass('slideOutLeft')
                .removeClass('d-none')
                .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    eventEnded = true;
                })
            ;
        }
    });

    $('input[type=text]').on('input', function(e) {
        let length = e.target.value.length;
        let maxLength = e.target.getAttribute('length');
        let element = $(e.target).closest('.md-form').find('.help-size:first')

        if (!element || 0 === element.length) {
            return;
        }
        element = $(element[0]);
        element.text(length+'/'+maxLength);
        if (length > maxLength) {
            element.addClass('red-text').removeClass('dark-grey-text')
        } else {
            element.removeClass('red-text').addClass('dark-grey-text')
        }
    });

    $('.sub-menu-button').on('click', function (e) {
        let element = $(e.target);
        let icons = $(e.target).find('i.fas');
        let icon = $(icons[0]) || null;
        let parent = element.closest('.nav-item');
        let menus = $(parent).find('.sub-menu');
        if (menus[0] === undefined) {
            return;
        }


        let item = $(menus[0]);
        if (item.hasClass('sub-menu-hidden')) {
            item
                .removeClass('sub-menu-hidden')
                .addClass('sub-menu-show');
            if (icon) {
                icon
                    .removeClass('fa-sort-up')
                    .addClass('fa-sort-down');
            }
        } else {
            item
                .addClass('sub-menu-hidden')
                .removeClass('sub-menu-show');
            if (icon) {
                icon
                    .removeClass('fa-sort-down')
                    .addClass('fa-sort-up');
            }
        }

    })

})()