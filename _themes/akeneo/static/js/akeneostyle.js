var menus = [
    'Documentation',
    'Technical'
];

$(function() {
    // Updates main menu elements
    $('#navbar-nav > ul').each(function (i, mainMenu) {
        mainMenu = $(mainMenu);
        var result = $('<li class="dropdown">');
        var resultItems = $('<ul class="dropdown-menu"><li class="dropdown-title">' + menus[i] + '</li></ul>');
        if (mainMenu.hasClass('current')) {
            result.addClass('active');
        }
        result.append('<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' + menus[i] + '<span class="caret"></span></a>');
        mainMenu.find('> li > a').each(function (j, subElement) {
            resultItems.append('<li><a href="' + $(subElement).attr('href') + '">' + $(subElement).text() + '</a></li>');
        });
        result.append(resultItems);

        $('.nav.navbar-nav.navbar-right').append(result);
    });

    // Updates title
    $('h1:first').html($('#navbar-nav > ul.current > li.current > a').text());

    // Drops menu elements
    $('#navbar-nav > ul:not(.current)').remove();
    $('#navbar-nav > ul > li:not(.current)').remove();
    $('#navbar-nav > ul > li > ul').appendTo($('#navbar-nav'));
    $('#navbar-nav > ul:first-child').remove();

    // Updates style
    $('#navbar-nav ul').addClass('nav');
    $('#navbar-nav > ul').addClass('nav-stacked');
    $('#navbar-nav li.current').addClass('active');

    // Updates anchors
    $('h1, h2, h3, h4, h5, h6').each(function (i, title) {
        var anchorLink = $(title).find('.headerlink');
        if (anchorLink) {
            anchorLink.prependTo($(title)).addClass('markdownIt-Anchor').html('#');
            var href = anchorLink.attr('href');
            var section = $(href);
            var id = section.attr('id');
            section.removeAttr('id');
            $('<a class="anchor" id="' + id + '"></a>').insertBefore(section);
        }
    })

    // Update notes
    $('.admonition .admonition-title').remove();
    $('.admonition.note').addClass('alert alert-info');
    $('.admonition.warning').addClass('alert alert-warning');

    // Update tables
    $('.docutils').addClass('table');
    $('.docutils').removeAttr('border');

    // Update code
    $('.linenos').remove();
    $('.highlight pre').addClass('hljs');
    $('.s, .s1').addClass('hljs-string');
    $('.cm, .c1, .sd').addClass('hljs-comment');
    $('.k, .kd, .nt').addClass('hljs-keyword');
    $('.l').addClass('hljs-literal');
    $('.p, .o').addClass(''); // Punctuation
    $('.nx, .nc').addClass('hljs-variable');

    $('.highlight-yaml, .highlight-php, .highlight-bash').each(function (i, block) {
        $(block).find('.highlight').appendTo($(block));
        $(block).find('.highlighttable').remove();
    });

    // Grow content
    if ($('#navbar-nav').children().length === 0) {
        $('#navbar').remove();
        $('.container h1:first').remove();
        $('.col-xs-12.col-sm-9').removeClass('col-sm-9').addClass('col-sm-12')
    }

    // Set image classes
    $('.container img').addClass('img-responsive');

    // Show result
    $('#mainContent').show();
});
