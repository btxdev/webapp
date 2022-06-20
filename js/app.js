// main
addEventListener('DOMContentLoaded', () => {
  document.querySelector('#windows').addEventListener('click', (event) => {
    if (event.target.id == 'windows') {
      closePopup();
    }
  });

  //   getBasicInfo();
  //   updateCategories();
  //   updateItems();
  //   updateUsers();
  //   updateOrders();

  updateEmployees();
  updatePositions();
  updateEmployeeFormOptions();

  openPage('employees');
});

const POPUP_TIME = 500;

const openPage = (page) => {
  const pages = [
    ['employees', 'Сотрудники'],
    ['services', 'Услуги'],
    ['contracts', 'Договоры'],
    ['deals', 'Сделки'],
    ['apartments', 'Недвижимость'],
    ['clients', 'Клиенты'],
    ['positions', 'Должности'],
  ];

  // change aside button style
  const aside = document.querySelectorAll('aside')[0];
  const ul = aside.querySelectorAll('ul');
  for (let i in pages) {
    let elem =
      pages[i][0] == 'positions'
        ? ul[1].querySelector('li')
        : ul[0].querySelectorAll('li')[i];

    if (page == pages[i][0]) elem.classList.add('aside-li_focused');
    else elem.classList.remove('aside-li_focused');
  }

  // hide all pages
  new Promise((resolve, reject) => {
    for (let pageSet of pages) {
      let pageId = pageSet[0];
      document.getElementById('header-title').style.opacity = 0;
      document.getElementById(pageId).style.opacity = 0;
      setTimeout(() => {
        document.getElementById(pageId).style.display = 'none';
      }, 200);
    }

    setTimeout(() => {
      resolve();
    }, 220);
  }).then(() => {
    // change title
    document.getElementById('header-title').innerHTML = pages.find(
      (elem) => elem[0] == page
    )[1];
    document.getElementById('header-title').style.opacity = 1;

    // show page
    document.getElementById(page).style.display = 'block';
    document.getElementById(page).style.opacity = 1;
  });
};

const shadow = (visible) => {
  return new Promise((resolve, reject) => {
    let param = visible ? 'flex' : 'none';
    if (visible) {
      document.getElementById('windows').style.display = param;
      setTimeout(() => {
        document.getElementById('windows').style.opacity = 1;
      }, 10);
      setTimeout(() => {
        resolve();
      }, POPUP_TIME);
    } else {
      document.getElementById('windows').style.opacity = 0;
      setTimeout(() => {
        document.getElementById('windows').style.display = param;
        resolve();
      }, POPUP_TIME);
    }
  });
};

const popup = (id, visible) => {
  return new Promise((resolve, reject) => {
    let param = visible ? 'block' : 'none';
    if (visible) {
      document.getElementById(id).style.display = param;
      setTimeout(() => {
        document.getElementById(id).style.opacity = 1;
      }, 10);
      setTimeout(() => {
        resolve();
      }, POPUP_TIME);
    } else {
      document.getElementById(id).style.opacity = 0;
      setTimeout(() => {
        document.getElementById(id).style.display = param;
        resolve();
      }, POPUP_TIME);
    }
  });
};

let activePopup;

const openPopup = (id) => {
  activePopup = id;
  let p1 = shadow(true);
  let p2 = popup(id, true);
  return Promise.all([p1, p2]);
};

const closePopup = (id = activePopup) => {
  let p1 = shadow(false);
  let p2 = popup(id, false);
  return Promise.all([p1, p2]);
};

// addEventListener('DOMContentLoaded', () => {
//   document.querySelector('#windows').addEventListener('click', (event) => {
//     if (event.target.id == 'windows') {
//       closePopup();
//     }
//   });
// });

function reload() {
  document.location.reload();
}

function logout() {
  fetch('php/auth.php', {
    method: 'POST',
    cache: 'no-cache',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      op: 'logout',
    }),
  })
    // .then(response => response.text())
    // .then(dataStr => {
    //     console.log(dataStr);
    // })
    .then((response) => response.json())
    .then((data) => {
      if (data['status'] == 'OK') reload();
      else {
        console.log(data);
        alert(data['msg']);
      }
    });
}

const HEADERS = {
  Accept: 'application/json',
  'Content-Type': 'application/json',
};

function sendData(args) {
  return new Promise((res) => {
    const method = 'method' in args ? args['method'] : 'POST';
    const headers = 'headers' in args ? args['headers'] : HEADERS;
    const body = JSON.stringify(args['body']);
    const thenFunc = 'then' in args ? args['then'] : () => {};
    const catchFunc = 'catch' in args ? args['catch'] : () => {};
    fetch('php/app.php', {
      method: method,
      headers: headers,
      body: body,
    })
      .then((response) => response.text())
      .then((body) => {
        console.log('raw response data:');
        console.log(body);
        try {
          return JSON.parse(body);
        } catch {
          throw Error(body);
        }
      })
      .then((data) => {
        console.log('JSON response data:');
        console.log(data);
        if (data['status'] == 'OK') {
          thenFunc(data);
        }
        res(data);
      })
      .catch(catchFunc);
  });
}

function removeEventListenersFrom(element) {
  let clone = element.cloneNode(true);
  element.parentNode.replaceChild(clone, element);
}

// изменение данных формы во всплывающем окне
function replacePopupData(type, id) {
  switch (type) {
    case 'positions':
      sendData({
        body: {
          op: 'get_position_data',
          position_id: id,
        },
      }).then((serverResponse) => {
        const position = serverResponse['position']['position'];
        const $input = document.getElementById('position-edit-input');
        let $button = document.getElementById('position-edit-btn');
        $input.value = position;
        removeEventListenersFrom($button);
        $button = document.getElementById('position-edit-btn');
        $button.addEventListener('click', (e) => {
          editPositionForm(id);
        });
      });
      break;
    case 'employees':
      break;
  }
}

