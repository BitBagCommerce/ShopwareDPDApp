const salesChannelIdSelectEl = document.getElementById('salesChannelId');
const apiLoginEl = document.getElementById('apiLogin');
const apiPasswordEl = document.getElementById('apiPassword');
const apiFidEl = document.getElementById('apiFid');
const apiEnvironmentEl = document.getElementById('apiEnvironment');
const senderFirstLastNameEl = document.getElementById('senderFirstLastName');
const senderStreetEl = document.getElementById('senderStreet');
const senderZipCodeEl = document.getElementById('senderZipCode');
const senderCityEl = document.getElementById('senderCity');
const senderPhoneNumberEl = document.getElementById('senderPhoneNumber');
const senderLocaleEl = document.getElementById('senderLocale');

salesChannelIdSelectEl.addEventListener('change', (e) => {
    const value = e.target.value;

    const searchParams = new URLSearchParams(window.location.search);

    const urlParams = {
        shopId: searchParams.get('shop-id'),
        salesChannelId: value,
        language: searchParams.get('sw-user-language'),
    };

    const fetchOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    };

    const urlSearchParams = new URLSearchParams(urlParams).toString();

    fetch('/app/config?' + urlSearchParams, fetchOptions)
        .then(result => {
            result.json().then(response => {
                apiLoginEl.value = response.apiLogin;
                apiPasswordEl.value = response.apiPassword;
                apiFidEl.value = response.apiFid;
                apiEnvironmentEl.value = response.apiEnvironment;

                senderFirstLastNameEl.value = response.senderFirstLastName;
                senderStreetEl.value = response.senderStreet;
                senderZipCodeEl.value = response.senderZipCode;
                senderCityEl.value = response.senderCity;
                senderPhoneNumberEl.value = response.senderPhoneNumber;
                senderLocaleEl.value = response.senderLocale;
            });
        });
});
