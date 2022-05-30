const shadow = visible => {
    let shadowParam = visible ? 'block' : 'none';
    let windowsParam = visible ? 'flex' : 'none';
    document.getElementById('shadow').style.display = shadowParam;
    document.getElementById('windows').style.display = windowsParam;
}



const openPage = page => {

    const pages = [
        ['employees', 'Сотрудники'],
        ['services', 'Услуги'],
        ['contracts', 'Договоры'],
        ['deals', 'Сделки'],
        ['apartments', 'Недвижимость'],
        ['clients', 'Клиенты'],
        ['admin', 'Администрирование']
    ];

    // change aside button style
    const aside = document.querySelectorAll('aside')[0];
    const ul = aside.querySelectorAll('ul');
    for(i in pages) {
        let elem = pages[i][0] == 'admin'
            ? ul[1].querySelector('li')
            : ul[0].querySelectorAll('li')[i];

        if(page == pages[i][0]) elem.classList.add('aside-li_focused');
        else elem.classList.remove('aside-li_focused');
    }

    // hide all pages
    new Promise((resolve, reject) => {

        for(pageSet of pages) {
            let pageId = pageSet[0];
            document.getElementById('header-title').style.opacity = 0;
            document.getElementById(pageId).style.opacity = 0;
            setTimeout(() => {
                document.getElementById(pageId).style.display = 'none';
            }, 200);
        }

        setTimeout(() => {
            resolve();
        }, 220)

    })

    .then(() => {

        // change title
        document.getElementById('header-title').innerHTML = pages.find(elem => elem[0] == page)[1];
        document.getElementById('header-title').style.opacity = 1;

        // show page
        document.getElementById(page).style.display = 'block';
        document.getElementById(page).style.opacity = 1;

    })

}