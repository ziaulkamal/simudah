// assets/js/globalsign.js

/**
 * Global API request helper dengan signature session
 */
window.apiRequest = async function(url, method = 'GET', body = null) {
    const signature = localStorage.getItem('signature_session');
    const timestamp = Math.floor(Date.now() / 1000);

    const headers = new Headers({
        'Accept': 'application/json',
        'X-App-Signature': signature,
        'X-App-Timestamp': timestamp,
    });

    if (body) {
        headers.append('Content-Type', 'application/json');
    }

    const options = {
        method: method,
        headers: headers,
        credentials: 'same-origin', // kirim cookie/session
    };

    if (body) {
        options.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            let errorData = {};
            try { errorData = await response.json(); } catch(e){}
            throw { status: response.status, data: errorData };
        }
        return await response.json();
    } catch (err) {
        console.error('API Request Error:', err);
        throw err;
    }
};

/**
 * Helper untuk set signature session setelah login
 */
window.setSignatureSession = function(signature) {
    localStorage.setItem('signature_session', signature);
};

/**
 * Helper untuk hapus signature session saat logout
 */
window.clearSignatureSession = function() {
    localStorage.removeItem('signature_session');
};