// сотрудники

function updateEmployees() {
  const $container = document.getElementById('employees-container');
  sendData({
    body: {
      op: 'get_employees',
    },
  }).then((serverData) => {
    const employees = serverData['msg'];
    let content = '';
    if (employees == false) {
      content = '<h1 style="margin-left: 50px;">Нет пользователей</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
        <td style="width: 20px"><div class="title">#</div></td>
        <td><div class="title">Имя</div></td>
        <td><div class="title">Фамилия</div></td>
        <td><div class="title">Отчество</div></td>
        <td><div class="title">Должность</div></td>
        <td style="width: 160px"><div class="title"></div></td>
        </tr>
    `;
      for (id in employees) {
        const item = employees[id];
        content += `
            <tr>
            <td style="width: 20px"><div class="field">${item['id']}</div></td>
            <td><div class="field">${item['first_name']}</div></td>
            <td><div class="field">${item['second_name']}</div></td>
            <td><div class="field">${item['patronymic']}</div></td>
            <td><div class="field">${item['position']}</div></td>
            <td style="width: 160px">
                <button onclick="openPopup('popup-employee-edit'); replacePopupData('employees', '${item['id']}');">Подробнее</button>
            </td>
            </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });
}

function addEmployee(
  first_name,
  second_name,
  patronymic,
  role_id,
  position_id,
  birth_date,
  phone,
  email,
  username,
  password
) {
  console.log(title, description, link, price, count);
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_employee',
        first_name: first_name,
        second_name: second_name,
        patronymic: patronymic,
        role_id: role_id,
        position_id: position_id,
        birth_date: birth_date,
        phone: phone,
        email: email,
        username: username,
        password: password,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function updateEmployeeFormOptions() {
  const $addSelectPosition = document.getElementById(
    'popup-employee-add__select-position'
  );
  const $addSelectRole = document.getElementById(
    'popup-employee-add__select-role'
  );
  const $editSelectPosition = document.getElementById(
    'popup-employee-edit__select-position'
  );
  const $editSelectRole = document.getElementById(
    'popup-employee-edit__select-role'
  );
  sendData({
    body: {
      op: 'get_employee_options',
    },
  }).then((serverData) => {
    console.log('options:');
    console.log(serverData);
    const positions = serverData['response']['positions'];
    const roles = serverData['response']['roles'];
    $addSelectPosition.innerHTML = '';
    for (let position of positions) {
      let option = document.createElement('option');
      option.value = position['position_id'];
      option.text = position['position'];
      $addSelectPosition.appendChild(option);
    }
    $addSelectRole.innerHTML = '';
    for (let role of roles) {
      let option = document.createElement('option');
      option.value = role['role_id'];
      option.text = role['role'];
      $addSelectRole.appendChild(option);
    }
  });
}

function addEmployeeForm() {
  const $selectPosition = document.getElementById(
    'popup-employee-add__select-position'
  );
  const $selectRole = document.getElementById(
    'popup-employee-add__select-role'
  );

  // const $employee = document.getElementById('position-add-input');
  // const value = $position.value;
  // addPosition(value)
  //   .then(() => {
  //     closePopup();
  //     updatePositions();
  //   })
  //   .catch((err) => {
  //     console.log(err);
  //   });
}

// должности

function updatePositions() {
  const $container = document.getElementById('positions-container');
  sendData({
    body: {
      op: 'get_positions',
    },
  }).then(({ positions }) => {
    //const positions = serverData['positions'];
    let content = '';
    if (positions == false) {
      content = '<h1 style="margin-left: 50px;">Нет должностей</h1>';
    } else {
      content += `
        <tr class="employees-table__title-row">
        <td style="width: 20px">
            <div class="title">#</div>
        </td>
        <td>
            <div class="title">Должность</div>
        </td>
        <td style="width: 160px">
            <div class="title"></div>
        </td>
        <td style="width: 100px">
            <div class="title"></div>
        </td>
        </tr>
    `;
      for (item of positions) {
        content += `
            <tr>
            <td style="width: 20px">
                <div class="field">${item['position_id']}</div>
            </td>
            <td class="td-wide">
                <div class="field">${item['position']}</div>
            </td>
            <td style="width: 160px">
                <button onclick="openPopup('popup-position-edit'); replacePopupData('positions', '${item['position_id']}');">
                Редактировать
                </button>
            </td>
            <td style="width: 100px">
                <button class="table-btn__remove" onclick="removePosition('${item['position_id']}');">Удалить</button>
            </td>
            </tr>
        `;
      }
    }
    $container.innerHTML = content;
  });
}

function addPosition(position) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'add_position',
        position: position,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function addPositionForm() {
  const $position = document.getElementById('position-add-input');
  const value = $position.value;
  addPosition(value)
    .then(() => {
      closePopup();
      updatePositions();
    })
    .catch((err) => {
      console.log(err);
    });
}

function editPosition(id, position) {
  return new Promise((resolve, reject) => {
    sendData({
      body: {
        op: 'edit_position',
        position_id: id,
        position: position,
      },
    })
      .then(() => {
        resolve();
      })
      .catch(() => {
        reject();
      });
  });
}

function editPositionForm(id) {
  const $position = document.getElementById('position-edit-input');
  const value = $position.value;
  editPosition(id, value)
    .then(() => {
      closePopup();
      updatePositions();
    })
    .catch((err) => {
      console.log(err);
    });
}

function removePosition(id) {
  sendData({
    body: {
      op: 'remove_position',
      position_id: id,
    },
  }).then(() => {
    updatePositions();
  });
}
