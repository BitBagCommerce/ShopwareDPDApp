const checkCredentialsEl = document.getElementById('check-credentials');
const checkApiCredentialsTarget = document.getElementById('check-api-credentials-target');
const checkApiCredentialsResponseLabel = document.getElementById('check-api-credentials-response-label');
const checkApiCredentialsResponseMessage = document.getElementById('check-api-credentials-response-message');
const authorizedCode = 200;
const notAuthorizedCode = 401;
const typeError = 'error';
const typeSuccess = 'success';

checkCredentialsEl.addEventListener('click', (e) => {
    e.preventDefault();

    checkApiCredentialsTarget.classList.add('d-none');
    checkApiCredentialsTarget.classList.remove('error-message');
    checkApiCredentialsTarget.classList.remove('success-message');

    const searchParams = new URLSearchParams(window.location.search);

    const data = new FormData(document.querySelector('form'));

    const fetchOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            shopId: searchParams.get('shop-id'),
            formData: Object.fromEntries(data.entries()),
            language: searchParams.get('sw-user-language')
        })
    };

    fetch('/app/module/api-check-credentials', fetchOptions)
        .then(result => {
            let type = null;

            result.json().then(response => {
                if (authorizedCode === result.status) {
                    type = typeSuccess;
                } else if (notAuthorizedCode === result.status) {
                    type = typeError;
                }

                checkApiCredentialsTarget.classList.remove('d-none');

                checkApiCredentialsTarget.classList.add(type === typeSuccess ? 'success-message' : 'error-message');

                checkApiCredentialsResponseLabel.textContent = response.label;
                checkApiCredentialsResponseMessage.textContent = response.message;
            });
        });
});
