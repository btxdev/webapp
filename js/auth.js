function reload() {
    document.location.reload();
}

function loginForm() {
    let login = document.querySelector('#login');
    let password = document.querySelector('#password');
    fetch('php/auth.php', {
        method: 'POST',
        cache: 'no-cache',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            op: 'login',
            login: login,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data['msg'] == 'OK') reload();
        else alert(data);
    })
}