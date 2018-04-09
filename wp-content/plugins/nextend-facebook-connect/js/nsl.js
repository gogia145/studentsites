(function (undefined) {
    window.NSLPopupCenter = function (url, title, w, h) {
        var userAgent = navigator.userAgent,
            mobile = function () {
                return /\b(iPhone|iP[ao]d)/.test(userAgent) ||
                    /\b(iP[ao]d)/.test(userAgent) ||
                    /Android/i.test(userAgent) ||
                    /Mobile/i.test(userAgent);
            },
            screenX = window.screenX !== undefined ? window.screenX : window.screenLeft,
            screenY = window.screenY !== undefined ? window.screenY : window.screenTop,
            outerWidth = window.outerWidth !== undefined ? window.outerWidth : document.documentElement.clientWidth,
            outerHeight = window.outerHeight !== undefined ? window.outerHeight : document.documentElement.clientHeight - 22,
            targetWidth = mobile() ? null : w,
            targetHeight = mobile() ? null : h,
            V = screenX < 0 ? window.screen.width + screenX : screenX,
            left = parseInt(V + (outerWidth - targetWidth) / 2, 10),
            right = parseInt(screenY + (outerHeight - targetHeight) / 2.5, 10),
            features = [];
        if (targetWidth !== null) {
            features.push('width=' + targetWidth);
        }
        if (targetHeight !== null) {
            features.push('height=' + targetHeight);
        }
        features.push('left=' + left);
        features.push('top=' + right);
        features.push('scrollbars=1');

        var newWindow = window.open(url, title, features.join(','));

        if (window.focus) {
            newWindow.focus();
        }

        return newWindow;
    };

    if (jQuery !== undefined) {
        (function ($) {
            $('a[data-plugin="nsl"][data-action="connect"],a[data-plugin="nsl"][data-action="link"]').on('click', function (e) {
                var $target = $(this),
                    href = $target.attr('href');
                if (href.indexOf('?') !== -1) {
                    href += '&';
                } else {
                    href += '?';
                }
                var redirectTo = $target.data('redirect');
                if (redirectTo === 'current') {
                    href += 'redirect=' + encodeURIComponent(window.location.href) + '&';
                } else if (redirectTo && redirectTo !== '') {
                    href += 'redirect=' + encodeURIComponent(redirectTo) + '&';
                }

                if (NSLPopupCenter(href + 'display=popup', 'nsl-social-connect', $target.data('popupwidth'), $target.data('popupheight'))) {
                    e.preventDefault();
                } else {
                    window.location = href;
                    e.preventDefault();
                }
            });
        })(jQuery);
    }
})();