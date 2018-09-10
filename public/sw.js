const LOADING = 0;
const LOADED = 1;
const FAILED = 2;

this.addEventListener('fetch', function (event) {
    if (event.request.mode === 'navigate') {
        // do a fetch, bug also emit the loading state
        event.respondWith(fetchAndEmit(event.request));
    } else {
        event.respondWith(fetch(event.request));
    }
});

function fetchAndEmit(request) {
    // first get all the `window` objects that use this
    // service worker, then send state messages to it.
    return clients.matchAll({
        type: 'window'
    }).then(function (clients) {
        // attempt to find the client that initiated the
        // request based on whether it's in focus
        //const activeClients = clients.filter( function(client) { return client.focused === true} );

        // note that with a navigate operation, there's two
        // clients, kind of like a double buffer effect, the
        // one that initiated the request, and the one that
        // will swap in once the content is parsed.
        clients.forEach(postMessage(LOADING));

        // now fetch the request, but before sending it back
        // in the promise, send the appropriate loading state
        return fetch(request).then(function(res) {
            // res.status is a weird case that's more likely
            // to throw in the future. It's where the user
            // cancelled the request,
            if (res.status === 0) {
                clients.forEach(postMessage(FAILED));
            } else {
                clients.forEach(postMessage(LOADED));
            }
            return res;
        }).catch(function(e) {
            // this could be a network timeout
            clients.forEach(postMessage(FAILED));

            // 204: No Content (to prevent page load)
            return new Response(null, { status: 204 });
        });
    });
}

// postMessage returns a function that posts
// to the given client (useful for .forEach)
function postMessage(message) {
    return function (client) { client.postMessage(message) };
}
