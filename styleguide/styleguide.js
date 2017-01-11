function setAnchor(group) {
    window.location.hash = group;
    toggleModules();
}

function toggleModules() {
    var group = window.location.hash;
    if ('' !== group) {
        group = group.substring(1);
        var module = '';
        var index = group.indexOf('-');
        if (index > -1) {
            module = group.substring(index + 1);
            group = group.substring(0, index);
        }
        toggleBlocks(group, module);
        toggleMenu(group, module);
        scroll(0,0);
    }
}

function toggleBlocks(group, module) {
    var elements = document.querySelectorAll('.AknStyleGuide-element[data-group]');
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].getAttribute('data-group') === group) {
            if ('' !== module) {
                if (elements[i].getAttribute('data-module') === module) {
                    elements[i].style.display = 'block';
                } else {
                    elements[i].style.display = 'none'
                }
            } else {
                elements[i].style.display = 'block';
            }
        } else {
            elements[i].style.display = 'none';
        }
    }
}

function toggleMenu(group, module) {
    var items = document.querySelectorAll('.AknStyleGuide-menuItem[data-group]');
    for (var i = 0; i < items.length; i++) {
        if (items[i].getAttribute('data-group') === group) {
            items[i].className = 'AknStyleGuide-menuItem AknStyleGuide-menuItem--current current reference internal';
            var subItems = items[i].querySelectorAll('.AknStyleGuide-subMenuItem[data-module]');
            console.log(subItems.length);
            for (var j = 0; j < subItems.length; j++) {
                if (subItems[j].getAttribute('data-module') === module)  {
                    subItems[j].className = 'AknStyleGuide-subMenuItem toctree-l2 current';
                } else {
                    subItems[j].className = 'AknStyleGuide-subMenuItem toctree-l2';
                }
            }
        } else {
            items[i].className = 'AknStyleGuide-menuItem';
        }
    }
}

function toggleCode(code) {
    var codeNode = document.querySelector('pre[data-code="' + code + '"]');
    var codeButton = document.querySelector('a[data-code="' + code + '"]');
    if (codeNode.style.display === 'block') {
        codeNode.style.display = 'none';
        codeButton.innerHTML = 'Show code <span class="fa fa-arrow-circle-down"></span>';
    } else {
        codeNode.style.display = 'block';
        codeButton.innerHTML = 'Hide code <span class="fa fa-arrow-circle-up"></span>';
    }
}

window.onload = toggleModules;
