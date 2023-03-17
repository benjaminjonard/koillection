window.onload = window.onpageshow = function() {
    document.documentElement.classList.remove('loading');
};

//Service Worker
if ('serviceWorker' in navigator) {
    const LOADING = 0;
    const LOADED = 1;
    const FAILED = 2;
    navigator.serviceWorker.register('/sw.js', { scope: "/" });
    navigator.serviceWorker.addEventListener('message', function (event) {
        if (event.data === LOADING) {
            document.documentElement.classList.add('loading');
        }
        if (event.data === LOADED) {
            document.documentElement.classList.remove('loading');
        }
        if (event.data === FAILED) {
            document.documentElement.classList.remove('loading');
        }
    });
}

// Fetch
let _oldFetch = fetch;
window.fetch = function(){
    let fetchStart = new Event( 'fetchStart', { 'view': document, 'bubbles': true, 'cancelable': false } );
    let fetchEnd = new Event( 'fetchEnd', { 'view': document, 'bubbles': true, 'cancelable': false } );
    let fetchCall = _oldFetch.apply(this, arguments);

    document.dispatchEvent(fetchStart);

    fetchCall.then(function(){
        document.dispatchEvent(fetchEnd);
    }).catch(function(){
        document.dispatchEvent(fetchEnd);
    });

    return fetchCall;
};

document.addEventListener('fetchStart', function() {
    document.documentElement.classList.add('loading');
});

document.addEventListener('fetchEnd', function() {
    document.documentElement.classList.remove('loading');
});