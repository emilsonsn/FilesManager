import './bootstrap';

function showLoader() {
    document.getElementById('global-loader').style.display = 'flex'
}

function hideLoader() {
    document.getElementById('global-loader').style.display = 'none'
}

const form = document.querySelectorAll('form')[1];

form.addEventListener('submit', function() {
    showLoader();
})
