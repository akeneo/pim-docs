var getJSON = function(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function() {
        var status = xhr.status;
        if (status === 200) {
            callback(null, xhr.response);
        } else {
            callback(status, xhr.response);
        }
    };
    xhr.send();
};

function updateVersions() {
    getJSON('/versions.json',
        function (err, data) {
            if (err === null) {
                var versionsDom = document.getElementById('versions');
                versionsDom.innerHTML = '<li class="dropdown-title">Versions</li>';
                data.forEach(function (version) {
                    var node = document.createElement('li');
                    var link = document.createElement('a');
                    link.href = version.url;
                    link.innerHTML = version.label;
                    node.appendChild(link);
                    versionsDom.appendChild(node);
                });
            }
        });
}
